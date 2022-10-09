<?php

namespace frontend\modules\user\models;

use yii\base\Model;
use Yii;
//use yii\web\JsExpression;
use borales\extensions\phoneInput\PhoneInputValidator;
use borales\extensions\phoneInput\PhoneInputBehavior;

/**
 * ProfileChange form
 */
class ProfileChangeForm extends Model
{

    public $username;
    public $firstname;
    public $middlename;
    public $lastname;
    public $phone;

    protected $user;
//    public $country;
//    public $city;

    public function setUser($user)
    {
        $this->user = $user;
        $this->attributes = $user->attributes;
        $this->attributes = $user->userProfile->attributes;
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
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'firstname', 'middlename', 'lastname'], 'filter', 'filter' => 'trim'],
            [['username', 'firstname', 'middlename', 'lastname'], 'filter', 'filter' => '\yii\helpers\Html::encode'],
            ['username', 'string', 'min' => 1, 'max' => 32],
            [['firstname', 'middlename', 'lastname'], 'string', 'min' => 0, 'max' => 255],
            ['username', 'required'],
            ['username', 'unique',
                'targetClass' => '\common\models\User',
                'message'     => Yii::t('frontend', 'Логин занят.'),
                'filter'      => function ($query) {
                    $query->andWhere(['not', ['id' => getMyId()]]);
                }
                    ],
                    [['phone'], 'string', 'max' => 30],
                    [['phone'], PhoneInputValidator::className()],
                    ['phone', 'unique',
                        'targetClass' => '\common\models\User',
                        'message'     => Yii::t('frontend', 'Телефон уже зарегистрирован.'),
                        'filter'      => function ($query) {
                            $query->andWhere(['not', ['id' => getMyId()]]);
                        }
                            ],
                        ];
                    }

    public function attributeLabels()
    {
        return [
            'username'   => Yii::t('frontend', 'Логин'),
            'firstname'  => Yii::t('frontend', 'Имя'),
            'middlename' => Yii::t('frontend', 'Отчество'),
            'lastname'   => Yii::t('frontend', 'Фамилия'),
            'phone'      => Yii::t('frontend', 'Телефон'),
        ];
    }

    public function save()
    {
        $user = $this->user;
        if($user->username <> $this->username){
            $user->username = $this->username;
        }
        if($user->phone <> $this->phone){
            $user->phone = $this->phone;
        }
        $user->userProfile->attributes = $this->attributes;

        return $user->save() && $user->userProfile->save();
    }

}
