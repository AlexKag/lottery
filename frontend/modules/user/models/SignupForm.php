<?php

namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserToken;
use frontend\modules\user\Module;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;
use borales\extensions\phoneInput\PhoneInputValidator;
use borales\extensions\phoneInput\PhoneInputBehavior;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
//use yii\behaviors\AttributeBehavior;
use common\validators\StopListValidator;
use common\models\StopList;

/**
 * Signup form
 */
class SignupForm extends Model
{

    /**
     * @var
     */
    public $username;

    /**
     * @var
     */
    public $email;

    /**
     * @var
     */
    public $password;
    public $password_repeat;
    public $phone;
    public $confirm;
    public $reCaptcha;
    public $ref;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique',
                'targetClass' => '\common\models\User',
                'message'     => Yii::t('frontend', 'Логин занят.')
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', StopListValidator::className(), 'stoplistType' => StopList::TYPE_EMAIL, 'message' => 'E-mail запрещен для использования на сайте.'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message'     => Yii::t('frontend', 'E-mail занят.')
            ],
            [['password', 'password_repeat'], 'string'],
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
            ['confirm', 'required', 'message' => 'Подтвердите возраст и примите условия Договора-оферты.'],
            ['confirm', 'in', 'range' => [1], 'message' => 'Подтвердите возраст и примите условия Договора-оферты.'],
            [['phone'], 'string', 'max' => 40],
            [['phone'], PhoneInputValidator::className()],
            [
                'phone', 'unique',
                'targetClass' => '\common\models\UserProfile',
                'message'     => Yii::t('frontend', 'Номер телефона уже зарегистрирован.'),
            ],
            [['reCaptcha'], ReCaptchaValidator::className()],
            ['ref', 'string']
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'PhoneInput' => [
                'class'          => PhoneInputBehavior::className(),
                'phoneAttribute' => 'phone',
            ],
//            'referrer_id' => [
//                'class' => AttributeBehavior::className(),
//                'attributes' => [
//                    Model::EVENT_BEFORE_VALIDATE => 'ref'
//                ],
//                'value' => User::validateRefId(Yii::$app->request->getBodyParam('ref', null)),
////                'value'      => Yii::$app->getSecurity()->generateRandomString(Yii::$app->params['referralStringLength'])
//            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'        => Yii::t('frontend', 'Логин'),
            'email'           => Yii::t('frontend', 'E-mail'),
            'password'        => Yii::t('frontend', 'Пароль'),
            'password_repeat' => Yii::t('frontend', 'Повторите пароль'),
            'phone'           => Yii::t('frontend', 'Телефон'),
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
//        if ($this->validate() && $this->confirm) {
            $shouldBeActivated = $this->shouldBeActivated();
            $user              = new User();
            $user->username    = $this->username;
            $user->email       = $this->email;
            $user->phone       = $this->phone;
            $user->status      = $shouldBeActivated ? User::STATUS_NOT_ACTIVE : User::STATUS_ACTIVE;

//            $user->referrer_id = User::validateRefId($this->ref);
            $user->referrer_id = isset($this->ref) ? $this->ref : null;
//            $user->ref = User::validateRefId(Yii::$app->request->getBodyParam('ref', null));
            $user->setPassword($this->password);
            $user->leader_id = $user->leaderRefId;
            if (!$user->save()) {
                throw new Exception("Ошибка сохранения профиля пользователя.");
            };

            $profileData = [
                'phone'      => $this->phone,
                'account'    => Yii::$app->keyStorage->get('user.account.default'),
                'init_ip'    => Yii::$app->request->userIP,
                'init_phone' => $this->phone,
                'init_email' => $this->email,
            ];
            $user->afterSignup($profileData);
            if ($shouldBeActivated) {
                $token = UserToken::create(
                                $user->id, UserToken::TYPE_ACTIVATION, Time::SECONDS_IN_A_DAY
                );
                Yii::$app->commandBus->handle(new SendEmailCommand([
                    'subject' => Yii::t('frontend', Yii::$app->name . ' Активация учетной записи'),
                    'view'    => 'activation',
                    'to'      => $this->email,
                    'params'  => [
                        'url' => Url::to(['/user/sign-in/activation', 'token' => $token->token], true)
                    ]
                ]));
            }
            return $user;
//        }

        return null;
    }

    /**
     * @return bool
     */
    public function shouldBeActivated()
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');
        if (!$userModule) {
            return false;
        }
        elseif ($userModule->shouldBeActivated) {
            return true;
        }
        else {
            return false;
        }
    }



}
