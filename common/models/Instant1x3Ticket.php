<?php

namespace common\components\lottery\models;

use Yii;

/**
 * This is the model class for table "{{%instant1x3_ticket}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $paid
 * @property integer $win_combination
 * @property integer $win_cnt
 * @property string $paid_out
 * @property integer $bet
 * @property integer $created_at
 * @property integer $updated_at
 */
class Instant1x3Ticket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%L1x3_ticket}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'paid', 'created_at', 'updated_at'], 'required'],
            [['user_id', 'paid', 'win_combination', 'win_cnt', 'bet', 'created_at', 'updated_at'], 'integer'],
            [['paid_out'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'paid' => 'Paid',
            'win_combination' => 'Win Combination',
            'win_cnt' => 'Win Cnt',
            'paid_out' => 'Paid Out',
            'bet' => 'Bet',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     * @return Instant1x3TicketQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new Instant1x3TicketQuery(get_called_class());
    }
}
