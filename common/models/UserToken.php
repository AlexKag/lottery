<?php

namespace common\models;

use common\models\query\UserTokenQuery;
use Yii;
use yii\base\InvalidCallException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use frontend\models\PaymentForm;

/**
 * This is the model class for table "{{%user_token}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $type
 * @property string $token
 * @property string $message
 * @property integer $expire_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 */
class UserToken extends ActiveRecord
{

    const TOKEN_LENGTH = 40;
    const PAYMENT_TOKEN_LENGTH = 32;
    const TYPE_ACTIVATION = 'activation';
    const TYPE_PASSWORD_RESET = 'password_reset';
    const TYPE_2FA_GA = '2fa_ga'; //Двухэтапная аутентификация Google Authenticator
    const TYPE_2FA_MAIL = '2fa_mail'; //Двухэтапная аутентификация через почту
    const TYPE_PAYMENT_PERFECTMONEY = 'payment_' . PaymentForm::PAYMENT_PERFECTMONEY;
    const TYPE_PAYMENT_PAYEER = 'payment_' . PaymentForm::PAYMENT_PAYEER;
    const TYPE_PAYMENT_BITCOIN = 'payment_' . PaymentForm::PAYMENT_BITCOIN;
    const TYPE_PAYMENT_FCHANGE = 'payment_' . PaymentForm::PAYMENT_FCHANGE;

    /**
     * @return string
     */
    function __toString()
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @return UserTokenQuery
     */
    public static function find()
    {
        return new UserTokenQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'type', 'token'], 'required'],
            [['user_id', 'expire_at'], 'integer'],
            [['type'], 'string', 'max' => 255],
//            [['token'], 'string', 'max' => self::TOKEN_LENGTH]
            [['token'], 'string', 'max' => max(self::TOKEN_LENGTH, self::PAYMENT_TOKEN_LENGTH)],
            ['message', 'string', 'max' => 200],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'user_id' => Yii::t('common', 'User ID'),
            'type' => Yii::t('common', 'Type'),
            'token' => Yii::t('common', 'Token'),
            'expire_at' => Yii::t('common', 'Expire At'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @param mixed $user_id
     * @param string $type
     * @param int|null $duration
     * @return bool|UserToken
     */
    public static function create($user_id, $type, $duration = null, $token_length = self::TOKEN_LENGTH, $message = null)
    {
        $model = new self;
        $model->setAttributes([
            'user_id' => $user_id,
            'type' => $type,
            'token' => Yii::$app->security->generateRandomString($token_length),
            'message' => $message,
            'expire_at' => $duration ? time() + $duration : null
        ]);

        if (!$model->save()) {
            throw new InvalidCallException;
        };

        return $model;
    }

    /**
     * @param int|null $duration
     */
    public function renew($duration)
    {
        $this->updateAttributes([
            'expire_at' => $duration ? time() + $duration : null
        ]);
    }

}
