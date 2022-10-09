<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\components\lottery\models\BaseLottery;
use common\components\lottery\models\L6x45;
//use common\components\lottery\models\L6x45Search;
use common\components\lottery\models\L6x45Draw;
use common\components\lottery\models\L6x45Ticket;
use yii\filters\AccessControl;
use common\components\lottery\widgets\models\BetForm;
use yii\web\ServerErrorHttpException;
use yii\web\HttpException;
use yii\helpers\Json;
use yii\bootstrap\Html;
//use Swift_Plugins_Loggers_ArrayLogger;
//use Swift_Plugins_LoggerPlugin;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use frontend\models\LCheckTicketForm;
use yii\base\Model;

/**
 * Lottery6x45Controller
 *
 * @author Mega
 */
class Lottery6x45Controller extends Controller
{

    public $layout = 'container';

//    const NAME = '6 out of 45';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only'  => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow'   => true,
                        'roles'   => ['?', '@'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $model = new BetForm();

        //Current lottery
        $lottery = L6x45::findCurrent();
        if (empty($lottery)) {
            throw new ServerErrorHttpException("Активная лотерея {$lotteryName} не найдена!");
        }

        $lotteryName        = L6x45::NAME;
        $flashMessageHeader = sprintf("<h3>Лотерея %s [тираж %d]</h3><hr class='kv-alert-separator'>", $lotteryName, $lottery->id);

        $userId = getMyId();
        $user   = Yii::$app->user->identity;
        if ($user) {
            BetForm::$paidLimit = $user->userProfile->account;
        }
        if ($model->load(Yii::$app->request->post()) && Yii::$app->user->can('user')) {
            $nextLottery = $lottery;
            $tickets     = [];
            for ($drawId = 0; $drawId <= $model->multidraw_count; $drawId++) {
                if (empty($model->bet)) {
                    continue;
                }
                try {
                    $ticket = new L6x45Ticket(['lottery_id' => $nextLottery->id, 'bet' => $model->bet, 'user_id' => $userId]);
                    if ($ticket->save()) {
                        $user->payLeader($ticket->lottery_id, L6x45Ticket::ID, $ticket->paid, $ticket->paid_out); //Нет paid_out
//                        $user->payReferrer($ticket->lottery_id, L6x45Ticket::ID, $ticket->paid, $ticket->paid_out); //Нет paid_out
                        $user->userProfile->account -= $ticket->paid;
                        $combination = implode(', ', Json::decode($model->bet));
                        $price       = Yii::$app->formatter->asCurrency($ticket->paid);
                        $tickets[]   = "Куплен билет № <strong>{$ticket->id}</strong> [тираж {$nextLottery->id}],<br />комбинация [{$combination}], <br />цена $price.<hr/>";
//                        Yii::$app->session->addFlash('info', "<h3>Лотерея 6 из 45 [тираж $lottery->id]</h3><hr class='kv-alert-separator'>Куплен билет № <strong>{$ticket->id}</strong>,<br />комбинация [{$combination}], <br />цена $price.");
                    }
                } catch (HttpException $ex) {
                    Yii::$app->session->addFlash('warning', "{$flashMessageHeader}Ошибка при оформлении билета");
                    Yii::error($ex->getMessage());
                }
//                catch (TypeError $ex) {
//                    Yii::$app->session->addFlash('warning', "{$flashMessageHeader}Ошибка при оформлении билета");
//                    Yii::error($ex->getMessage());
//                }
                $nextLottery = L6x45::findNext($nextLottery);
            }
            if (count($tickets)) {
                Yii::$app->session->addFlash('info', "<h3>Лотерея {$lotteryName}</h3><hr class='kv-alert-separator'>" . Html::ol($tickets, ['encode' => false]));
            }
        }
        elseif ($model->load(Yii::$app->request->post()) && Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('warning', "{$flashMessageHeader}Билет не оформлен.<br>" . Html::a('Войдите на сайт', ['/user/sign-in/login']) . ' или ' . Html::a('зарегистрируйтесь.', ['/user/sign-in/signup']));
        }
        if ($lottery instanceof BaseLottery) {
            $pricing    = Json::decode(Yii::$app->keyStorage->get('lottery.6x45.pricing'));
            $betDefault = Yii::$app->request->post('betDefault', null);
            if (!is_null($betDefault)) {
                Yii::$app->random->attributes = [
                    'count' => rand(6, 10),
                    'max'   => 45,
                ];
                $bet                          = Yii::$app->random->numbers;
            }
            else {
                $bet = null;
            }
            return $this->render('index', [
                        'model'      => $lottery,
                        'pricing'    => $pricing,
                        'betDefault' => $bet,
            ]);
        }
//        $this->redirect(['site/index']);
        throw new ServerErrorHttpException("Активная лотерея {$lotteryName} не найдена!");
    }

    /**
     * Displays a single Lottery model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
//        http://www.bootply.com/gOoa4S0W0Q
//        http://www.bootply.com/WLzR480I4G
//        http://www.bootply.com/5FP4ua1wcV
        $model        = L6x45::findOne($id);
        $dataProvider = new ArrayDataProvider([
            'allModels'  => $model->_wins_stat,
            'pagination' => false,
        ]);
        return $this->render('view', [
                    'model'        => $model,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Display lotteries list
     * @return type
     */
    public function actionList()
    {
        $query        = L6x45::find()
                ->where([
                    'not', ['draw' => null],
                ])
                ->andWhere([
            'enabled' => true,
        ]);
        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['draw_at' => SORT_DESC]],
            'pagination' => [ 'pageSize' => 6],
        ]);

        return $this->render('list', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     *
     * @return type
     */
    public function actionCheckTicket()
    {
        $model             = new LCheckTicketForm();
        $modelsCheckTicket = [];
        if ($model->load(Yii::$app->request->post())) {
            foreach (Yii::$app->request->post('LCheckTicketForm') as $key => $modelData) {
                if (isset($modelData['ticket_id'])) {
//                    $model->ticket_id = $modelData['ticket_id'];
                    $modelsCheckTicket[] = clone $model;
                }
            }
            if (Model::loadMultiple($modelsCheckTicket, Yii::$app->request->post()) && Model::validateMultiple($modelsCheckTicket)) {
                $tickets = array_map(function($val) {
                    return $val->ticket_id;
                }, $modelsCheckTicket);
                $query        = L6x45Ticket::find()->where(['id' => $tickets]);
                $dataProvider = new ActiveDataProvider([
                    'query' => $query,
                ]);
                return $this->render('tickets', [
//                            'model'        => $model,
                            'dataProvider' => $dataProvider,
                ]);
            }
        }
        elseif ($model->load(Yii::$app->request->get(), '') && $model->validate()) {
            return $this->render('tickets', [
                        'dataProvider' => new ArrayDataProvider(['allModels' => [L6x45Ticket::findOne($model->ticket_id)]]),
            ]);
        }
        //FIXME renderPartial for multiple tickets
//            if ($model->validate()) {
//                return $this->redirect(['/lottery' . $model->lottery_type . '/view_ticket', 'id' => $model->ticket_id]);
//            }
        //Fields collection
//        https://habrahabr.ru/post/239147/
//        http://www.yiiframework.com/wiki/666/handling-tabular-data-loading-and-validation-in-yii-2/
//        https://github.com/wbraganca/yii2-dynamicform
        //http://formvalidation.io/examples/adding-dynamic-field/
//        https://github.com/yiisoft/yii2/issues/9535
//        http://yiiframework.ru/forum/viewtopic.php?t=21698
        return $this->render('check_ticket_form', [
                    'model' => $model,
        ]);
    }

    /**
     * Display ticket info
     */
//    public function actionViewTicket($id)
//    {
//        return $this->render('viewTicket', [
////                    'model' => $this->findModel($id),
//        ]);
//    }

}
