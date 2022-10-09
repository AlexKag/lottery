<?php

namespace frontend\modules\user\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\UserToken;
use Yii;
use common\models\User;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{

    /**
     * @var user email
     */
    public $identity;
    private $user = false;

    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['identity', 'required'],
            [['reCaptcha'], ReCaptchaValidator::className()]
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = $this->getUser();
        if ($user) {
            $token = UserToken::create($user->id, UserToken::TYPE_PASSWORD_RESET, Time::SECONDS_IN_A_DAY);
            if ($user->save()) {
                return Yii::$app->commandBus->handle(new SendEmailCommand([
                            'to'      => $user->email,
                            'subject' => Yii::t('frontend', 'Сброс пароля пользователя на сайте {name}', ['name' => Yii::$app->name]),
                            'view'    => 'passwordResetToken',
                            'params'  => [
                                'user'  => $user,
                                'token' => $token->token
                            ]
                ]));
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'identity' => Yii::t('frontend', 'Имя пользователя или e-mail'),
        ];
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->user === false) {
            $this->user = User::find()
                    ->active()
                    ->andWhere(['or', ['username' => $this->identity], ['email' => $this->identity]])
                    ->one();
        }

        return $this->user;
    }

}
