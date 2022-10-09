<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use common\components\slots\widgets\models\FiveBetForm as BetForm;
use common\components\slots\models\SlotsFiveTicket;
use yii\helpers\Html;

//use yii\web\ServerErrorHttpException;
//use yii\web\HttpException;
//use yii\helpers\Json;

/**
 * Lottery3x9Controller
 *
 * @author Mega
 */
class SlotsFiveController extends Controller
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
                'only' => ['index', 'draw'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                    [
                        'actions' => ['draw'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {

        return $this->render('index', [
                    'gameName' => 'Пятерочка',
        ]);
    }

    public function actionDraw()
    {
        $model                      = new BetForm();
        $userId                     = getMyId();
        $user                       = isset(Yii::$app->user->identity->userProfile) ? Yii::$app->user->identity->userProfile : null;
        $data                       = [];
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post(), '') && Yii::$app->user->can('user')) {
            $ticket = new SlotsFiveTicket(['_bet' => $model->attributes, 'user_id' => $userId, 'lottery_id' => null]);
            $ticket->draw(Yii::$app->keyStorage->get('slots.five.return_efficiency'), (int)Yii::$app->keyStorage->get('slots.five.return_efficiency.time'));
            if ($ticket->save()) {
                $ticket->lottery_id = $ticket->id;
                $ticket->save();
                Yii::$app->user->identity->payReferrer($ticket->lottery_id, SlotsFiveTicket::ID, $ticket->paid, $ticket->paid_out);
                Yii::$app->user->identity->payLeader($ticket->lottery_id, SlotsFiveTicket::ID, $ticket->paid, $ticket->paid_out);
                $data               = $ticket->attributes;
                if ($ticket->paid_out > 0) {
                    $ticket->paidOut();
                    $user->account += $ticket->paid_out;
                }
                $user->account -= $ticket->paid;
            } else {
//                $data = $model->attributes;
                $data = $ticket->errors;
                Yii::warning('Ошибка сохранения розыгрыша слотов ' . SlotsFiveTicket::NAME . print_r($data));
            }
        } else {
            return [];
        }
        return [
//            'draw' => [10,0,0,0,1],
            'draw' => $ticket->_bet['draw'],
            'account' => $user->account,
            'win' => $ticket->paid_out,
            'uid' => $userId,
            'data' => $data,
            'growl' => [
                'title' => Html::tag('h3', "Мгновенная лотерея<br>" . SlotsFiveTicket::NAME) . '<hr />',
//                'icon'          => 'glyphicon glyphicon-ok-sign',
                'message' => $ticket->paid_out ? 'Поздравляем! Ваш выигрыш составил ' . Yii::$app->formatter->asCurrency($ticket->paid_out) . '<br>Выиграли линии: ' . implode(', ', array_keys($ticket->_win_combination)) . '.' : 'Выигрыш отсутствует. Вам обязательно повезет в другой раз! <br>Удачи!',
                'showSeparator' => true,
//                'delay' => 2000,
            ]
        ];
    }

    public function actionTest()
    {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $ticket                     = new SlotsFiveTicket(['_bet' => ['betPerLine' => 1, 'linesCount' => 1, 'denomination' => 1], 'user_id' => getMyId(), 'lottery_id' => null]);
        print_r($ticket->expandDraw([1, 2, 3, 4, 5]));
        $lines                      = $ticket->linesDraw($ticket->lines, 5);
        print_r($lines);
        foreach ($lines as $line) {
            print_r($ticket->normLine($line));
        }
    }

}
