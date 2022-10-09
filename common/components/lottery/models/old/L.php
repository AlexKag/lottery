<?php

namespace common\components\lottery\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\BaseJson;

/**
 * Base lottery Class
 *
 * @author Mega
 */
abstract class L extends \yii\db\ActiveRecord
{

    public $defaultSuperprize = 30000;

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
     * Find current Lottery
     * @return
     */
    public static function findCurrent()
    {
        return static::find()
                        ->where([
                            'draw' => null,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at ASC')
                        ->limit(1)
                        ->one();
    }

    /**
     * Следующий розыгрыш
     * @return
     */
    public static function findNext(L $lottery = null)
    {
        if (is_null($lottery)) {
            return static::find()
                            ->where([
                                'draw' => null,
                            ])
                            ->andWhere(['>', 'draw_at', time()])
                            ->orderBy('draw_at ASC')
                            ->offset(1)
                            ->limit(1)
                            ->one();
        }
        else {
            $lotteryNext = static::find()->where(['id' => 1 + $lottery->id])->limit(1)->one();
            if (!$lotteryNext instanceof L) { //FIXME instance of static
                $lotteryNext = static::createSet(1);
                $lotteryNext = array_shift($lotteryNext);
            }
        }
        return $lotteryNext;
    }

    /**
     * Самый последний розыгрыш (не сыгранный) в будущем
     * @return
     */
    public static function findLastInFuture()
    {
        return static::find()
                        ->where([
                            'draw' => null,
                        ])
                        ->andWhere(['>', 'draw_at', time()])
                        ->orderBy('draw_at DESC')
                        ->limit(1)
                        ->one();
    }

    /**
     * Last finished (с проверкой временного интервала)
     * Завершилась не более чем предыдущий промежуток времени
     * @param integer $timeinterval seconds
     * @return
     */
    public static function findLastFinished($timeInterval = null)
    {
        $query = static::find()
                ->where([
                    'not', ['draw' => null]
                ])
                ->orderBy('draw_at DESC')
                ->limit(1);

        if (is_null($timeInterval)) {
            return $query
                            ->andWhere(['<', 'draw_at', time()])
                            ->one();
        }
        return $query
                        ->andWhere(['<', 'draw_at', time()])
                        ->andWhere(['<', time() . '- draw_at', $timeInterval])
                        ->one();
    }

    /**
     * Batch Lottery creation
     * @param int $count
     * @param int $period
     * @return [L]
     */
    public static function createSet($count = null, $period = 3600)
    {
        $count             = $count < 1 ? 1 : (int) $count;
//        $period            = Yii::$app->keyStorage->get('lottery.5x36.interval');
        $lastFutureLottery = static::findLastInFuture();
        $timestamp         = !empty($lastFutureLottery) ? $lastFutureLottery->draw_at : time();
        $timestamp         = static::ceilToQuarterHour($timestamp + $period);
        $created           = [];
        for ($i = 0; $i < $count; $i++) {
            $created[] = static::create($timestamp);
            $timestamp += $period;
        }
        return $created;
    }

    /**
     * Create Lottery
     * @param timestamp $draw_at
     * @return L
     * @throws Exception
     */
    public static function create($draw_at)
    {
        $lottery = new static();
        if ($draw_at <= time()) {
            Yii::warning("New lottery draw time [$draw_at] expired. Now is " . time(),'lottery\\' . static::NAME . '\createLottery');
//            throw new Exception("New lottery draw time [$draw_at] expired. Now is " . time());
            return null;
        }
        $lottery->draw_at = $draw_at;
        if ($lottery->save()) {
            $addToTimelineCommand = new AddToTimelineCommand([
                'category' => 'lottery',
                'event'    => 'create',
                'data'     => [
                    'id'         => $lottery->id,
                    'type'       => static::NAME,
                    'draw_at'    => $lottery->draw_at,
                    'created_at' => $lottery->created_at
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
        }
        return $lottery;
    }

    public static function ceilToHour($timestamp)
    {
        $dt = getdate($timestamp);
        return mktime($dt['hours'] + 1, 0, 0);
        return mktime($dt['hours'] + 1, 0, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    public static function ceilToHalfHour($timestamp)
    {
        $dt   = getdate($timestamp);
        $mins = (floor($dt['minutes'] / 30) + 1) * 30;
        return mktime($dt['hours'], $mins, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    public static function ceilToQuarterHour($timestamp)
    {
        $dt   = getdate($timestamp);
        $mins = (floor($dt['minutes'] / 15) + 1) * 15;
        return mktime($dt['hours'], $mins, 0, $dt['mon'], $dt['mday'], $dt['year']);
    }

    /**
     * Human readable datetime of draw
     */
    public function getDraw_Dt()
    {
        return Yii::$app->formatter->asDatetime($this->draw_at);
    }

    public function set_Draw(array $draw)
    {
        $this->draw = BaseJson::encode($draw);
    }

    public function get_Draw()
    {
        return BaseJson::decode($this->draw);
    }

    public function set_Wins_Stat(array $stat)
    {
        $this->wins_stat = BaseJson::encode($stat);
    }

    public function get_Wins_Stat()
    {
        return BaseJson::decode($this->wins_stat);
    }

}
