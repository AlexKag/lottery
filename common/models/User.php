<?php

namespace common\models;

use common\commands\AddToTimelineCommand;
use common\models\query\UserQuery;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use common\models\UserReferralsStat;
use borales\extensions\phoneInput\PhoneInputBehavior;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\UserAccountStat;

//use frontend\modules\user\models\SignupForm;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $email
 * @property string $phone
 * @property string $auth_key
 * @property string $access_token
 * @property string $oauth_client
 * @property string $oauth_client_user_id
 * @property string $publicIdentity
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 * @property string $password write-only password
 *
 * @property \common\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface
{

    const STATUS_NOT_ACTIVE    = 1;
    const STATUS_ACTIVE        = 2;
    const STATUS_DELETED       = 3;
    const ROLE_USER            = 'user';
    const ROLE_MANAGER         = 'manager';
    const ROLE_ADMINISTRATOR   = 'administrator';
    const ROLE_USER_WITH_PHONE = 'user_with_phone';
    const ROLE_USER_WITH_DOCS  = 'user_with_docs';
    const ROLE_USER_LEADER     = 'user_leader';
    const EVENT_AFTER_SIGNUP   = 'afterSignup';
    const EVENT_AFTER_LOGIN    = 'afterLogin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            'auth_key'     => [
                'class'      => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key'
                ],
                'value'      => Yii::$app->getSecurity()->generateRandomString()
            ],
            //TODO Создать событие, так как иначе genRefId вызывается при создании каждого экземпляра класса
            'referral_id'  => [
                'class'      => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'referral_id'
                ],
                'value'      => static::genRefId(),
            ],
//            'referrer_id' => [
//                'class' => AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => 'ref'
//                ],
//                'value' => static::validateRefId(Yii::$app->request->get('ref', null)),
////                'value'      => Yii::$app->getSecurity()->generateRandomString(Yii::$app->params['referralStringLength'])
//            ],
            'access_token' => [
                'class'      => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token'
                ],
                'value'      => function () {
            return Yii::$app->getSecurity()->generateRandomString(40);
        }
            ],
//            'access_token' => [
//                'class'      => AttributeBehavior::className(),
//                'attributes' => [
//                    ActiveRecord::EVENT_BEFORE_INSERT => 'ga_token'
//                ],
//                'value'      => function () {
//            return Yii::$app->googleAuth->generateSecret();
//        }
//            ],
            'PhoneInput' => [
                'class'          => PhoneInputBehavior::className(),
                'phoneAttribute' => 'phone',
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return ArrayHelper::merge(
                        parent::scenarios(), [
                    'oauth_create' => [
                        'oauth_client', 'oauth_client_user_id', 'email', 'username', '!status'
                    ]
                        ]
        );
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'unique'],
            ['username', 'string', 'min' => 1, 'max' => 32],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVE],
            ['status', 'in', 'range' => array_keys(self::statuses())],
            [['username'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            [['referrer_id', 'referral_id'], 'string'], //TODO validation referrer_id -> validateRefId()
            ['referrer_id', 'exist', 'targetAttribute' => 'referral_id'],
            ['referral_id', 'unique'],
            [['phone'], 'string', 'max' => 30],
            ['phone', 'unique'],
            [['phone'], PhoneInputValidator::className()],
            ['ip', 'ip', 'ipv6' => false],
            ['email', 'email'],
            ['email', 'unique'],
            ['ga_token', 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username'     => Yii::t('common', 'Username'),
            'email'        => Yii::t('common', 'Email'),
            'status'       => Yii::t('common', 'Status'),
            'access_token' => Yii::t('common', 'API access token'),
            'created_at'   => Yii::t('common', 'Created at'),
            'updated_at'   => Yii::t('common', 'Updated at'),
            'logged_at'    => Yii::t('common', 'Last login'),
            'phone'        => Yii::t('common', 'Phone'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
                        ->active()
                        ->andWhere(['id' => $id])
                        ->one();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
                        ->active()
                        ->andWhere(['access_token' => $token, 'status' => self::STATUS_ACTIVE])
                        ->one();
    }

    /**
     * Find user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
                        ->active()
                        ->andWhere(['username' => $username, 'status' => self::STATUS_ACTIVE])
                        ->one();
    }

    /**
     * Finds user by username or email
     *
     * @param string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
                        ->active()
                        ->andWhere([
                            'and',
                            ['or', ['username' => $login], ['email' => $login]],
                            'status' => self::STATUS_ACTIVE
                        ])
                        ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Yii::$app->user->identity->referrer
     * @return User
     */
    public function getReferrer()
    {
        return $this->hasOne(static::className(), ['referral_id' => 'referrer_id']);
    }

    /**
     * Yii::$app->user->identity->referrals
     * @return User
     */
    public function getReferrals()
    {
        return $this->hasMany(static::className(), ['referrer_id' => 'referral_id']);
    }

    public function getLeader()
    {
        return $this->hasOne(static::className(), ['referral_id' => 'leader_id']);
    }

    public function getAccountStat()
    {
        return $this->hasMany(UserAccountStat::className(), ['user_id' => 'id']);
    }

    /**
     *
     * @param int $lotteryId Id лотереи
     * @param int $lotteryType Тип лотереи
     * @param currency $amount Оплачено за билет пользователем
     * @param currency $paidOut Полученный пользователем выигрыш
     * @param int $level Текущий уровень начислений
     * @param User $referrerUser Кому платить
     */
    public function payReferrer($lotteryId, $lotteryType, $amount, $paidOut, $level = 1, User $referrerUser = null)
    {
        $key          = sprintf('user.account.ref_rate_%d', $level);
        $refRate      = Yii::$app->keyStorage->get($key) / 100;
        $referrerUser = is_null($referrerUser) ? $this->referrer : $referrerUser->referrer;
        if (!is_null($refRate) && !empty($referrerUser)) {
            if ($refRate > 0) {
                $paidRef = $refRate * $amount;
                $referrerUser->userProfile->accountCharge($paidRef, 'Referral payment');
                $referrerUser->userProfile->save();
                $refStat = new UserReferralsStat([
                    'lottery_id'   => $lotteryId,
                    'lottery_type' => $lotteryType,
                    'user_id'      => $referrerUser->id,
                    'level'        => $level,
                    'ref_count'    => 1,
                    'paid_in'      => $amount,
                    'paid_out'     => $paidOut,
                    'paid_ref'     => $paidRef,
                ]);
                if (!$refStat->save()) {
                    Yii::error($refStat);
                }
            }
            $this->payReferrer($lotteryId, $lotteryType, $amount, $paidOut, ++$level, $referrerUser);
        }
    }

    public function payLeader($lotteryId, $lotteryType, $amount, $paidOut)
    {
        $leaderUser = $this->leader;
        if ($leaderUser instanceof User) {
            $refRate = Yii::$app->keyStorage->get('user.account.ref_rate_leader') / 100;
            $paidRef = $refRate * $amount;
            $leaderUser->userProfile->accountCharge($paidRef, 'Leader referral payment');
            $leaderUser->userProfile->save();
            $refStat = new UserReferralsStat([
                'lottery_id'   => $lotteryId,
                'lottery_type' => $lotteryType,
                'user_id'      => $leaderUser->id,
                'level'        => 0,
                'ref_count'    => 1,
                'paid_in'      => $amount,
                'paid_out'     => $paidOut,
                'paid_ref'     => $paidRef,
            ]);
            if (!$refStat->save()) {
                Yii::error($refStat);
            }
        }
    }

    /**
     * Поиск лидера по реферальному дереву
     * @return string
     */
    protected function getLeaderRefId()
    {
        if (empty($this->referrer_id) || !$this instanceof ActiveRecord) {
            return null;
        }
        $max_nested_level = Yii::$app->keyStorage->get('user.account.leader.max_nested_level');
        $referrer         = $this->referrer;
        for ($i = 1; $i < $max_nested_level; $i++) {
//            if (Yii::$app->user->can('getLeaderRefPayment', ['user' =>$referrer])) {
            if (Yii::$app->authManager->checkAccess($referrer->id, 'getLeaderRefPayment')) {
                return $referrer->referral_id;
            }
            $referrer = $referrer->referrer;
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    /**
     * Returns user statuses list
     * @return array|mixed
     */
    public static function statuses($code = null)
    {
        $codes = [
            self::STATUS_NOT_ACTIVE => Yii::t('backend', 'Not Active'),
            self::STATUS_ACTIVE     => Yii::t('backend', 'Active'),
            self::STATUS_DELETED    => Yii::t('backend', 'Disabled')
        ];
        if (!is_null($code) && isset($codes[$code])) {
            return $codes[$code];
        }
        else {
            return $codes;
        }
    }

    /**
     * Creates user profile and application event
     * @param array $profileData
     */
    public function afterSignup(array $profileData = [])
    {
        $this->refresh();
        Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'category' => 'user',
            'event'    => 'signup',
            'data'     => [
                'public_identity' => $this->getPublicIdentity(),
                'user_id'         => $this->getId(),
                'created_at'      => $this->created_at
            ]
        ]));
        $profile         = new UserProfile();
        $profile->locale = Yii::$app->language;
//        $profile->init_email = $this->email;
        $profile->load($profileData, '');
        $this->link('userProfile', $profile);
        $this->trigger(self::EVENT_AFTER_SIGNUP);
        // Default role
        $auth            = Yii::$app->authManager;
        //TODO Validate phone and than assign corresponding role
//        $role                = isset($profile->phone) ? self::ROLE_USER_WITH_PHONE : self::ROLE_USER;
        $role            = self::ROLE_USER;
        $auth->assign($auth->getRole($role), $this->getId());
    }

    /**
     * @return string
     */
    public function getPublicIdentity()
    {
        if ($this->userProfile && $this->userProfile->getFullname()) {
            return $this->userProfile->getFullname();
        }
        if ($this->username) {
            return $this->username;
        }
        if ($this->email) {
            return $this->email;
        }
        return $this->phone;
    }

    public function disable()
    {
        $this->status = self::STATUS_DELETED;
        return $this;
    }

    public function enable()
    {
        $this->status = self::STATUS_ACTIVE;
        return $this;
    }

    /**
     * Generate unique ref id
     * @return string
     */
    public static function genRefId()
    {
        $strip = function($str) {
            return str_replace(['-', '_'], '', $str);
        };
        $ref = $strip(Yii::$app->security->generateRandomString(Yii::$app->params['referralStringLength'] * 2));
//        $user = User::find()->where(['ref' => $ref])->one();
        //TODO Verify algoritm
        while (User::find()->where(['referrer_id' => $ref])->one() || strlen($ref) < Yii::$app->params['referralStringLength']) {
            $ref = $strip(Yii::$app->security->generateRandomString(Yii::$app->params['referralStringLength']));
        }
        return substr($ref, 0, Yii::$app->params['referralStringLength']);
    }

    /**
     *
     * @return string Url для регистрации рефералов
     */
    public function getRefUrl()
    {
        return Url::to(['/user/sign-in/signup', 'ref' => $this->referral_id], true);
    }

//    public static function validateRefId($ref)
//    {
//        if (!is_null($ref) && !is_null(User::find()->where(['referrer_id' => $ref])->one())) {
//            return $ref;
//        }
//        return null;
//    }

    /**
     * Проверка ip адреса
     * @param type $ip
     * @return type
     */
    public function isIPKnown($ip)
    {
        $knownIPs[] = $this->ip;
        if (!empty($this->userProfile->init_ip)) {
            $knownIPs[] = $this->userProfile->init_ip;
        }
        return in_array($ip, $knownIPs);
    }

    /*
     * Обновляет ip адрес пользователя
     */

    public function touchIP()
    {
        $ip = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userIP : null;
        if (!empty($ip)) {
            $this->ip = Yii::$app->request->userIP;
            $this->save();
        }
    }

}
