<?php

namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use common\models\UserToken;

/**
 * 2FA second step
 */
class TwoStepAuthStep2Form extends Model
{

    const GA    = UserToken::TYPE_2FA_GA;
    const MAIL = UserToken::TYPE_2FA_MAIL;

    public $token;
    public $code;
    public $type;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code', 'token', 'type'], 'required'],
            ['token', 'string'],
            ['code', 'integer'],
            ['type', 'in', 'range' => [self::GA, self::MAIL]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'token' => Yii::t('frontend', 'Персональный ключ'),
            'code'  => Yii::t('frontend', 'Код подтверждения'),
        ];
    }

}
