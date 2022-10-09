<?php

namespace common\models;

use common\components\lottery\models;

//use Yii;

/**
 * This is the model class for table "{{%user_referrals_stat}}".
 *
 * @property integer $lottery_id
 * @property string $lottery_type
 * @property integer $user_id
 * @property integer $level
 * @property integer $ref_count
 * @property integer $paid_in
 * @property integer $paid_out
 * @property string $created_at
 *
 * @property User $user
 */
class UserReferralsStat extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_referrals_stat}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lottery_id', 'user_id', 'level', 'ref_count'], 'required'],
            [['lottery_id', 'user_id', 'level', 'ref_count'], 'integer'],
            [['paid_in', 'paid_out'], 'number', 'min' => 0],
            [['lottery_type'], 'string', 'max' => 10],
            [['lottery_id', 'user_id', 'level', 'lottery_type'], 'unique', 'targetAttribute' => ['lottery_id', 'user_id', 'level', 'lottery_type'], 'message' => 'The combination of Lottery ID, Lottery Type, User ID and Level has already been taken.'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lottery_id'   => 'Lottery ID',
            'lottery_type' => 'Lottery Type',
            'user_id'      => 'User ID',
            'level'        => 'Level',
            'ref_count'    => 'Ref Count',
            'paid_in'      => 'Paid In',
            'paid_out'     => 'Paid Out',
            'created_at'   => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['id' => 'user_id']);
    }

    public function getLottery()
    {
        $class = '\\L' . $this->lottery_type;
        return $this->hasOne($class::className(), ['id' => 'lottery_id']);
    }

    /**
     * @inheritdoc
     * @return UserReferralsStatQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserReferralsStatQuery(get_called_class());
    }

}
