<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;
use common\commands\SendEmailCommand;
use yii\helpers\Url;
use common\models\Page;

/**
 * Login form
 */
class InviteFriendForm extends Model
{

    public $email;
    public $name;
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [];
//        if (Yii::$app->user->isGuest) {
        $rules[] = [['reCaptcha'], ReCaptchaValidator::className(), 'skipOnEmpty' => !Yii::$app->user->isGuest];
//        }
        $rules[] = ['email', 'required'];
        $rules[] = ['email', 'email'];
        $rules[] = [['reCaptcha', 'name'], 'safe'];
        $rules[] = ['name', 'string', 'max' => 50];
        return $rules;
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'E-mail вашего друга'),
            'name' => Yii::t('frontend', 'Ваше имя'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        $subject = 'Приглашение от вашего друга';
        if (empty($this->name)) {
//            $subject .= Yii::$app->user->isGuest ? null : Yii::$app->user->identity->getPublicIdentity();
        } else {
            $subject .= ' ' . $this->name;
        }
        $subject .= ' на игру в честную лотерею ' . Yii::$app->name;
        $refParams = [];
        $refParams[] = '/user/sign-in/signup';
        if (!Yii::$app->user->isGuest) {
            $refParams['ref'] = Yii::$app->user->identity->referral_id;
        }

        $page = Page::find()->where(['slug' => 'invitefriendemail', 'status' => Page::STATUS_PUBLISHED])->one();
        $inviteFriendText = is_null($page) ? '' : $page->body;

        return Yii::$app->commandBus->handle(new SendEmailCommand([
                    'to' => $this->email,
                    'subject' => $subject,
                    'view' => 'inviteFriend',
                    'params' => [
                        'inviteUrl' => Url::to($refParams, true),
                        'inviteFriendText' => $inviteFriendText,
                    ]
        ]));
    }

}
