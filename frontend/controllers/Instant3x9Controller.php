<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\components\lottery\models\L3x9;
use common\components\lottery\models\L3x9Ticket;
use yii\filters\AccessControl;
use common\components\lottery\widgets\models\BetForm;
//use yii\web\ServerErrorHttpException;
use yii\web\HttpException;
//use yii\helpers\Json;
use yii\bootstrap\Html;

/**
 * Lottery3x9Controller
 *
 * @author Mega
 */
class Instant3x9Controller extends Controller
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
        $flashMessageHeader = sprintf("<h3>Мгновенная лотерея<br>%s</h3><hr class='kv-alert-separator'>", L3x9::NAME);
        $model              = new BetForm();

        $user   = isset(Yii::$app->user->identity->userProfile) ? Yii::$app->user->identity->userProfile : null;
        $userId = isset($user->user_id) ? $user->user_id : null;

        if ($model->load(Yii::$app->request->post()) && Yii::$app->user->can('user')) {
            try {
                $ticket = new L3x9Ticket(['bet' => $model->bet, 'user_id' => $userId, 'paid' => $model->paid, 'lottery_id' => 0]);
                if ($ticket->save()) {
                    $user->account -= $ticket->paid;
                    $ticket->lottery_id           = $ticket->id;
//                    $user->accountWithdraw($ticket->paid, 'Ставка в игре 3 из 9');
                    $price                        = Yii::$app->formatter->asCurrency($ticket->paid);
                    //Мгновенный розыгрыш
                    Yii::$app->random->attributes = L3x9::DRAW_CONFIG;
                    $res                          = Yii::$app->random->numbers;
                    sort($res);
                    $ticket->_win_combination     = $res;
                    $win                          = array_intersect($res, $ticket->_bet);
                    $res                          = implode(', ', $res);
                    switch (count($win)) {
                        case 1:
                            $ticket->paid_out = $ticket->paid / 2;
                            $ticket->win_cnt  = 1;
                            break;
                        case 2:
                            $ticket->paid_out = 3 / 2 * $ticket->paid;
                            $ticket->win_cnt  = 2;
                            break;
                        case 3:
                            $ticket->paid_out = 3 * $ticket->paid;
                            $ticket->win_cnt  = 3;
                            break;
                    }
                    Yii::$app->user->identity->payReferrer($ticket->lottery_id, L3x9Ticket::ID, $model->paid, $ticket->paid_out);
                    Yii::$app->user->identity->payLeader($ticket->lottery_id, L3x9Ticket::ID, $model->paid, $ticket->paid_out);
                    if ($ticket->paid_out > 0) {
//                        $user->accountCharge($ticket->paid_out, false, 'Выигрыш по ставке в мгновенной лотерее 3 из 9');
                        $ticket->paidOut();
                        $user->account += $ticket->paid_out;
                        $val  = Yii::$app->formatter->asCurrency($ticket->paid_out);
                        $bet  = implode(', ', $ticket->_bet);
                        $sent = Yii::t('frontend', 'Вы угадали {nums,plural,=1{# число} other{# числа}}', ['nums' => $ticket->win_cnt]);
                        Yii::$app->session->addFlash('info', "{$flashMessageHeader}<p class='text-primary'>{$sent}! <br>Выиграли числа [{$res}].<br>Ваша ставка [{$bet}].<hr>Поздравляем! <br>Ваш выигрыш составил {$val}.</p>");
                    } else {
                        Yii::$app->session->addFlash('warning', "{$flashMessageHeader}<p class='text-primary'>Вы не угадали ни одного числа! <br>Выиграли числа [{$res}]! <hr>Вам обязательно повезет в другой раз! Удачи!</p>");
                    }
//                    if (!($user->save() && $ticket->save())) {
                    if (!$ticket->save()) {
                        Yii::warning('Ошибка сохранения результатов мгновенной лотереи ' . L3x9::NAME);
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
            Yii::$app->random->attributes = L3x9::DRAW_CONFIG;
            $bet                          = Yii::$app->random->numbers;
        } else {
            $bet = null;
        }

        return $this->render('index', [
                    'gameName' => Yii::t('common', L3x9Ticket::NAME),
                    'betDefault' => $bet,
                    'betMax' => ($userId > 0) ? min([$user->account, BetForm::$paidLimit]) : 0,
        ]);
    }

}
