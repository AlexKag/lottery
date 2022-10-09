<?php

namespace common\components\lottery\models;

use Yii;
use common\models\UserProfile;

/**
 * BaseDraw
 *
 * @author Mega
 */
abstract class BaseDraw1 extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ticket_id', 'number'], 'required'],
            [['ticket_id', 'number'], 'integer'],
        ];
    }

    public function getName()
    {
        return static::NAME;
    }

    public function getType()
    {
        return static::ID;
    }

    public static function importTicket(BaseTicket $ticket)
    {
//        $transaction = Yii::$app->db->beginTransaction();
        $res = true;
        foreach ($ticket->_bet as $number) {
            $draw = new static([
                'ticket_id' => $ticket->id,
                'number'    => $number,
            ]);
            $res  = $res && $draw->save();
        }
//        if ($res) {
//            $transaction->commit();
//        }
//        else {
//            $transaction->rollBack();
//        }
        return $res;
    }

    //Relations

    /**
     * @return \yii\db\ActiveQuery
     */
//    abstract public function getTicket();
    public function getTicket()
    {
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        return $this->hasOne($class, ['id' => 'ticket_id']);
//        return $this->hasOne(L6x45Ticket::className(), ['id' => 'ticket_id']);
    }

    //TODO one table for all games statistics (use yii2 optimistic locks)
    /**
     * Сравнить результаты розыгранной лотереи со ставками
     */
    public static function checkTickets(BaseLottery $lottery)
    {
        //Распаковка данных розыгрыша для подсчёта статистики
//        $lottery  = L5x36::findLastFinished();
        $classTicket = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        $draw        = $lottery->_draw;
        $query       = $classTicket::find()->where(['lottery_id' => $lottery->id]);
//        $query    = L6x45Ticket::find()->where(['lottery_id' => $lottery->id]);
        $imported    = 0;
        static::deleteAll();
        foreach ($query->each(50) as $ticket) {
//            foreach ($tickets as $ticket) {
            if (static::importTicket($ticket)) {
                $imported ++;
            }
            else {
                Yii::warning([
                    'message' => 'Can\'t import ticket',
                    'bet'     => $ticket,
                        ], 'lottery\\' . self::ID . '\draw');
            }
//            }
        }

        Yii::info("Lottery id:{$lottery->id}[{$lottery->draw_dt}] drawed out. Counted [$imported] tickets.");
        //TODO Добавить событие в timeline

        //Подсчёт выигрышных комбинаций
        $winsQuery = static::find()
                ->select([
                    'ticket_id',
                    'GROUP_CONCAT(number) as wins',
                    'COUNT(number) as cnt'
                ])
                ->where(['in', 'number', $draw])
                ->groupBy('ticket_id')
                ->having(['>=', 'cnt', 2])
//                ->with('bet');
                ->asArray();

//        $command = $winsQuery->createCommand();// $command->sql returns the actual SQL
        foreach ($winsQuery->each(10) as $win) {
//            foreach ($wins as $win) {
            $ticket = $classTicket::findOne(['id' => $win['ticket_id']]);
//                $ticket = L6x45Ticket::findOne(['id' => $win['ticket_id']]);
            if ($ticket) {
                $ticket->_win_combination = explode(',', $win['wins']);
                $ticket->win_cnt          = $win['cnt'];
                if (!$ticket->save()) {
                    Yii::warning("Lottery id: {$lottery->id} [$lottery->draw_dt] ticket save error. Ticket id:[{$win['ticket_id']}].");
                }
            }
            else {
                Yii::warning("Lottery id: {$lottery->id} [{$lottery->draw_dt}] ticket processing error. Bet id:[{$win['ticket_id']}] is missed.");
            }
//            }
        }
        static::deleteAll();
    }

    /**
     * Выплатить/распределить призовой фонд
     */
//    public static function payoutBets(BaseLottery $lottery)
//    {
//        $wins = static::_getWinsStat($lottery);
//
//        $pool = $lottery->pool / 2;
//
//        static::paidOut();
//        static::calcPaidOut();
//    }

    protected static function _getWinsStat(BaseLottery $lottery)
    {
        //Распределение фонда игры
//        SELECT LotteryTicket.TicketID, GROUP_CONCAT(LotteryTicketNumbers.number), COUNT(LotteryTicketNumbers.number) AS cnt
//FROM LotteryTicket
//LEFT JOIN LotterYTicketNumbers ON (LotteryTicketNumbers.number IN (winning, numbers, here))
//GROUP BY LotteryTicket.TicketID
//HAVING cnt >= 3;

        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        return $class::find()
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
    }

    /**
     * Начисление выигрышей по билетам
     */
    protected static function _paidOutTickets($paidOutOne, $correctNumbersCount, $lotteryId)
    {
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        //http://stackoverflow.com/questions/25469689/yii2-update-field-with-query-builder
        return Yii::$app->db->createCommand()
                        ->update(
                                $class::tableName(), ['paid_out' => $paidOutOne], ['win_cnt' => $correctNumbersCount, 'lottery_id' => $lotteryId]
                        )
                        ->execute();
    }

    /**
     * Начисление выигрышей на счета
     */
    protected static function _paidOut($lottery_id)
    {
        $class = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        //Начисление выигрышей
        //Заключить в транзакцию
        //UPDATE lottery_user_profile as b INNER JOIN lottery_l5x36_bets as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out is not null
        //http://stackoverflow.com/questions/11709043/mysql-update-column-with-value-from-another-table
        //Лог начислений
        $query = $class::find()
                ->where([ 'lottery_id' => $lottery_id])
                ->andWhere(['>', 'paid_out', 0])
                ->with('userProfile');
        foreach ($query->each(50) as $ticket) {
//            $ticket->userProfile->AccountCharge($ticket->paid_out, sprintf('Зачисление выигрыша [%01.2f]. Лотерея [%s]', $ticket->paid_out, $class::ID));
//            $ticket->userProfile->save();
            $ticket->paidOut();
        }

//        $sql = sprintf('UPDATE %s as b INNER JOIN %s as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out IS NOT null AND a.lottery_id = %d', UserProfile::tableName(), $class::tableName(), $lottery_id);
//        $sql = sprintf('UPDATE %s as b INNER JOIN %s as a ON b.user_id = a.user_id SET b.account = b.account + a.paid_out WHERE a.paid_out IS NOT null', UserProfile::tableName(), L6x45Ticket::tableName());
//        return Yii::$app->db->createCommand($sql)->execute();
    }

    //Вычисление статистики по суммам
//    SELECT user_id, lottery_id, SUM(paid) as paid, sum(paid_out) as paid_out FROM `lottery_l6x45_ticket` group by user_id
    //Рефералы 1 уровня
//    SELECT a.id, a.referrer_id, a.referral_id, b.id, b.referrer_id, b.referral_id FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id
//    //Реферальные начисления 1 уровня
//    SELECT a.id, a.referrer_id, a.referral_id, b.id, b.referrer_id, b.referral_id, lottery_id, SUM(paid), sum(paid_out) FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = b.id GROUP BY d.user_id
//    COUNT(distinct b.id) - число рефералов (проверить)
//    //!!! Реф начисления 1 уровня
//    REPLACE INTO lottery_user_referrals_stat(lottery_id, user_id, level, paid_in, paid_out) SELECT d.lottery_id, a.id, 1, SUM(paid), SUM(paid_out) FROM lottery_user as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = b.id group by a.id
//    REPLACE INTO lottery_user_referrals_stat(lottery_id, user_id, level, ref_count, paid_in, paid_out) SELECT d.lottery_id, a.id, 1, count(DISTINCT b.id), SUM(paid), SUM(paid_out) FROM lottery_user as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = b.id group by a.id
    //Рефералы 2 уровня
    //SELECT a.id, a.referrer_id, a.referral_id, b.id, b.referrer_id, b.referral_id, c.id, c.referrer_id, c.referral_id FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_user as c on b.referral_id = c.referrer_id
//    SELECT a.id, a.referrer_id, a.referral_id, c.id, c.referrer_id, c.referral_id FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_user as c on b.referral_id = c.referrer_id
//Реферальные начисления 2 уровня
//    SELECT a.id, a.referrer_id, a.referral_id, c.id, c.referrer_id, c.referral_id, lottery_id, SUM(paid), sum(paid_out) FROM `lottery_user` as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_user as c on b.referral_id = c.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = c.id GROUP BY d.user_id
    //Update http://stackoverflow.com/questions/15209414/how-to-do-3-table-join-in-update-query
    //Replace http://stackoverflow.com/questions/8627946/mysql-using-replace-with-a-join
//    REPLACE INTO lottery_user_referrals_stat(lottery_id, user_id, level, paid_in, paid_out) SELECT d.lottery_id, a.id, 1, SUM(paid), SUM(paid_out) FROM lottery_user as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = b.id group by a.id

    /**
     * Рефферальные начисления 1 - 3 уровней
     */
    public static function _paidOutRefs(BaseLottery $lottery)
    {
        $class      = __NAMESPACE__ . '\\L' . static::ID . 'Ticket';
        $refRate    = [];
        $refRate[1] = Yii::$app->keyStorage->get('user.account.ref_rate_1') / 100;
        $refRate[2] = Yii::$app->keyStorage->get('user.account.ref_rate_2') / 100;
        $refRate[3] = Yii::$app->keyStorage->get('user.account.ref_rate_3') / 100;
        $table      = $class::tableName();
        $params     = [
            ':id'      => $lottery->id,
            ':type'    => $lottery->type,
            ':refRate' => $refRate[1],
        ];
        //Реф статистика 1 уровня
        $sql        = "INSERT INTO {{%user_referrals_stat}}(lottery_id, user_id, lottery_type, level, ref_count, paid_in, paid_out, paid_ref) SELECT d.lottery_id, a.id, :type, 1, count(DISTINCT b.id), SUM(paid), SUM(paid_out), SUM(paid) * :refRate FROM {{%user}} as a INNER JOIN {{%user}} as b on a.referral_id = b.referrer_id inner join $table as d on d.user_id = b.id WHERE d.lottery_id = :id group by a.id";
        $res        = Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        //Реф статистика 2 уровня
        $sql                = "INSERT INTO {{%user_referrals_stat}}(lottery_id, user_id, lottery_type, level, ref_count, paid_in, paid_out, paid_ref) SELECT d.lottery_id, a.id, :type, 2, count(DISTINCT c.id), SUM(paid), SUM(paid_out), SUM(paid) * :refRate FROM {{%user}} as a INNER JOIN {{%user}} as b on a.referral_id = b.referrer_id inner join {{%user}} as c on b.referral_id = c.referrer_id inner join $table as d on d.user_id = c.id WHERE d.lottery_id = :id group by a.id";
        $params[':refRate'] = $refRate[2];
        $res                = $res + Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

        //Реф статистика 3 уровня
//        insert into lottery_user_referrals_stat(lottery_id, user_id, lottery_type, level, ref_count, paid_in, paid_out) SELECT d.lottery_id, a.id, '6x45', 3, count(DISTINCT e.id), SUM(paid), SUM(paid_out) FROM lottery_user as a INNER JOIN lottery_user as b on a.referral_id = b.referrer_id inner join lottery_user as c on b.referral_id = c.referrer_id inner join lottery_user as e on c.referral_id = e.referrer_id inner join lottery_l6x45_ticket as d on d.user_id = e.id where lottery_id = 7 group by a.id
        $sql                = "INSERT INTO {{%user_referrals_stat}}(lottery_id, user_id, lottery_type, level, ref_count, paid_in, paid_out, paid_ref) SELECT d.lottery_id, a.id, :type, 3, count(DISTINCT e.id), SUM(paid), SUM(paid_out), SUM(paid) * :refRate FROM {{%user}} as a INNER JOIN {{%user}} as b on a.referral_id = b.referrer_id inner join {{%user}} as c on b.referral_id = c.referrer_id inner join {{%user}} as e on c.referral_id = e.referrer_id inner join $table as d on d.user_id = e.id WHERE d.lottery_id = :id group by a.id";
        $params[':refRate'] = $refRate[3];
        $res                = $res + Yii::$app->db->createCommand($sql)->bindValues($params)->execute();

//        //Начисление реф выплат (без внесения в лог)
//        $params = [
//            ':refRate1' => $refRate[1],
//            ':refRate2' => $refRate[2],
//            ':refRate3' => $refRate[3],
//            ':type'     => $lottery->type,
//        ];
//        $sql    = "
//        UPDATE {{%user_profile}} AS a INNER JOIN
//            (SELECT
//                user_id,
//                sum(CASE level
//			WHEN 1 THEN paid_in*:refRate1
//                        WHEN 2 THEN paid_in*:refRate2
//                        WHEN 3 THEN paid_in*:refRate3
//                        ELSE 0
//                    END) AS paid
//            FROM {{%user_referrals_stat}}
//            WHERE lottery_id = {$lottery->id} AND lottery_type = :type
//            GROUP BY user_id) AS b
//        ON a.user_id = b.user_id
//        SET a.account = a.account + b.paid
//        ";
//        $res    = $res + Yii::$app->db->createCommand($sql)->bindValues($params)->execute();
        //Начисление с логгированием
        $params   = [
            ':refRate1' => $refRate[1],
            ':refRate2' => $refRate[2],
            ':refRate3' => $refRate[3],
            ':type'     => $lottery->type,
        ];
        $sql      = "
            SELECT
                user_id,
                sum(CASE level
			WHEN 1 THEN paid_in*:refRate1
                        WHEN 2 THEN paid_in*:refRate2
                        WHEN 3 THEN paid_in*:refRate3
                        ELSE 0
                    END) AS paid
            FROM {{%user_referrals_stat}}
            WHERE lottery_id = {$lottery->id} AND lottery_type = :type
            GROUP BY user_id";
        $payments = Yii::$app->db->createCommand($sql)->bindValues($params)->queryAll();
        foreach ($payments as $payment) {
            $userProfile = UserProfile::findOne($payment['user_id']);
            if ($userProfile) {
                $userProfile->AccountCharge($payment['paid'], 'Реферальные начисления');
                $userProfile->save();
            }
        }

        return $res;
    }

}
