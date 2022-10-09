<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\components\lottery\models\L1x3;
use common\components\lottery\models\L1x3Ticket;
use yii\filters\AccessControl;
use common\components\lottery\widgets\models\BetForm;
//use yii\web\ServerErrorHttpException;
use yii\web\HttpException;
//use yii\helpers\Json;
use yii\bootstrap\Html;

/**
 * Lottery1x3Controller
 *
 * @author Mega
 */
class Instant1x3Controller extends Controller
{

    public $layout = 'container';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $flashMessageHeader = sprintf("<h3>Мгновенная лотерея<br>%s</h3><hr class='kv-alert-separator'>", L1x3::NAME);
        $model              = new BetForm();

        $user   = isset(Yii::$app->user->identity->userProfile) ? Yii::$app->user->identity->userProfile : null;
        $userId = isset($user->user_id) ? $user->user_id : null;
        if ($model->load(Yii::$app->request->post()) && Yii::$app->user->can('user')) {
            try {
                $ticket = new L1x3Ticket(['bet' => $model->bet, 'user_id' => $userId, 'paid' => $model->paid, 'lottery_id' => 0]);
                if ($ticket->save()) {
                    $user->account -= $ticket->paid;
                    $ticket->lottery_id           = $ticket->id;
//                    $user->accountWithdraw($ticket->paid, 'Ставка в игре 1 из 3');
                    $bet                          = $ticket->_bet;
                    $bet                          = array_shift($bet);
                    $price                        = Yii::$app->formatter->asCurrency($ticket->paid);
//Мгновенный розыгрыш
                    Yii::$app->random->attributes = L1x3::DRAW_CONFIG;
                    $res                          = Yii::$app->random->number;
                    $ticket->_win_combination     = [$res];
                    if ($res == $bet && $bet > 0 && $bet <= 3) {
                        $ticket->paid_out = 2 * $ticket->paid;
                        $ticket->win_cnt  = 1;
                        $ticket->paidOut();
                        $user->account += $ticket->paid_out;
//                        $user->accountCharge($ticket->paid_out, false, 'Выигрыш по ставке в мгновенной лотерее 1 из 3');
                        $val              = Yii::$app->formatter->asCurrency($ticket->paid_out);
                        Yii::$app->session->addFlash('info', "{$flashMessageHeader}<p class='text-primary'>Вы угадали число! Поздравляем! Ваш выигрыш составил {$val}.</p>");
                    } else {
                        Yii::$app->session->addFlash('warning', "{$flashMessageHeader}<p class='text-primary'>Вы не угадали! Выиграло число {$res}! Вам обязательно повезет в другой раз! Удачи!</p>");
                    }
                    Yii::$app->user->identity->payReferrer($ticket->lottery_id, L1x3Ticket::ID, $model->paid, $ticket->paid_out);
                    Yii::$app->user->identity->payLeader($ticket->lottery_id, L1x3Ticket::ID, $model->paid, $ticket->paid_out);
                    if (!$ticket->save()) {
//if (!($user->save() && $ticket->save())) {
                        Yii::warning('Ошибка сохранения результатов мгновенной лотереи ' . L1x3::NAME);
                    }
                }
            } catch (HttpException $ex) {
                Yii::$app->session->addFlash('warning', "{$flashMessageHeader}Ошибка при оформлении билета");
//TODO Записать в лог событие
            }
        } elseif ($model->load(Yii::$app->request->post()) && Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('warning', "{$flashMessageHeader}Билет не оформлен.<br>" . Html::a('Войдите на сайт', ['/user/sign-in/login']) . ' или ' . Html::a('зарегистрируйтесь.', ['/user/sign-in/signup']));
        }

        $betDefault = Yii::$app->request->post('betDefault', null);
        if (!is_null($betDefault)) {
            Yii::$app->random->attributes = L1x3::DRAW_CONFIG;
            $bet                          = Yii::$app->random->numbers;
        } else {
            $bet = null;
        }

        return $this->render('index', [
                    'gameName' => Yii::t('common', L1x3Ticket::NAME),
                    'betDefault' => $bet,
                    'betMax' => ($userId > 0) ? min([$user->account, BetForm::$paidLimit]) : 0,
        ]);
    }

}
