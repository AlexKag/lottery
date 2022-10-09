<?php

namespace common\components\lottery\l5x36;

//use yii\base\Component;
use Yii;
use yii\base\Object;
use yii\base\Exception;
use common\components\lottery\models\L5x36;
use common\components\lottery\models\L5x36Bets;
use common\components\lottery\models\L5x36Draw;
//use common\components\lottery\models\L5x36DrawQuery;
use common\commands\AddToTimelineCommand;
use common\models\User;
use common\models\UserProfile;

/**
 * Lottery 5x36
 *
 * @author Mega
 */
class Lottery extends Object
{

    const NAME = '5 out of 36';

    public $id;
    public $game;

    public function __construct($id, $config = [])
    {
        $this->id = $id;
        parent::__construct($config);
    }

//    public function getDraw()
//    {
//        $randomConfig                 = [
//            'count' => 5,
//            'max'   => 36
//        ];
//        Yii::$app->random->attributes = $randomConfig;
//        $draw                         = Yii::$app->random->numbers;
//
//        L5x36::findOne($id);
//    }

    /**
     * Призовой фонд
     */
    public static function getPool(L5x36 $lottery)
    {
//        $z       = $lottery = L5x36::findLastFinished();
//        if ($lottery = L5x36::findLastFinished()) {
        $lottery->pool = L5x36Bets::find()->pool($lottery->id);
        $lottery->save();
        return $lottery->pool;
//        }
//        else {
//            return null;
//        }
    }

//    public function getLotteryBets()
//    {
//        return L5x36::find([
//                            'lottery_id' => $this->id,
//                        ])
//                        ->where([
////                            'is_finished' => true,
//                            'not', ['draw' => null]
//                        ])
//        ;
//    }
    //TODO one table for all games statistics (use yii2 optimistic locks)
    /**
     * Подсчитать результаты розыгранной лотереи
     */
    public static function checkBets(L5x36 $lottery)
    {
        //Распаковка данных розыгрыша для подсчёта статистики
//        $lottery  = L5x36::findLastFinished();
        $draw     = $lottery->_draw;
        $query    = L5x36Bets::find(['lottery_id' => $lottery->id,]);
        $imported = 0;
        L5x36Draw::deleteAll();
        foreach ($query->batch() as $bets) {
            foreach ($bets as $bet) {
                if (L5x36Draw::importBet($bet)) {
                    $imported ++;
                }
                else {
                    Yii::warning([
                        'message' => 'Can\'t import bet',
                        'bet'     => $bet,
                            ], 'lottery\\' . self::NAME . '\draw');
                }
            }
        }

//        $dt = Yii::$app->formatter->asDatetime($lottery->draw_at);
        Yii::info("Lottery id:{$lottery->id}[{$lottery->draw_dt}] drawed out. Counted [$imported] bets.");

//        $query = L5x36Draw::find()
//                ->select(['id',])
//                ->join('LEFT JOIN', L5x36Bets::tableName(), ['in', L5x36Draw::tableName() . '.number', $draw])
//                ->where([
//            'lottery_id' => $lottery->id,
//        ]);
        //Подсчёт выигрышных комбинаций
        $winsQuery = L5x36Draw::find()
                ->select([
                    'bet_id',
                    'GROUP_CONCAT(number) as wins',
                    'COUNT(number) as cnt'
                ])
                ->where(['in', 'number', $draw])
                ->groupBy('bet_id')
                ->having(['>=', 'cnt', 2])
//                ->with('bet');
                ->asArray();

//        $command = $winsQuery->createCommand();// $command->sql returns the actual SQL
        foreach ($winsQuery->batch() as $wins) {
            foreach ($wins as $win) {
                $bet = L5x36Bets::findOne(['id' => $win['bet_id']]);
                if ($bet) {
                    $bet->_win_combination = explode(',', $win['wins']);
                    $bet->win_cnt          = $win['cnt'];
                    if (!$bet->save()) {
                        Yii::warning("Lottery id:{$lottery->id}[$lottery->draw_dt] bet save error. Bet id:[{$win['bet_id']}].");
                    }
                }
                else {
                    Yii::warning("Lottery id:{$lottery->id}[{$lottery->draw_dt}] bet calculation error. Bet id:[{$win['bet_id']}] is missed.");
                }
            }
        }
    }

    /**
     * Выплатить/распределить призовой фонд
     */
    public static function payOutBets(L5x36 $lottery)
    {
//Подсчёт выигрышных комбинаций
        //Распределение фонда игры
//        SELECT LotteryTicket.TicketID, GROUP_CONCAT(LotteryTicketNumbers.number), COUNT(LotteryTicketNumbers.number) AS cnt
//FROM LotteryTicket
//LEFT JOIN LotterYTicketNumbers ON (LotteryTicketNumbers.number IN (winning, numbers, here))
//GROUP BY LotteryTicket.TicketID
//HAVING cnt >= 3;
        $wins = L5x36Bets::find()
                ->select([
                    'win_cnt as correct_numbers',
                    'COUNT(win_cnt) as winners',
                ])
                ->where(['lottery_id' => $lottery->id])
                ->groupBy('correct_numbers')
                ->having(['>=', 'correct_numbers', 2])
                ->orderBy('correct_numbers')
//                ->with('bet');
                ->asArray()
                ->all();

        $pool          = self::getPool($lottery) / 2;
        $lottery->pool = $pool;

        //Выплата
        $sum     = 0;
        //Статистика розыгрыша
        $winStat = [];
        foreach ($wins as $win) {
            switch ($win['correct_numbers']) {
                case 2:
                    $paidOut = L5x36Bets::PAIDOUT2;
                    break;
                case 3:
                    $paidOut = L5x36Bets::PAIDOUT3;
                    break;
                case 4:
                    $paidOut = L5x36Bets::PAIDOUT4;
                    break;
                case 5:
                    if ($win['winners'] > 0) {
                        $paidOut = ($lottery->superprize + $pool - $sum) / $win['winners'];
                        //FIXME Что делать, если $pool - $sum < 0, т.е. денег на выигрыш не хватило
                    }
                    else {
                        $paidOut = 0;
                    }
                    break;
                default:
                    $paidOut = 0;
                    break;
            }
            $sum += $paidOut * $win['winners'];
            //http://stackoverflow.com/questions/25469689/yii2-update-field-with-query-builder
            Yii::$app->db->createCommand()
                    ->update(
                            L5x36Bets::tableName(), ['paid_out' => $paidOut], ['win_cnt' => $win['correct_numbers'], 'lottery_id' => $lottery->id]
                    )
                    ->execute();

            $winStat[$win['correct_numbers']] = [
                'correct_numbers' => $win['correct_numbers'],
                'winners'         => $win['winners'],
                'paid_out'        => $paidOut,
                'paid_out_total'  => $paidOut * $win['winners']
            ];
        }
        //Увеличить призовой фонд следующей лотереи
        $lotteryNext = L5x36::findCurrent($lottery); //was findNext()
        if (isset($winStat[5])) {
            $lotteryNext->superprize = L5x36::DEFAULT_SUPERPRIZE;
        }
        else {
            $lottery->superprize_gain = $pool - $sum; //Calculates after all deductions because ORDER BY correct_numbers
            $lotteryNext->superprize  = $lottery->superprize + $lottery->superprize_gain;
        }
        $lotteryNext->save();

        $lottery->tickets    = L5x36Bets::find()->where(['lottery_id' => $lottery->id])->count('id');
        $lottery->paid_out   = $sum;
        $lottery->_wins_stat = $winStat;
        $lottery->save();

        //Начисление выигрышей
        //UPDATE lottery_user_profile as b INNER JOIN lottery_l5x36_bets as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out is not null
        //http://stackoverflow.com/questions/11709043/mysql-update-column-with-value-from-another-table
        $sql = sprintf('UPDATE %s as b INNER JOIN %s as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out IS NOT null', UserProfile::tableName(), L5x36Bets::tableName());
        Yii::$app->db->createCommand($sql)->execute();
        Yii::info("Lottery id:{$lottery->id} [{$lottery->draw_dt}] is paid out. Total sum is {$sum}.");
    }

    /**
     * Create Lottery
     * @param timestamp $draw_at
     * @return L5x36
     * @throws Exception
     */
    public static function create($draw_at)
    {
        $lottery = new L5x36();
        if ($draw_at <= time()) {
            throw new Exception("New lottery draw time [$draw_at] expired. Now is " . time());
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

    public static function createSet($count = 10)
    {
        $period            = Yii::$app->keyStorage->get('lottery.5x36.interval');
        $lastFutureLottery = L5x36::findLastInFuture();
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
     * Generates range of timestamps with $interval period
     * @param integer $timestamp
     * @param type $count
     * @param type $interval
     */
//    public static function timeRange($timestamp, $count = 1, $interval = 3600)
//    {
////        yield;
//    }

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

    public static function createBet($lotteryId, User $user, $paid, array $bet)
    {
        $userProfile = $user->userProfile;
        if ($userProfile->account < $paid) {
            //TODO Exception classes
            Yii::warning([
                'message' => 'Insufficient funds',
                'user'    => $user,
                'paid'    => $paid,
                    ], 'lottery\\' . self::NAME . '\createBet'
            );
//            throw new Exception('Insufficient funds'); //Недостаточно средств
            return null;
        }
        $userProfile->account -= $paid;
        if (!$userProfile->save()) {
            Yii::warning([
                'message' => 'Money withdraw error',
                'user'    => $user,
                'paid'    => $paid,
                    ], 'lottery\\' . self::NAME . '\createBet'
            );
//            throw new Exception('Money withdraw error'); //Ошибка списания средств
            return null;
        }

        $lBet = new L5x36Bets([
            'lottery_id' => $lotteryId,
            'user_id'    => $user->id,
            'paid'       => $paid,
            '_bet'       => $bet
        ]);

//        $count = count($bet);
//        if ($count < 5 or $count > 12) {
//            throw new Exception("Lottery 5 out of 36 bet error. Wrong numbers count [$count].");
//        }
        //TODO Проверить на повторяющиеся номера
//        $cBet = $bet;
//        for ($i = 1; $i <= $count; $i++) {
//            $lBet->{'bet_' . $i} = array_shift($cBet);
//        }

        if ($lBet->save()) {
            $addToTimelineCommand = new AddToTimelineCommand([
                'category' => 'lottery',
                'event'    => 'bet',
                'data'     => [
                    'id'         => $lBet->id,
                    'user_id'    => $lBet->user_id,
                    'lottery_id' => $lBet->lottery_id,
                    'type'       => static::NAME,
                    'numbers'    => implode('; ', $bet),
                    'paid'       => $lBet->paid,
                    'created_at' => $lBet->created_at,
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
        }

        return $lBet;
    }

    /**
     * Make bets for current lottery (test method)
     * @param type $count
     * @return type
     */
    public static function _createBets($count = 10)
    {
        $users                        = [User::findIdentity(5), User::findIdentity(4)];
        $lottery                      = L5x36::findCurrent();
        $randomConfig                 = [
            'count' => 5,
            'max'   => 36,
        ];
        Yii::$app->random->attributes = $randomConfig;

        $created = [];
        for ($i = 0; $i < $count; $i++) {
            $k = array_rand($users);
            try {
                $created[] = static::createBet($lottery->id, $users[$k], 10, Yii::$app->random->numbers);
            } catch (\yii\db\Exception $e) {

            }
        }
        return $created;
    }

    /**
     * Розыгрыш лотереи - определение выигрышной комбинации
     * @return type
     */
    public static function Draw()
    {
        $lottery                      = L5x36::findCurrent();
        $randomConfig                 = [
            'count' => 5,
            'max'   => 36
        ];
        Yii::$app->random->attributes = $randomConfig;
        $lottery->_draw               = Yii::$app->random->numbers;
        if ($lottery->save()) {
            $addToTimelineCommand = new AddToTimelineCommand([
                'category' => 'lottery',
                'event'    => 'draw',
                'data'     => [
                    'id'         => $lottery->id,
                    'draw'       => $lottery->_draw,
                    'created_at' => time(),
                ]
            ]);
            Yii::$app->commandBus->handle($addToTimelineCommand);
            return $lottery;
        }
        return null;
    }

}
