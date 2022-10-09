<?php

namespace common\components\lottery\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\BaseJson;
use common\components\lottery\l5x36\Lottery;

/**
 * This is the model class for table "{{%l5x36}}".
 *
 * @property integer $id
 * @property integer $draw_at
 * @property string $superprize
 * @property string $superprize_gain
 * @property string $tickets
 * @property string $bets
 * @property string $pool
 * @property string $paid_out
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $wins_stat
 * @property string $draw
 *
 * @property L5x36Bets[] $l5x36Bets
 */
class L5x36 extends \yii\db\ActiveRecord
{

    const DEFAULT_SUPERPRIZE = 30000;
//    public $defaultSuperprize = 30000;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%l5x36}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['draw_at', 'draw', 'superprize', 'superprize_gain', 'tickets', 'bets', 'pool', 'paid_out', 'created_at', 'updated_at', 'wins_stat'], 'safe'],
            [['draw_at', 'tickets', 'bets', 'created_at', 'updated_at'], 'integer'],
            [['superprize', 'superprize_gain', 'pool', 'paid_out'], 'number'],
            [['wins_stat'], 'string'],
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
            'draw'            => Yii::t('frontend', 'Draw'),
            'draw_at'         => Yii::t('frontend', 'Draw date'),
            'superprize'      => Yii::t('frontend', 'Superprize'),
            'superprize_gain' => Yii::t('frontend', 'Superprize Gain'),
            'tickets'         => Yii::t('frontend', 'Tickets'),
            'bets'            => Yii::t('frontend', 'Bets'),
            'pool'            => Yii::t('frontend', 'Pool'),
            'paid_out'        => Yii::t('frontend', 'Paid Out'),
            'created_at'      => Yii::t('frontend', 'Created At'),
            'updated_at'      => Yii::t('frontend', 'Updated At'),
            'wins_stat'       => Yii::t('frontend', 'Wins Stat'),
        ];
    }

    public function set_Draw(array $draw)
    {
//        $this->draw = json_encode($draw);
        $this->draw = BaseJson::encode($draw);
//        Yii::$app->info($this->draw);
    }

    public function get_Draw()
    {
        return BaseJson::decode($this->draw);
    }

    public function set_Wins_Stat(array $stat)
    {
//        $this->draw = json_encode($draw);
        $this->wins_stat = BaseJson::encode($stat);
//        Yii::$app->info($this->draw);
    }

    public function get_Wins_Stat()
    {
        return BaseJson::decode($this->wins_stat);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getL5x36Bets()
    {
        return $this->hasMany(L5x36Bets::className(), ['lottery_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return L5x36Query the active query used by this AR class.
     */
    public static function find()
    {
        return new L5x36Query(get_called_class());
    }

    /**
     *
     * @return L5x36Query
     */
    public static function findCurrent()
    {
        return static::find()
                        ->where([
//                            'is_finished' => false,
                            'draw' => null,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at ASC')
                        ->one();
    }

    /**
     *
     * @return L5x36Query
     */
    public static function findLastFinished()
    {
        return static::find()
                        ->where([
//                            'is_finished' => true,
                            'not', ['draw' => null]
                        ])
                        ->andWhere(['<', 'draw_at', time()])
                        ->orderBy('draw_at DESC')
                        ->one();
    }

    /**
     * Last finished (с проверкой временного интервала)
     * Завершилась не более чем предыдущий промежуток времени
     * @param integer $timeinterval seconds
     * @return L5x36Query
     */
    public static function findLastFinishedByTime($timeInterval)
    {
        return static::find()
                        ->where([
//                            'is_finished' => true,
                            'not', ['draw' => null]
                        ])
                        ->andWhere(['<', 'draw_at', time()])
                        ->andWhere(['<', time() . '- draw_at', $timeInterval])
                        ->orderBy('draw_at DESC')
                        ->one();
    }

    /**
     * Самый последний розыгрыш (не сыгранный) в будущем
     * @return L5x36Query
     */
    public static function findLastInFuture()
    {
        return static::find()
                        ->where([
                            'draw' => null,
//                            'is_finished' => false,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at DESC')
                        ->one();
    }

    /**
     * Следующий розыгрыш
     * @return L5x36Query
     */
    public static function findNext(L5x36 $lottery = null)
    {
        if (is_null($lottery)) {
            return static::find()
                            ->where([
                                'draw' => null,
//                            'is_finished' => false,
                            ])
                            ->andWhere(['>', 'draw_at', time()])
                            ->orderBy('draw_at ASC')
                            ->offset(1)
                            ->one(); //?limit(1)
        }
        else {
            $lotteryNext = L5x36::find()->where(['id'=>1+$lottery->id])->limit(1)->one();
            if (!$lotteryNext instanceof L5x36) {
                $lotteryNext = Lottery::createSet(1);
                $lotteryNext = array_shift($lotteryNext);
//                            $lotteryNext = array_shift($lotteryNext);
            }
        }
        return $lotteryNext;
    }

    /**
     * Human readable datetime of draw
     */
    public function getDraw_Dt()
    {
        return Yii::$app->formatter->asDatetime($this->draw_at);
    }

    public function getTicketsCount(){

    }

}
