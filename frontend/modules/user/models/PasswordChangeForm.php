<?php

namespace frontend\modules\user\models;

use yii\base\Model;
use Yii;
use yii\web\JsExpression;

/**
 * PasswordChange form
 */
class PasswordChangeForm extends Model
{

    public $password;
    public $password_new;
    public $password_new_confirm;
    private $user;

    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_new'], 'string', 'min' => 6],
            [['password', 'password_new', 'password_new_confirm'], 'required'],
            ['password_new_confirm', 'compare', 'compareAttribute' => 'password_new', 'skipOnEmpty' => false],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password'             => Yii::t('frontend', 'Текущий пароль'),
            'password_new'         => Yii::t('frontend', 'Новый пароль'),
            'password_new_confirm' => Yii::t('frontend', 'Повторите новый пароль'),
        ];
    }

    public function save()
    {
        $isPasswordValid = $this->user->validatePassword($this->password);
        if ($isPasswordValid && $this->password_new) {
            $this->user->setPassword($this->password_new);
            Yii::$app->session->addFlash('success', "Пароль изменен.");
        }
        else {
            if (!$isPasswordValid) {
                Yii::$app->session->addFlash('warning', "Неправильный текущий пароль.");
            }
            Yii::$app->session->addFlash('warning', "Пароль не изменен.");
        }
        return $this->user->save();
    }

}
