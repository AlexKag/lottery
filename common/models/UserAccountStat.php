<?php

namespace common\models;

use Yii;
use common\models\User;
use common\models\UserProfile;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%user_account_stat}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $direction
 * @property string $amount
 * @property string $system
 * @property string $status
 * @property string $target
 * @property string $operation_id
 * @property string $description
 * @property integer $created_at
 * @property integer $updated_at
 */
class UserAccountStat extends \yii\db\ActiveRecord
{

    const DIRECTION_IN             = 'in';
    const DIRECTION_OUT            = 'out';
    const STATUS_REQUESTED         = 'requested'; //Запрос на вывод средств
    const STATUS_CONFIRMED         = 'confirmed'; //Заявка подтверждена пользователем
    const STATUS_POSTPONED         = 'postponed'; //Заявка отправлена на подтверждение оператору
    const STATUS_APPROVED          = 'approved'; //Подтверждена оператором и выполнен перевод
    const STATUS_CANCELLED         = 'cancelled';
    const STATUS_CANCELLED_BY_USER = 'cancelled by user';
    const STATUS_ERRORED           = 'error';
    const STATUS_FINISHED          = 'finished';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_account_stat}}';
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_REQUESTED => self::STATUS_REQUESTED,
            self::STATUS_POSTPONED => self::STATUS_POSTPONED,
            self::STATUS_FINISHED => self::STATUS_FINISHED,
            self::STATUS_CANCELLED => self::STATUS_CANCELLED,
            self::STATUS_APPROVED => self::STATUS_APPROVED,
            self::STATUS_ERRORED => self::STATUS_ERRORED,
            self::STATUS_CANCELLED_BY_USER => self::STATUS_CANCELLED_BY_USER,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['direction'], 'in', 'range' => ['in', 'out']],
            ['target', 'string', 'max' => 100],
            [['amount'], 'number'],
            [['description'], 'string', 'max' => 1024],
            ['operation_id', 'safe'],
            ['system', 'string', 'max' => 30],
            ['status', 'string', 'max' => 20],
            ['status', 'in', 'range' => [
                    self::STATUS_APPROVED,
                    self::STATUS_CANCELLED,
                    self::STATUS_CANCELLED_BY_USER,
                    self::STATUS_FINISHED,
                    self::STATUS_POSTPONED,
                    self::STATUS_REQUESTED,
                    self::STATUS_ERRORED
                ]],
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
            'direction' => Yii::t('common', 'Direction'),
            'amount' => Yii::t('common', 'Amount'),
            'target' => Yii::t('common', 'Target Account'),
            'system' => Yii::t('common', 'Payment System'),
            'status' => Yii::t('common', 'Operation status'),
            'operation_id' => Yii::t('common', 'Operation ID'),
            'description' => Yii::t('common', 'Description'),
            'created_at' => Yii::t('common', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     * @return UserAccountStatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAccountStatQuery(get_called_class());
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'user_id']);
    }

    public function cancel($status = self::STATUS_CANCELLED)
    {
        if (in_array($this->status, [self::STATUS_POSTPONED, self::STATUS_ERRORED, self::STATUS_REQUESTED]) && $this->direction == self::DIRECTION_OUT) {
//            $this->status = self::STATUS_CANCELLED;
            $this->status = $status;
            $this->userProfile->accountCharge($this->amount, 'Payout cancel', true);
            return $this->userProfile->save() && $this->save();
        } else {
            return false;
        }
    }

    public function confirm()
    {
        if (in_array($this->status, [self::STATUS_ERRORED, self::STATUS_REQUESTED]) && $this->direction == self::DIRECTION_OUT) {
            $this->status = self::STATUS_POSTPONED;
            return $this->save();
        } else {
            return false;
        }
    }

    //Запрос на вывод средств
//    public function query()
//    {
//        $this->status = self::STATUS_REQUESTED;
//        $this->userProfile->accountWithdraw()
//    }
}
