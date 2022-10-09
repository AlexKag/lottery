<?php

namespace common\behaviors;

use yii\base\Behavior;
use Yii;
//use common\models\UserProfile;
use frontend\controllers\PaymentController;
use common\base\PaymentEvent;
use common\models\UserAccountStat;
use cheatsheet\Time;
use yii\helpers\Url;

/**
 * Проверка лимитов вывода средств и логгирование операций ввода\вывода
 * Class PayoutLimiterBehavior
 * @package common\behaviors
 */
class PayoutLimiterBehavior extends Behavior
{

    public $actionPostponed = '/payment/payout-postponed';

    /**
     * @return array
     */
    public function events()
    {
        return [
            PaymentController::EVENT_BEFORE_PAYOUT => 'checkLimits',
            PaymentController::EVENT_AFTER_PAYOUT => 'addStat',
            PaymentController::EVENT_AFTER_PAYMENT => 'addStat',
        ];
    }

    public function checkLimits(PaymentEvent $event)
    {
        if ($event->isValid) {
            $this->checkLimitOnce($event);
        }
        if ($event->isValid) {
            $this->checkLimitDay($event);
        }
        if (!$event->isValid) {
            $event->handled = true;
            $event->status  = UserAccountStat::STATUS_REQUESTED;
            $stat           = $this->addStat($event);
            return Yii::$app->getResponse()->redirect(Url::to([$this->actionPostponed,
                                'id' => $stat->id,
//                                'target' => $event->target,
//                                'amount' => $event->amount,
//                                'system' => $event->system,
            ]));
//            return $this->render('/payment/payment-postponed', [
//                        'target' => $event->target,
//                        'amount' => $event->amount,
//                        'system' => $event->system,
//            ]);
        } else {
            $event->status = UserAccountStat::STATUS_FINISHED;
//            $stat           = $this->addStat($event);
        }
    }

    public function checkLimitOnce(PaymentEvent &$event)
    {
        $event->isValid = (double) $event->amount <= Yii::$app->keyStorage->get('user.account.payout.limit.once');
        if (!$event->isValid) {
            $event->reason = 'Превышен единоразовый лимит на вывод денежных средств.';
            Yii::$app->session->setFlash('warning', Yii::t('common', 'Выполнение операции приостановлено. Превышен единоразовый лимит на вывод денежных средств.'));
        }
//        else {
//            $event->status = UserAccountStat::STATUS_FINISHED;
//        }
    }

    public function checkLimitDay(PaymentEvent &$event)
    {
        $sum            = UserAccountStat::find()
                ->where(['user_id' => $event->userProfile->user_id])
                ->andWhere(['direction' => 'out'])
                ->andWhere(['>', 'created_at', time() - Time::SECONDS_IN_A_DAY])
                ->andWhere(['status' => [
                        UserAccountStat::STATUS_APPROVED,
                        UserAccountStat::STATUS_FINISHED,
            ]])
                ->sum('amount');
        $sum += $event->amount;
        $event->isValid = $sum <= Yii::$app->keyStorage->get('user.account.payout.limit.day');
        if (!$event->isValid) {
            $event->reason = 'Превышен суточный лимит на вывод денежных средств.';
            Yii::$app->session->setFlash('warning', Yii::t('common', 'Выполнение операции приостановлено. Превышен суточный лимит на вывод средств.'));
//            $event->status = UserAccountStat::STATUS_REQUESTED;
        }
//        else {
//            $event->status = UserAccountStat::STATUS_FINISHED;
//        }
    }

    public function addStat(PaymentEvent $event)
    {
        $stat = new UserAccountStat([
            'user_id' => isset($event->userProfile->user_id) ? $event->userProfile->user_id : null,
            'direction' => $event->direction,
            'amount' => $event->amount,
            'target' => $event->target,
            'system' => $event->system,
            'status' => $event->status,
            'description' => $event->reason,
            'operation_id' => $event->operation_id,
        ]);
        $res  = $stat->save();
        return $stat;
    }

    /**
     *
     * @param integer $id Event id
     */
    public function updateStat($id, $status)
    {
        $stat         = UserAccountStat::findOne($id);
        $stat->status = $status;
        $stat->save();
        return $stat;
    }

}
