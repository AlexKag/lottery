<?php

namespace common\components\lottery\models;

use Yii;

/**
 * Lottery 6 out of 45 model
 *
 * @author Mega
 */
class L6x45 extends BaseLottery
{

    const ID          = '6x45';
    const NAME        = '«6 из 45»';
    const DRAW_CONFIG = [
        'count' => 6,
        'max'   => 45,
    ];

//    public function rules()
//    {
//        return array_merge(parent::rules(), [
//            ['superprize', 'default', 'value' => Yii::$app->keyStorage->get('lottery.6x45.superprize.default')],
//        ]);
//    }
//    /**
//     * @inheritdoc
//     */
//    public static function tableName()
//    {
//        return '{{%l6x45}}';
//    }
//    public function getTickets()
//    {
//        return $this->hasMany(L6x45Ticket::className(), ['id' => 'lottery_id'])->inverseOf('lottery');
//    }

    protected static function _ceilToDraw($timestamp)
    {
        if (!is_numeric($timestamp)) {
            return null;
        }
        //Every day at 13 o'clock
        $dt               = getdate($timestamp);
        list($hour, $min) = explode(',', Yii::$app->keyStorage->get('lottery.6x45.draw_at'));
        $lotteryTimestamp = mktime($hour, $min, 0, $dt['mon'], $dt['mday'], $dt['year']);
//        while ($nextLotteryTimestamp < time()) {
//            $nextLotteryTimestamp += $period;
//        }
        return $lotteryTimestamp;
    }

    public function get_Superprize()
    {
        $superprize = Yii::$app->keyStorage->get('lottery.6x45.superprize.default');
        return $this->superprize > $superprize ? $this->superprize : $superprize;
    }

    //TODO one table for all games statistics (use yii2 optimistic locks)
    /**
     * Подсчитать результаты розыгранной лотереи
     */
    //Moved to Draw class
//    public function checkTickets()
//    {
//        //Распаковка данных розыгрыша для подсчёта статистики
////        $lottery  = L5x36::findLastFinished();
//        $draw     = $this->_draw;
//        $query    = L6x45Ticket::find(['lottery_id' => $this->id]);
//        $imported = 0;
//        L6x45Draw::deleteAll();
//        foreach ($query->batch() as $tickets) {
//            foreach ($tickets as $ticket) {
//                if (L6x45Draw::importTicket($ticket)) {
//                    $imported ++;
//                }
//                else {
//                    Yii::warning([
//                        'message' => 'Can\'t import ticket',
//                        'bet'     => $ticket,
//                            ], 'lottery\\' . self::ID . '\draw');
//                }
//            }
//        }
//
//        Yii::info("Lottery id:{$this->id}[{$this->draw_dt}] drawed out. Counted [$imported] tickets.");
//
//        //Подсчёт выигрышных комбинаций
//        $winsQuery = L6x45Draw::find()
//                ->select([
//                    'ticket_id',
//                    'GROUP_CONCAT(number) as wins',
//                    'COUNT(number) as cnt'
//                ])
//                ->where(['in', 'number', $draw])
//                ->groupBy('ticket_id')
//                ->having(['>=', 'cnt', 2])
////                ->with('bet');
//                ->asArray();
//
////        $command = $winsQuery->createCommand();// $command->sql returns the actual SQL
//        foreach ($winsQuery->batch() as $wins) {
//            foreach ($wins as $win) {
//                $ticket = L5x36Bets::findOne(['id' => $win['ticket_id']]);
//                if ($ticket) {
//                    $ticket->_win_combination = explode(',', $win['wins']);
//                    $ticket->win_cnt          = $win['cnt'];
//                    if (!$ticket->save()) {
//                        Yii::warning("Lottery id: {$this->id} [$this->draw_dt] ticket save error. Bet id:[{$win['ticket_id']}].");
//                    }
//                }
//                else {
//                    Yii::warning("Lottery id: {$this->id} [{$this->draw_dt}] ticket processing error. Bet id:[{$win['ticket_id']}] is missed.");
//                }
//            }
//        }
//    }
//    public function beforeValidate()
//    {
//        //FIXME Создавать через один метод статический ?
//        parent::beforeValidate();
//        $tmp = $this->draw_at;
//        if (!is_numeric($this->draw_at)) {
//            $this->draw_at = static::_ceilToDraw(strtotime($this->draw_at));
//        }
//        return true;
//    }
}
