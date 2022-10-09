<?php

namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;

/**
 * Google Authenticator form
 */
class GAForm extends Model
{

    public $token;
    public $code;
    public $enable = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'required'],
            ['token', 'string'],
            ['code', 'integer'],
            ['enable', 'boolean'],
            ['code', 'validateCode'],
            ['token', 'required', 'when' => function($model) {
                    return (bool) $model->enable;
                }]
        ];
    }

    public function attributeLabels()
    {
        return [
            'token' => Yii::t('frontend', 'Персональный ключ'),
            'code'  => Yii::t('frontend', 'Код подтверждения'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateCode()
    {
        if (!$this->hasErrors()) {
            if ($this->enable) {
                if (!Yii::$app->googleAuth->checkCode($this->token, $this->code)) {
                    $this->addError('code', Yii::t('frontend', 'Неверный код подтверждения.'));
                }
            }
            else {
                if (!Yii::$app->googleAuth->checkCode(Yii::$app->user->identity->ga_token, $this->code)) {
                    $this->addError('code', Yii::t('frontend', 'Неверный код подтверждения.'));
                }
            }
        }
    }

}
