<?php

namespace common\components\lottery\models;

use Yii;
use common\models\User;
use yii\behaviors\TimestampBehavior;
use yii\helpers\BaseJson;

/**
 * This is the model class for table "{{%l5x36_bets}}".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $lottery_id
 * @property integer $paid
 * @property string $win_combination
 * @property string $_win_combination Proxy to $win_combination
 * @property string $win_cnt
 * @property string $paid_out
 * @property string $bet
 * @property string $_bet Proxy to $bet
 *
 * @property L5x36 $lottery
 * @property User $user
 */
class L5x36Bets extends LBet
{

    const MIN_NUMBERS = 5;
    const MAX_NUMBERS = 12;
    const MAX_NUMBER  = 36;
    const NAME        = '5 out of 36';

    const PAIDOUT2 = 2;
    const PAIDOUT3 = 20;
    const PAIDOUT4 = 200;

//    public $minNumbers = 5;
//
//    public $maxNumbers = 12;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%l5x36_bets}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'lottery_id', 'paid', 'win_combination', 'win_cnt'], 'safe'],
            [['user_id', 'lottery_id', 'paid', 'win_cnt'], 'integer'],
            [['win_combination', 'bet'], 'string'],
            [['lottery_id'], 'exist', 'skipOnError' => false, 'targetClass' => L5x36::className(), 'targetAttribute' => ['lottery_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['paid'], 'number', 'min' => 1],
            [['paid_out'], 'number'],
            [['win_cnt'], 'number', 'min' => 2],
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
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('frontend', 'ID'),
            'user_id'         => Yii::t('frontend', 'User ID'),
            'lottery_id'      => Yii::t('frontend', 'Lottery ID'),
            'paid'            => Yii::t('frontend', 'Оплаченная сумма за билет'),
            'win_combination' => Yii::t('frontend', 'выигрышная комбинация'),
            'win_cnt'         => Yii::t('frontend', 'Число совпавших цифр'),
            'bet'             => Yii::t('frontend', 'Bet'),
            'created_at'      => Yii::t('frontend', 'Created At'),
            'updated_at'      => Yii::t('frontend', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLottery()
    {
        return $this->hasOne(L5x36::className(), ['id' => 'lottery_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return L5x36BetsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new L5x36BetsQuery(get_called_class());
    }

    public function set_Bet(array $bets)
    {
        $count = count($bets);
        if ($count < static::MIN_NUMBERS or $count > static::MAX_NUMBERS) {
            throw new Exception('Lottery ' . static::NAME . " bet error. Wrong numbers count [$count].");
        }

        //Проверка повторяющихся номеров
        $uniqCount = count(array_unique($bets));
        if ($count != $uniqCount) {
            throw new Exception('Wrong bet.' . implode(', ', $bets) . ' Doubled Numbers!');
        }

        $this->bet = BaseJson::encode($bets);
    }

    public function get_Bet()
    {
        return BaseJson::decode($this->bet);
    }

    public function set_Win_Combination(array $numbers)
    {
        $this->win_combination = BaseJson::encode($numbers);
    }

    public function get_Win_Combination()
    {
        return BaseJson::decode($this->win_combination);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDraw()
    {
        return $this->hasMany(L5x36Draw::className(), ['bet_id' => 'id']);
    }

}
