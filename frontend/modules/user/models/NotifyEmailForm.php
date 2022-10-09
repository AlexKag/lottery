<?php

namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class NotifyEmailForm extends Model
{

    public $email;
    public $type = 'common';

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            ['email', 'required'],
            ['email', 'email'],
            ['type', 'in', 'range' => ['common', 'start']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'Ваш e-mail'),
        ];
    }

}
