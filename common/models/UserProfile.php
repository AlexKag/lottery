<?php

namespace common\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use borales\extensions\phoneInput\PhoneInputValidator;
use borales\extensions\phoneInput\PhoneInputBehavior;
use yii\web\UnprocessableEntityHttpException;
use common\models\query\UserProfileQuery;
use common\base\PaymentEvent;
//use common\behaviors\AccountWithdrawLimiterBehavior;
use common\models\UserAccountStat;

/**
 * This is the model class for table "user_profile".
 *
 * @property integer $user_id
 * @property integer $account
 * @property integer $locale
 * @property string $firstname
 * @property string $middlename
 * @property string $lastname
 * @property string $picture
 * @property string $avatar
 * @property string $avatar_path
 * @property string $avatar_base_url
 * @property integer $gender
 * @property string $phone
 * @property string $init_email
 * @property string $init_ip
 * @property string $init_phone
 *
 * @property User $user
 */
class UserProfile extends ActiveRecord
{

    const GENDER_MALE                   = 1;
    const GENDER_FEMALE                 = 2;
    const EVENT_BEFORE_ACCOUNT_WITHDRAW = 'beforeAccountWithdraw';

    /**
     * @var
     */
    public $picture;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'picture' => [
                'class' => UploadBehavior::className(),
                'attribute' => 'picture',
                'pathAttribute' => 'avatar_path',
                'baseUrlAttribute' => 'avatar_base_url'
            ],
            'PhoneInput' => [
                'class' => PhoneInputBehavior::className(),
                'phoneAttribute' => 'init_phone',
            ],
//            'accountWithdrawLimiter' => [
//                'class' => AccountWithdrawLimiterBehavior::className(),
//            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_profile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'gender'], 'integer'],
            [['gender'], 'in', 'range' => [NULL, self::GENDER_FEMALE, self::GENDER_MALE]],
            [['firstname', 'middlename', 'lastname', 'avatar_path', 'avatar_base_url'], 'string', 'max' => 255],
            ['locale', 'default', 'value' => Yii::$app->language],
            ['locale', 'in', 'range' => array_keys(Yii::$app->params['availableLocales'])],
            ['picture', 'safe'],
            ['init_phone', 'string', 'max' => 30],
            ['init_phone', PhoneInputValidator::className()],
//            ['phone', 'unique'],
            ['init_email', 'email'],
            ['init_ip', 'ip', 'ipv6' => false],
//            ['account', 'default', 'value' => Yii::$app->keyStorage->get('user.account.default')],
            ['account', 'default', 'value' => 0],
            ['account', 'number', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('common', 'User ID'),
            'firstname' => Yii::t('common', 'Firstname'),
            'middlename' => Yii::t('common', 'Middlename'),
            'lastname' => Yii::t('common', 'Lastname'),
            'locale' => Yii::t('common', 'Locale'),
            'picture' => Yii::t('common', 'Picture'),
            'gender' => Yii::t('common', 'Gender'),
            'init_phone' => Yii::t('common', 'Initial Phone'),
            'init_ip' => Yii::t('common', 'Initial IP address'),
            'init_email' => Yii::t('common', 'Initial Email'),
        ];
    }

    /**
     * @return UserProfileQuery
     */
    public static function find()
    {
        return new UserProfileQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getAccountStat()
    {
        return $this->hasMany(UserAccountStat::className(), ['user_id' => 'user_id']);
    }

    /**
     * @return null|string
     */
    public function getFullName()
    {
        if ($this->firstname || $this->lastname) {
            return implode(' ', [$this->firstname, $this->lastname]);
        }
        return null;
    }

    /**
     * @param null $default
     * @return bool|null|string
     */
    public function getAvatar($default = null)
    {
        return $this->avatar_path ? Yii::getAlias($this->avatar_base_url . '/' . $this->avatar_path) : $default;
    }

    /**
     * User profile initial values getter
     * @param null $key
     * @return array|null
     */
//    public function getProfileInits($key = null) {
//        if (!is_array($this->profile_init)) {
//            try {
//                $this->profile_init = BaseJson::decode($this->profile_init, true);
//            } catch (yii\base\InvalidParamException $e) {
//                Yii::error($e);
//                $this->profile_init = [];
//            }
//        }
//        if (is_null($key)) {
//            return $this->profile_init;
//        }
//        elseif (!isset($this->profile_init[$key])) {
//            return null;
//        }
//        else {
//            return $this->profile_init;
//        }
//    }

    /**
     *
     * http://stackoverflow.com/questions/2754340/inet-aton-and-inet-ntoa-in-php
     * http://php.net/manual/en/function.inet-pton.php
     * @return type
     */
    public function getInitIp()
    {
        return inet_ntop($this->ip_init);
    }

    public function setInitIp($address)
    {
        $this->ip_init = inet_pton($address);
    }

    //Максимальная сумма, доступная для оплаты (зависит от уровня верификации)
    public function getPaidLimit()
    {
        return 100000;
    }

    public function get_Account()
    {
        return Yii::$app->formatter->asCurrency($this->account);
    }

    public function AccountWithdraw($amount, $reason = 'default', $withHandlingFee = false)
    {
        if ($withHandlingFee) {
            $handlingFee = Yii::$app->keyStorage->get('payment.handling_fee') / 100; //Комиссия за снятие средств
            $withdraw    = abs($amount) * (1 + $handlingFee);
        } else {
            //TODO Запись действия в лог
            $withdraw = abs($amount);
        }

//        $event = new PaymentEvent([
//            'amount' => $amount,
//            'reason' => $reason,
////            'withHandlingFee' => $withHandlingFee,
//            'withdraw' => $withdraw,
////            'userProfile' => $this,
//        ]);
//        $this->trigger(self::EVENT_BEFORE_ACCOUNT_WITHDRAW, $event);
//        if ($event->isValid && $this->account >= $withdraw) {
        if ($this->account >= $withdraw) {
            $this->account -= $withdraw;
            Yii::info(sprintf('Со счёта пользователя [%s] списана сумма [%01.2f]. Остаток на счету после списания: [%01.2f]. Событие: [%s].', $this->user_id, $withdraw, $this->account, $reason), 'account\withdraw');
            //TODO Писать списания/зачисления в отдельный лог
        } else {
            $message = sprintf('Со счёта пользователя [%s] запрошено списание суммы [%01.2f]. Недостаточно средств на счету или превышен лимит на вывод средств. Доступно [%01.2f]. Событие: [%s]', $this->user_id, $withdraw, $this->account, $reason);
            Yii::warning($message, 'account\noaction');
            throw new UnprocessableEntityHttpException($message);
        }
    }

    /**
     * 
     * @param type $amount
     * @param type $reason
     * @param type $withHandlingFeeBack Используется при отмене платежа
     */
    public function AccountCharge($amount, $reason = 'default', $withHandlingFeeBack = false)
    {
        if ($withHandlingFeeBack) {
            $handlingFee = Yii::$app->keyStorage->get('payment.handling_fee') / 100; //Комиссия за снятие средств
            $charge      = abs($amount) * (1 + $handlingFee);
        } else {
            //TODO Запись действия в лог
            $charge = abs($amount);
        }
        $this->account += $charge;
        Yii::info(sprintf('Счёт пользователя [%s] пополнен на сумму [%01.2f]. Остаток на счету после пополнения: [%01.2f]. Событие: [%s].', $this->user_id, $charge, $this->account, $reason), 'account\charge');
    }

}
