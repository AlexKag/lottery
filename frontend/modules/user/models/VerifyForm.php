<?php

namespace frontend\modules\user\models;

//use cheatsheet\Time;
//use common\models\User;
use Yii;
use yii\base\Model;
use himiklab\yii2\recaptcha\ReCaptchaValidator;

/**
 * Login form
 */
class VerifyForm extends Model
{

    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'Ваш E-mail'),
        ];
    }

}
