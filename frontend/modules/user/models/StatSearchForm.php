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
class StatSearchForm extends Model
{

    const WIN = 'win';
    const LOOSE = 'loose';
    const NOTDRAWED = 'notdrawed';
    
    public $category;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['category', 'in', 'range' => [self::WIN, self::LOOSE, self::NOTDRAWED]],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => Yii::t('frontend', 'Вариант'),
        ];
    }

}
