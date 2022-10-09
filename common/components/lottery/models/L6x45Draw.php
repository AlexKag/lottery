<?php

namespace common\components\lottery\models;

use Yii;
use yii\helpers\Json;

//use common\models\UserProfile;

/**
 * L6x45Ticket
 *
 * @author Mega
 */
class L6x45Draw extends BaseDraw
{

    const ID = '6x45';
    const NAME = '«6 из 45»';

//    public function getTicket()
//    {
//        return $this->hasOne(L6x45Ticket::className(), ['id' => 'ticket_id']);
//    }
//    //TODO one table for all games statistics (use yii2 optimistic locks)
//    /**
//     * Подсчитать результаты розыгранной лотереи
//     */
//    public static function checkTickets(BaseLottery $lottery)
//    {
//        //Распаковка данных розыгрыша для подсчёта статистики
////        $lottery  = L5x36::findLastFinished();
//        $draw     = $lottery->_draw;
//        $query    = L6x45Ticket::find()->where(['lottery_id' => $lottery->id]);
//        $imported = 0;
//        static::deleteAll();
//        foreach ($query->batch() as $tickets) {
//            foreach ($tickets as $ticket) {
//                if (static::importTicket($ticket)) {
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
//        Yii::info("Lottery id:{$lottery->id}[{$lottery->draw_dt}] drawed out. Counted [$imported] tickets.");
//
//        //Подсчёт выигрышных комбинаций
//        $winsQuery = static::find()
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
//                $ticket = L6x45Ticket::findOne(['id' => $win['ticket_id']]);
//                if ($ticket) {
//                    $ticket->_win_combination = explode(',', $win['wins']);
//                    $ticket->win_cnt          = $win['cnt'];
//                    if (!$ticket->save()) {
//                        Yii::warning("Lottery id: {$lottery->id} [$lottery->draw_dt] ticket save error. Bet id:[{$win['ticket_id']}].");
//                    }
//                }
//                else {
//                    Yii::warning("Lottery id: {$lottery->id} [{$lottery->draw_dt}] ticket processing error. Bet id:[{$win['ticket_id']}] is missed.");
//                }
//            }
//        }
//    }

    /**
     * Выплатить/распределить призовой фонд
     */
    public static function payoutBets(BaseLottery $lottery)
    {
        $wins = static::_getWinsStat($lottery);
        $pool = $lottery->pool / 2;

        //Расчёт числа выигрышных комбинаций (их больше чем билетов)
        $sum = 0;
        $winStat = [];
        $paidoutPlan = Json::decode(Yii::$app->keyStorage->get('lottery.6x45.prize_distribution'));

        $wins['statExtended'][2]['paidoutSum'] = 0;
        foreach ($wins['statExtended'] as $correctNumbers => $winners) {
            if (empty($winners['winners'])) {
                continue;
            }
            $winners = $winners['winners'];
//            if (!isset($statExtended[$correctNumbers]['paidoutOne'])) {
//                $statExtended[$correctNumbers]['paidoutOne'] = 0;
//            }
            switch ($correctNumbers) {
                case 2:
                    $wins['statExtended'][$correctNumbers]['paidoutOne'] = $paidoutPlan[$correctNumbers];
                    $wins['statExtended'][$correctNumbers]['paidoutSum'] = $wins['statExtended'][$correctNumbers]['paidoutOne'] * $winners;
                    break;
                case 3:
                case 4:
                case 5:
                    $wins['statExtended'][$correctNumbers]['paidoutOne'] = ($pool - $wins['statExtended'][2]['paidoutSum'] > 0) ? $paidoutPlan[$correctNumbers] * ($pool - $wins['statExtended'][2]['paidoutSum']) / $winners : 0;
                    $wins['statExtended'][$correctNumbers]['paidoutSum'] = $wins['statExtended'][$correctNumbers]['paidoutOne'] * $winners;
                    break;
                case 6:
                    $lottery->superprize = ($pool - $wins['statExtended'][2]['paidoutSum'] > 0) ? $lottery->_superprize + $paidoutPlan[$correctNumbers] * ($pool - $wins['statExtended'][2]['paidoutSum']) : $lottery->_superprize;
                    $wins['statExtended'][$correctNumbers]['paidoutOne'] = $lottery->superprize / $winners;
                    $wins['statExtended'][$correctNumbers]['paidoutSum'] = $lottery->superprize;
                    break;
            }
            $sum += $wins['statExtended'][$correctNumbers]['paidoutSum'];

            $winStat[$correctNumbers] = [
                'correct_numbers' => $correctNumbers,
                'winners' => $winners,
                'paid_out' => $wins['statExtended'][$correctNumbers]['paidoutOne'],
                'paid_out_total' => $wins['statExtended'][$correctNumbers]['paidoutSum'],
            ];
        }

        //Увеличить призовой фонд следующей лотереи, если суперприз не разыгран
        if (!isset($wins['statExtended'][6])) {
            $lotteryNext = L6x45::findNext($lottery);
            $gain = $pool - $sum;
            $lottery->superprize_gain = $gain > 0 ? $gain : 0; //Calculates after all deductions because ORDER BY correct_numbers
            $lotteryNext->superprize = $lottery->_superprize + $lottery->superprize_gain;
            $lotteryNext->save();
        }

        //Расчёт выплат по билетам и начисление суммы на билеты
        foreach ($wins['stat'] as $win) {
            $paidoutOne = (bool) $win['is_bet_extended'] ? L6x45Ticket::betWin($win['bet_cnt'], $win['correct_numbers'], $wins['statExtended']) : $wins['statExtended'][$win['correct_numbers']]['paidoutOne'];
            static::_paidOutTickets($paidoutOne, $win['correct_numbers'], $lottery->id, $win['bet_cnt']);
        }

        $lottery->tickets = L6x45Ticket::find()->where(['lottery_id' => $lottery->id])->count('id');
        $lottery->paid_out = $sum;
        $lottery->_wins_stat = $winStat;
        $res = $lottery->save();

        //Начисление выигрышей
        //Заключить в транзакцию
        //UPDATE lottery_user_profile as b INNER JOIN lottery_l5x36_bets as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out is not null
        //http://stackoverflow.com/questions/11709043/mysql-update-column-with-value-from-another-table
//        $sql = sprintf('UPDATE %s as b INNER JOIN %s as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out IS NOT null', UserProfile::tableName(), L6x45Ticket::tableName());
//        $res = $res && Yii::$app->db->createCommand($sql)->execute();
        static::_paidOut($lottery->id);

        //Реферальные начисления
        static::_paidOutRefs($lottery);
        Yii::info("Lottery id:{$lottery->id} [{$lottery->draw_dt}] is paid out. Total paid out sum is {$sum}. Statistics: " .  print_r($winStat, 1), 'lottery\l6x45\draw');

        return $res;
    }

}
