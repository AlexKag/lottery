<?php

namespace frontend\modules\user\models;

//use common\models\User;
use common\models\UserToken;
use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{

    /**
     * @var
     */
    public $password;
    public $password_confirm;

    /**
     * @var \common\models\UserToken
     */
    private $token;

    /**
     * Creates a form model given a token.
     *
     * @param  string                          $token
     * @param  array                           $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Для восстановления пароля необходим токен.');
        }
        /** @var UserToken $tokenModel */
        $this->token = UserToken::find()
                ->notExpired()
                ->byType(UserToken::TYPE_PASSWORD_RESET)
                ->byToken($token)
                ->one();

        if (!$this->token) {
            throw new InvalidParamException('Неправильный токен. Восстановление пароля невозможно.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user           = $this->token->user;
        $user->password = $this->password;
        if ($user->save()) {
            $this->token->delete();
        };

        return true;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password'         => Yii::t('frontend', 'Новый пароль'),
            'password_confirm' => Yii::t('frontend', 'Повторите новый пароль'),
        ];
    }

}
