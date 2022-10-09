<?php

namespace common\components\lottery\models;

use Yii;

/**
 * This is the model class for table "{{%l5x36_draw}}".
 * Statistics calculation
 * @property integer $bet_id
 * @property integer $number
 */
class L5x36Draw extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%l5x36_draw}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bet_id'], 'required'],
            [['bet_id', 'number'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bet_id' => 'Bet ID',
            'number' => 'Number',
        ];
    }

    /**
     * @inheritdoc
     * @return L5x36DrawQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new L5x36DrawQuery(get_called_class());
    }

    public static function importBet(LBet $bet)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $res         = true;
        foreach ($bet->_bet as $number) {
            $draw = new L5x36Draw([
                'bet_id' => $bet->id,
                'number' => $number,
            ]);
            $res  = $res && $draw->save();
        }
        if ($res) {
            $transaction->commit();
        }
        else {
            $transaction->rollBack();
        }
        return $res;
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getBet()
    {
        return $this->hasOne(L5x36Bets::className(), ['id' => 'bet_id']);
    }

}
