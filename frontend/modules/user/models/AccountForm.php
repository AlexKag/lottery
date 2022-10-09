<?php
namespace frontend\modules\user\models;

use yii\base\Model;
use Yii;
use yii\web\JsExpression;

/**
 * Account form
 */
class AccountForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_confirm;

    private $user;

    public function setUser($user)
    {
        $this->user = $user;
        $this->email = $user->email;
        $this->username = $user->username;
    }

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
                'message' => Yii::t('frontend', 'Логин занят.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => getMyId()]]);
                }
            ],
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('frontend', 'E-mail занят.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => getMyId()]]);
                }
            ],
            ['password', 'string'],
            [
                'password_confirm',
                'required',
                'when' => function($model) {
                    return !empty($model->password);
                },
                'whenClient' => new JsExpression("function (attribute, value) {
                    return $('#accountform-password').val().length > 0;
                }")
            ],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Логин'),
            'email' => Yii::t('frontend', 'E-mail'),
            'password' => Yii::t('frontend', 'Пароль'),
            'password_confirm' => Yii::t('frontend', 'Повторите пароль')
        ];
    }

    public function save()
    {
        $this->user->username = $this->username;
        $this->user->email = $this->email;
        if ($this->password) {
            $this->user->setPassword($this->password);
        }
        return $this->user->save();
    }
}
