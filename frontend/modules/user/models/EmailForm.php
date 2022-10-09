<?php

namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;

/**
 * 2FA e-mail form
 */
class EmailForm extends Model
{

    public $code;
    public $enable = 1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['code', 'required'],
            ['code', 'integer'],
            ['enable', 'boolean'],
            ['code', 'validateCode'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'code' => Yii::t('frontend', 'Код подтверждения'),
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     */
    public function validateCode()
    {
        if (!$this->hasErrors()) {
            if (Yii::$app->user->identity->email_auth_code != $this->code) {
                $this->addError('code', Yii::t('frontend', 'Неверный код подтверждения.'));
            }
        }
    }

}
