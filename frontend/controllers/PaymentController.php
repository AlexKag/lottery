<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UnprocessableEntityHttpException;
use frontend\models\PaymentForm;
use frontend\models\PayoutForm;
use yii\filters\AccessControl;
use yiidreamteam\perfectmoney\events\GatewayEvent as PmGatewayEvent;
use yiidreamteam\perfectmoney\Api as PmApi;
//use yiidreamteam\payeer\Api as PrApi;
use common\components\payment\payeer\Api as PrApi;
use common\components\payment\fchange\events\GatewayEvent as FchangeGatewayEvent;
use common\components\payment\fchange\Api as FchangeApi;
use yiidreamteam\payeer\events\GatewayEvent as PrGatewayEvent;
use yii\base\InvalidConfigException;
use common\models\payment\PerfectMoneyTransaction;
use common\models\payment\PayeerTransactionResult;
use yii\helpers\Url;
//use common\models\UserProfile;
use yii\helpers\VarDumper;
use yii\helpers\ArrayHelper;
use cheatsheet\Time;
use common\models\UserToken;
use common\models\UserAccountStat;
use common\base\PaymentEvent;
use common\behaviors\PayoutLimiterBehavior;
use yii\helpers\Html;

class PaymentController extends Controller
{

//    const EVENT_BEFORE_PAYOUT_CHECK = 'event_before_payout_check';
    const EVENT_BEFORE_PAYOUT  = 'event_before_payout';
    const EVENT_AFTER_PAYOUT   = 'event_after_payout';
    const EVENT_BEFORE_PAYMENT = 'event_before_payment';
    const EVENT_AFTER_PAYMENT  = 'event_after_payment';

    public $layout               = 'container';
    public $enableCsrfValidation = false;

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
//                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index', 'pm-success', 'pm-failure', 'pr-success', 'pr-failure', 'payout', 'fchange-success', 'fchange-failure', 'payout-postponed'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['pm-status', 'pr-status', 'fchange-status'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ]
            ],
            'accountWithdrawLimiter' => [
                'class' => PayoutLimiterBehavior::className(),
            ]
        ];
    }

    public function init()
    {
        parent::init();
        /** @var Api $pm */
        try {
            $pm = Yii::$app->pm;
            $pm->on(PmGatewayEvent::EVENT_PAYMENT_REQUEST, [$this, 'handlePmPaymentRequest']);
            $pm->on(PmGatewayEvent::EVENT_PAYMENT_SUCCESS, [$this, 'handlePmPaymentSuccess']);
        } catch (\Exception $ex) {
            Yii::warning($ex);
        }
        try {
            $pr = Yii::$app->payeer;
            $pr->on(PrGatewayEvent::EVENT_PAYMENT_REQUEST, [$this, 'handlePrPaymentRequest']);
            $pr->on(PrGatewayEvent::EVENT_PAYMENT_SUCCESS, [$this, 'handlePrPaymentSuccess']);
        } catch (\yii\base\InvalidConfigException $ex) {
            Yii::warning($ex);
        }
        try {
            $fchange = Yii::$app->fchange;
            $fchange->on(FchangeGatewayEvent::EVENT_PAYMENT_REQUEST, [$this, 'handleFchangePaymentRequest']);
            $fchange->on(FchangeGatewayEvent::EVENT_PAYMENT_SUCCESS, [$this, 'handleFchangePaymentSuccess']);
        } catch (yii\base\ErrorException $ex) {
            Yii::warning($ex);
        }
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $model                     = new PaymentForm;
        $send_paysys_identificator = null; //?????????????????????????? ?????????????????? ?????????????? ?????? f-change
//Add F-change payment methods
        try {
            PaymentForm::$methods += Yii::$app->fchange->methods;
        } catch (\yii\base\InvalidConfigException $e) {
            Yii::error($e);
        } catch (\Exception $e) {
            Yii::error($e);
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            print_r($model);die;
            switch ($model->method) {
                case PaymentForm::PAYMENT_PERFECTMONEY:
                    $view                      = 'pm_redirect';
                    $token                     = UserToken::create(
                                    getMyId(), UserToken::TYPE_PAYMENT_PERFECTMONEY, Time::SECONDS_IN_A_DAY, UserToken::PAYMENT_TOKEN_LENGTH, $model->amount
                    );
                    break;
                case PaymentForm::PAYMENT_PAYEER:
                    $view                      = 'pr_redirect';
                    $token                     = UserToken::create(
                                    getMyId(), UserToken::TYPE_PAYMENT_PAYEER, Time::SECONDS_IN_A_DAY, UserToken::PAYMENT_TOKEN_LENGTH, $model->amount
                    );
                    break;
//                case PaymentForm::PAYMENT_BITCOIN:
//                    $view                      = PaymentForm::PAYMENT_BITCOIN;
//                    $token                     = UserToken::create(
//                                    getMyId(), UserToken::TYPE_PAYMENT_BITCOIN, Time::SECONDS_IN_A_DAY, UserToken::PAYMENT_TOKEN_LENGTH
//                    );
//                    break;
                case PaymentForm::PAYMENT_FCHANGE_ADVCUSD:
                case PaymentForm::PAYMENT_FCHANGE_CARDRUB:
                case PaymentForm::PAYMENT_FCHANGE_OKUSD:
                case PaymentForm::PAYMENT_FCHANGE_QWRUB:
                case PaymentForm::PAYMENT_FCHANGE_YAMRUB:
                    $view                      = 'fchange_redirect';
                    $token                     = UserToken::create(
                                    getMyId(), UserToken::TYPE_PAYMENT_FCHANGE, Time::SECONDS_IN_A_WEEK, UserToken::PAYMENT_TOKEN_LENGTH, $model->amount
                    );
                    $send_paysys_identificator = $model->method;
                    break;
                default :
                    //?????????????? ???????????? F-change
                    if (array_key_exists($model->method, Yii::$app->fchange->methods)) {
                        $view                      = 'fchange_redirect';
                        $token                     = UserToken::create(
                                        getMyId(), UserToken::TYPE_PAYMENT_FCHANGE, Time::SECONDS_IN_A_DAY, UserToken::PAYMENT_TOKEN_LENGTH, $model->amount
                        );
                        $send_paysys_identificator = $model->method;
                    } else {
                        $view  = 'pm_redirect';
                        $token = UserToken::create(
                                        getMyId(), UserToken::TYPE_PAYMENT_PERFECTMONEY, Time::SECONDS_IN_A_DAY, UserToken::PAYMENT_TOKEN_LENGTH, $model->amount
                        );
                    }
            }

            $model->token = $token->token;
            Yii::info($model->attributes, 'payment\\' . $model->method);

            return $this->render($view, [
                        'amount' => $model->amount,
                        'id' => $model->token,
                        'send_paysys_identificator' => $send_paysys_identificator,
                        'public_id' => Yii::$app->user->identity->publicIdentity,
                        'username' => Yii::$app->user->identity->username,
            ]);
//            return $this->redirect([$action]);
        }


        return $this->render('index', [
                    'model' => $model,
                    'methods' => PaymentForm::$methods,
        ]);
    }

//http://frontend.lottery/payment/pm-status
    public function actionPmStatus()
    {
        $this->layout = false;

        $api = Yii::$app->pm;
        if (!$api instanceof PmApi) {
            Yii::error(Yii::$app->pm, 'payment\pm\status\error');
            throw new InvalidConfigException('Invalid PerfectMoney component configuration');
        }
        try {
            $api->processResult(\Yii::$app->request->post());
        } catch (\Exception $e) {
            Yii::error($e, 'payment\pm\status\error');
            throw $e;
        }
    }

    public function actionPmSuccess()
    {
        Yii::info('?????????????????? ???????????? ?????????? ?????????????? Perfect Money', 'payment\pm\success');

        $model = new PerfectMoneyTransaction;
        if ($model->load(Yii::$app->request->post(), '')) {
            return $this->render('success', [
                        'model' => $model,
                        'account' => Yii::$app->user->identity->userProfile->account,
            ]);
        }
        return Yii::$app->response->redirect(Url::to('/user/default/finance'));
    }

    public function actionPmFailure()
    {
        Yii::info('???????????? ?????????? ?????????????? Perfect Money ???? ??????????????????', 'payment\pm\failure');
        return $this->render('failure');
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handlePmPaymentRequest($event)
    {
//        $invoice = Invoice::findOne(ArrayHelper::getValue($event->gatewayData, 'PAYMENT_ID'));
//
//        if (!$invoice instanceof Invoice ||
//                $invoice->status != Invoice::STATUS_NEW ||
//                ArrayHelper::getValue($event->gatewayData, 'PAYMENT_AMOUNT') != $invoice->amount ||
//                ArrayHelper::getValue($event->gatewayData, 'PAYEE_ACCOUNT') != \Yii::$app->get('pm')->walletNumber
//        )
//            return;

        $debugData      = VarDumper::dumpAsString($event->gatewayData);
        Yii::info($debugData, 'payment\pm\handlePmPaymentRequest');
        $event->invoice = $event->gatewayData;
        $event->handled = true;
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handlePmPaymentSuccess($event)
    {
        $invoice = $event->invoice;
        $token   = UserToken::find()
                ->byType(UserToken::TYPE_PAYMENT_PERFECTMONEY)
                ->byToken($invoice['PAYMENT_ID'])
                ->notExpired()
                ->one();
        if ($token) {
            $deltaAmount = abs($token->message - $invoice['PAYMENT_AMOUNT']);
            $deltaAmount = empty($token->message) ? 0 : $deltaAmount; //FIXME ???????????????? ?????????????????????????? ????????????????. ???? ?????????? ?????? ???????????????????? ?????????? 06.02.17
            if ($deltaAmount < 1) {
                $userProfile = $token->user->userProfile;
                $userProfile->AccountCharge($invoice['PAYMENT_AMOUNT'] * (YII_ENV == 'dev' ? 100000 : 1), 'Perfect Money incoming payment'); //FIXME Remove 100000
                if ($userProfile->save()) {
                    $ev = new PaymentEvent([
                        'amount' => $invoice['PAYMENT_AMOUNT'],
                        'reason' => VarDumper::dumpAsString($invoice),
                        'userProfile' => $userProfile,
                        'system' => PaymentForm::$methods[PaymentForm::PAYMENT_PERFECTMONEY],
                        'target' => isset($invoice['PAYER_ACCOUNT']) ? $invoice['PAYER_ACCOUNT'] : null, //TODO add target account
                        'status' => UserAccountStat::STATUS_FINISHED,
                        'direction' => UserAccountStat::DIRECTION_IN,
                    ]);
                    $this->trigger(self::EVENT_AFTER_PAYMENT, $ev);
                    $token->delete();
                    Yii::info("???????? ???????????????????????? [{$userProfile->user_id}] ???????????????? ???? {$invoice['PAYMENT_AMOUNT']} {$invoice['PAYMENT_UNITS']}.", 'payment\pm\handlePmPaymentSuccess');
                } else {
                    Yii::error("???????????? ???????????????????? ?????????? ???????????????????????? [{$userProfile->user_id}]", 'payment\pm\handlePmPaymentSuccess');
                }
            } else {
                Yii::error("???????????????????? ?????????? ????????????????????. ??????????????????: {$token->message}, ???????????????? ?? ??????????????: {$invoice['amount']}. ?????????????????????????? ???????????? {$invoice['amount']} {$invoice['payed_paysys']} ????????????????. ?????????? ???????????????? [{$invoice['payment_num']}].", 'payment\fchange\handleFchangePaymentSuccess');
            }
        } else {
            Yii::error("?????????????????????????? ???????????? {$invoice['PAYMENT_AMOUNT']} {$invoice['PAYMENT_UNITS']} ????????????????. ?????????? ???????????????? [{$invoice['PAYMENT_ID']} ???? ???????????? ?????? ??????????????????.", 'payment\pm\handlePmPaymentSuccess');
        }
    }

    public function actionPrStatus()
    {
        $this->layout = false;

//        Yii::info('Status', 'payment\payeer\status');
        try {
            $api = Yii::$app->payeer;
        } catch (\Exception $e) {
            Yii::info($e, 'payment\payeer\status\error');
        }
//            Yii::info('erra', 'payment\payeer\status\error');
//        Yii::info(VarDumper::dump(Yii::$app->payeer, 3), 'payment\payeer\status\error');
        if (!$api instanceof PrApi) {
            Yii::error(Yii::$app->payeer, 'payment\payeer\status\error');
            throw new InvalidConfigException('Invalid Payeer component configuration');
        }
        try {
            if ($api->processResult(\Yii::$app->request->post())) {
                echo Yii::$app->request->post('m_orderid') . '|success';
                return;
            }
        } catch (\Exception $e) {
            Yii::error($e, 'payment\payeer\status\error');
            throw $e;
        }
        echo Yii::$app->request->post('m_orderid') . '|error';
    }

    public function actionPrSuccess()
    {
//        Yii::info('?????????????????? ???????????? ?????????? ?????????????? Payeer', 'payment\payeer\success');
//        echo '?????????????????? ???????????? ?????????? ?????????????? Payeer';
        $model = new PayeerTransactionResult;
        $resp  = Yii::$app->request->get();
        $data  = [
            'PAYEE_ACCOUNT' => $resp['m_shop'],
            'PAYMENT_ID' => $resp['m_orderid'],
            'PAYMENT_AMOUNT' => $resp['m_amount'],
            'PAYMENT_UNITS' => $resp['m_curr'],
            'PAYMENT_BATCH_NUM' => $resp['m_operation_id'],
            'TIMESTAMPGMT' => \DateTime::createFromFormat('d.m.Y G:i:s', $resp['m_operation_pay_date']),
            'SUGGESTED_MEMO' => base64_decode($resp['m_desc'])
        ];
        if ($model->load($data, '')) {
            return $this->render('success', [
                        'model' => $model,
                        'account' => Yii::$app->user->identity->userProfile->account,
            ]);
        }
        return Yii::$app->response->redirect(Url::to('/user/default/finance'));
    }

    public function actionPrFailure()
    {
        Yii::info('???????????? ?????????? ?????????????? Payeer ???? ??????????????????', 'payment\payeer\failure');
        return $this->render('failure');
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handlePrPaymentRequest($event)
    {
        $debugData      = VarDumper::dumpAsString($event->gatewayData);
        Yii::info($debugData, 'payment\payeer\handlePrPaymentRequest');
        $event->invoice = $event->gatewayData;
        $event->handled = true;
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handlePrPaymentSuccess($event)
    {
        $invoice = $event->invoice;
        $token   = UserToken::find()
                ->byType(UserToken::TYPE_PAYMENT_PAYEER)
                ->byToken($invoice['m_orderid'])
                ->notExpired()
                ->one();
        if ($token) {
            $deltaAmount = abs($token->message - $invoice['m_amount']);
            $deltaAmount = empty($token->message) ? 0 : $deltaAmount; //FIXME ???????????????? ?????????????????????????? ????????????????. ???? ?????????? ?????? ???????????????????? ?????????? 06.02.17
            if ($deltaAmount < 1) {
                $userProfile = $token->user->userProfile;
                $userProfile->AccountCharge($invoice['m_amount'] * (YII_ENV == 'dev' ? 100000 : 1), 'Payeer incoming payment'); //FIXME Remove 100000
                if ($userProfile->save()) {
                    $ev = new PaymentEvent([
                        'amount' => $invoice['m_amount'],
                        'reason' => VarDumper::dumpAsString($invoice),
                        'userProfile' => $userProfile,
                        'system' => PaymentForm::$methods[PaymentForm::PAYMENT_PAYEER],
                        'target' => isset($invoice['client_account']) ? $invoice['client_account'] : null, //TODO add target account
                        'status' => UserAccountStat::STATUS_FINISHED,
                        'direction' => UserAccountStat::DIRECTION_IN,
                    ]);
                    $this->trigger(self::EVENT_AFTER_PAYMENT, $ev);
                    $token->delete();
                    Yii::info("???????? ???????????????????????? [{$userProfile->user_id}] ???????????????? ???? {$invoice['m_amount']} {$invoice['m_curr']}.", 'payment\payeer\handlePrPaymentSuccess');
                } else {
                    Yii::error("???????????? ???????????????????? ?????????? ???????????????????????? [{$userProfile->user_id}]", 'payment\pm\handlePrPaymentSuccess');
                }
            }
        } else {
            Yii::error("?????????????????????????? ???????????? {$invoice['m_amount']} {$invoice['m_curr']} ????????????????. ?????????? ???????????????? [{$invoice['m_orderid']}] ???? ???????????? ?????? ??????????????????.", 'payment\payeer\handlePrPaymentSuccess');
        }
    }

    public function actionFchangeSuccess()
    {
        Yii::$app->session->addFlash('info', '???????????? ????????????????. ?? ???????????????????? ?????????? ???????????????? ?????????? ?????????????????? ???? ?????? ???????? ' . Yii::$app->name . '.');
        Yii::info('???????????????????????? ???????????????? ?????????????????? ????????????.', 'payment\fchange\success');
        return Yii::$app->response->redirect(Url::to('/user/default/finance'));
    }

    public function actionFchangeFailure()
    {
        Yii::$app->session->addFlash('warning', '???????????? ???? ??????????????????.');
        Yii::info('???????????? ???????????????? ??????????????????????????.', 'payment\fchange\failure');
        return Yii::$app->response->redirect(Url::to('/user/default/finance'));
    }

    public function actionFchangeStatus()
    {
        $this->layout = false;

//        Yii::info(VarDumper::dumpAsString(Yii::$app->request), 'payment\fchange\status');
        $api = Yii::$app->fchange;
        if (!$api instanceof FchangeApi) {
            Yii::error(Yii::$app->fchange, 'payment\fchange\status\error');
            throw new InvalidConfigException('Invalid F-Change component configuration');
        }
        try {
            return $api->processResult(\Yii::$app->request->post());
//            return $api->processResult(
//                            [
//                                'merchant_name' => 'yandexmoney2',
//                                'merchant_title' => 'FreedomLOTTO',
//                                'payed_paysys' => 'QWRUB',
//                                'amount' => 0.01,
//                                'payment_info' => '???????????????????? ?????????? FreedomLOTTO ???????????????????????? test.fchange',
//                                'payment_num' => 'eIn71a-WQAJAbSJb8jHOgOad9SrK0kNb',
//                                'sucess_url' => 'http://freedom-lotto.com/payment/fchange-success',
//                                'error_url' => 'http://freedom-lotto.com/payment/fchange-failure',
//                                'obmen_order_id' => 5490,
//                                'obmen_recive_valute' => 'usd',
//                                'obmen_timestamp' => 1484681766,
//                                'verificate_hash' => '5aa6dbb3ff43dfe1ebc25133a5e77842c4a4c87cd327f2746b56766686bc8b4f',
//                            ]
//            );
        } catch (\Exception $e) {
            Yii::error($e, 'payment\fchange\status\error');
//            throw $e;
        }
        return 0;
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handleFchangePaymentRequest($event)
    {
        $debugData = VarDumper::dumpAsString($event->gatewayData);
        if (!key_exists('verificate_hash', $event->gatewayData) || !array_key_exists('payment_num', $event->gatewayData)) {
            Yii::error($debugData, 'payment\fchange\handleFchangePaymentRequest');
            $event->handled = false;
        } else {
            Yii::info($debugData, 'payment\fchange\handleFchangePaymentRequest');
            $event->handled = true;
        }
        $event->invoice = $event->gatewayData;
    }

    /**
     * @param GatewayEvent $event
     * @return bool
     */
    public function handleFchangePaymentSuccess($event)
    {
        $invoice = $event->invoice;
        $token   = UserToken::find()
                ->byType(UserToken::TYPE_PAYMENT_FCHANGE)
                ->byToken($invoice['payment_num'])
                ->notExpired()
                ->one();
        if ($token) {
            $deltaAmount = abs($token->message - $invoice['amount']);
            $deltaAmount = empty($token->message) ? 0 : $deltaAmount; //FIXME ???????????????? ?????????????????????????? ????????????????. ???? ?????????? ?????? ???????????????????? ?????????? 06.02.17
            if ($deltaAmount < 1) {
                $userProfile = $token->user->userProfile;
                $userProfile->AccountCharge($invoice['amount'] * (YII_ENV == 'dev' ? 100000 : 1), 'F-Change incoming payment'); //FIXME Remove 100000
                if ($userProfile->save()) {
                    $ev = new PaymentEvent([
                        'amount' => $invoice['amount'],
                        'reason' => VarDumper::dumpAsString($invoice),
                        'system' => 'F-Change:' . $invoice['payed_paysys'],
                        'userProfile' => $userProfile,
                        'operation_id' => $invoice['obmen_order_id'],
                        'status' => UserAccountStat::STATUS_FINISHED,
                        'direction' => UserAccountStat::DIRECTION_IN,
                    ]);
                    $this->trigger(self::EVENT_AFTER_PAYMENT, $ev);
                    $token->delete();
                    Yii::info("???????? ???????????????????????? [{$userProfile->user_id}] ???????????????? ???? {$invoice['amount']} {$invoice['obmen_recive_valute']} ?????????? {$invoice['payed_paysys']}.", 'payment\fchange\handleFchangePaymentSuccess');
                } else {
                    Yii::error("???????????? ???????????????????? ?????????? ???????????????????????? [{$userProfile->user_id}]", 'payment\fchange\handleFchangePaymentSuccess');
                }
            } else {
                Yii::error("???????????????????? ?????????? ????????????????????. ??????????????????: {$token->message}, ???????????????? ?? ??????????????: {$invoice['amount']}. ?????????????????????????? ???????????? {$invoice['amount']} {$invoice['obmen_recive_valute']} ?????????? {$invoice['payed_paysys']} ????????????????. ?????????? ???????????????? [{$invoice['payment_num']}].", 'payment\fchange\handleFchangePaymentSuccess');
            }
        } else {
            Yii::error("?????????????????????????? ???????????? {$invoice['amount']} {$invoice['obmen_recive_valute']} ?????????? {$invoice['payed_paysys']} ????????????????. ?????????? ???????????????? [{$invoice['payment_num']}] ???? ???????????? ?????? ??????????????????.", 'payment\fchange\handleFchangePaymentSuccess');
        }
    }

    /**
     * @return string
     */
    public function actionPayout()
    {
//        print_r($this->actionPostponed);die;
        $handling_fee = Yii::$app->keyStorage->get('payment.handling_fee');
        $account      = isset(Yii::$app->user->identity) ? Yii::$app->user->identity->userProfile->account : 0;
        $amountMax    = $account / (1 + $handling_fee / 100);
        $amountMax    = number_format($amountMax, 2, '.', '');
        $model        = new PayoutForm(['amountMax' => $amountMax]);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $this->actionPostponed = '/payment/payout-postponed';
            //???????????????? ??????????????
//            $event                 = new PaymentEvent([
//                'amount'      => $model->amount * (YII_ENV == 'dev' ? 100000 : 1),
//                'reason'      => 'Before payout',
//                'userProfile' => Yii::$app->user->identity->userProfile,
//                'system'      => isset(PaymentForm::$methods[$model->method]) ? PaymentForm::$methods[$model->method] : $model->method,
//                'direction'   => UserAccountStat::DIRECTION_OUT,
//                'status'      => UserAccountStat::STATUS_REQUESTED,
//                'target'      => $model->toAccount,
//            ]);
//            $this->trigger(self::EVENT_BEFORE_PAYOUT_CHECK, $event);

            $result = null;
            switch ($model->method) {
                case PaymentForm::PAYMENT_PERFECTMONEY:
                    try {
                        $balance = Yii::$app->pm->balance();
                        if (isset($balance['ERROR'])) {
                            throw new \Exception($balance['ERROR']);
                        }
                        $amountMax = isset($balance[Yii::$app->pm->walletNumber]) ? $balance[Yii::$app->pm->walletNumber] * (YII_ENV == 'dev' ? 100000 : 1) : null;
                        if ($model->amount <= $amountMax) {
                            $res = $this->handlePmPayout($model->toAccount, $model->amount);
                            if (isset($res['PAYMENT_BATCH_NUM'])) {
                                $result = ['PAYMENT_BATCH_NUM' => $res['PAYMENT_BATCH_NUM']];
                            } else {
                                $result['error'] = ArrayHelper::getValue($res, 'error', '???????????? ???????????? ??????????????');
                                Yii::error($res, 'payment\\' . $model->method . '\payout\error');
                            }
                        } else {
                            $message         = sprintf('???????????????????????? ?????????????? ?????? ?????????????? ???????????? [%d]. ??????????????????: %s. ????????????????: %s', getMyId(), $model->amount, $amountMax);
                            Yii::error($message, 'payment\\' . $model->method . '\payout\error');
                            $message         = sprintf('???? ???????????? ???????????? ?????????? ?????????????? ???? ?????????? %s. ?????????????????? ???????????????? ??????????. ???????????????? ???? ????????????????????.', Yii::$app->formatter->asCurrency($amountMax));
                            $result['error'] = $message;
//                            Yii::$app->session->addFlash('warning', $message);
                        }
                        $balance = Yii::$app->pm->balance();
//                $balance['walletNumber'] ?????????????? ?????????? ???? ??????????
                        Yii::info($balance, 'payment\\' . $model->method . '\\balance');
                    } catch (\Exception $e) {
                        Yii::error($e, 'payment\pm');
                        Yii::error($e);
                        $result          = ArrayHelper::toArray($e);
                        $result['error'] = $e->getMessage();
                    }
                    break;
                case PaymentForm::PAYMENT_PAYEER:
                    try {
                        $balance   = Yii::$app->payeer->balance();
                        $amountMax = isset($balance['balance']['USD']['DOSTUPNO_SYST']) ? $balance['balance']['USD']['DOSTUPNO_SYST'] * (YII_ENV == 'dev' ? 100000 : 1) : null;
                        if ($model->amount <= $amountMax) {
                            $res = $this->handlePrPayout($model->toAccount, $model->amount);
                            if (isset($res['historyId']) && $res['historyId'] > 0) {
                                $result = ['PAYMENT_BATCH_NUM' => $res['historyId']];
                            } else {
                                Yii::error($res, 'payment\\' . $model->method . '\payout\error');
                                $result['error'] = ArrayHelper::getValue($res, 'error', '???????????? ???????????? ??????????????');
                                $error           = Yii::$app->payeer->getErrors();
//                                if (!empty($error)) {
//                                    $result['error'][] = array_values($error);
//                                }
                                Yii::error($result, 'payment\\' . $model->method . '\payout\error');
                            }
                        } else {
                            $message         = sprintf('???????????????????????? ?????????????? ?????? ?????????????? ???????????? [%d]. ??????????????????: %s. ????????????????: %s', getMyId(), $model->amount, $amountMax);
                            Yii::error($message, 'payment\\' . $model->method . '\payout\error');
                            $message         = sprintf('???? ???????????? ???????????? ?????????? ?????????????? ???? ?????????? %s. ?????????????????? ???????????????? ??????????. ???????????????? ???? ????????????????????.', Yii::$app->formatter->asCurrency($amountMax));
                            $result['error'] = $message;
//                            Yii::$app->session->addFlash('warning', $message);
                        }
                        $balance = Yii::$app->payeer->balance();
                        Yii::info($balance, 'payment\\' . $model->method . '\\balance');
                    } catch (\Exception $e) {
                        Yii::error($e, 'payment\payeer');
                        Yii::error($e);
                        $result          = ArrayHelper::toArray($e);
                        $result['error'] = $e->getMessage();
                    }
                    break;
//                case PaymentForm::PAYMENT_BITCOIN:
//                    $view = PaymentForm::PAYMENT_BITCOIN;
//                    break;
//                case PaymentForm::PAYMENT_ETHEREUM:
//                    $view = PaymentForm::PAYMENT_ETHEREUM;
//                    break;
                default :
                    $view = 'pm_payout';
            }

            Yii::info($model->attributes, 'payment\\' . $model->method);
//            if (is_null($result) || isset($result['error'])) {
//                return $this->render('payout_failure', [
//                            'error' => isset($result['error']) ? (array) $result['error'] : []
//                ]);
//            }
//            else {
//            $x1 = isset($result['error']);
//            $x2 = empty($result['error']);
//            $x3 = is_null($result['error']);
            if (!(is_null($result) || isset($result['error']))) {
                return $this->render('payout_result', [
                            'model' => $model,
                            'result' => $result,
                            'handling_fee' => $handling_fee,
                            'account' => Yii::$app->user->identity->userProfile->account,
                ]);
            } else {
                $errors = array_unique((array) $result['error']);
                foreach ($errors as $err) {
                    Yii::$app->session->addFlash('warning', Yii::t('payment', $err));
                }
            }
        }

        return $this->render('payout', [
                    'model' => $model,
                    'methods' => PaymentForm::$methods,
                    'handling_fee' => $handling_fee,
                    'account' => $account,
        ]);
    }

    /**
     *
     * @param type $target ????????, ???? ?????????????? ???????????????????????? ??????????????
     * @param type $amount ??????????
     * @param type $system ?????????????????? ??????????????
     * @param integer $id Id ??????????????
     */
    public function actionPayoutPostponed()
//    public function actionPayoutPostponed($target, $amount, $system)
    {
//        $id = Yii::$app->request->get('id');
        $confirm = Yii::$app->request->get('confirm');
        $stat    = UserAccountStat::findOne(Yii::$app->request->get('id'));
//        print_r([$id, $stat]);
        if (!$stat instanceof UserAccountStat) {
            Yii::$app->session->addFlash('warning', Yii::t('payment', '???????????????????????????????? ????????????????.'));
            return $this->redirect('/payment/payout');
        }
        if (is_null($confirm)) {
            //?????????????????? ????????????
            return $this->render('payout_postponed', [
                        'model' => $stat,
            ]);
        } else {
            if ((bool) $confirm) {
                //???????????????????? ???????????? ?? ??????????????
                $stat->status = UserAccountStat::STATUS_POSTPONED;
                $message      = '???????? ???????????? ???? ?????????? ???????????????? ?????????????? ?????????? ???????????????????? ?? ?????????????????? ??????????.<br>???? ???????????? ???????????????? ???????????? ?? ???????????? ???????????????? ???? ???????????????? ' . Html::a('&laquo;??????????????&raquo;', ['/user/default/finance', '#' => 'postponed'], ['class' => 'btn-success btn-xs', 'role' => 'button']) . '.';
                //?????????????????????????? ???????????? ???? ?????????? ????????????????????????
                $userProfile  = Yii::$app->user->identity->userProfile;
                $userProfile->accountWithdraw($stat->amount, sprintf('%s payout request', $stat->system), true);
                $userProfile->save();
            } else {
                $stat->status = UserAccountStat::STATUS_CANCELLED_BY_USER;
                $message      = '???????????? ???? ?????????? ???????????????? ?????????????? ????????????????.';
            }
            $res = $stat->save();
            Yii::$app->session->addFlash('info', Yii::t('payment', $message));
            return $this->redirect('/user/default/finance');
        }
//        print_r(Yii::$app->request->get());
    }

    public function handlePmPayout($target, $amount)
    {
        $res         = [];
        $user_id     = getMyId();
        $userProfile = Yii::$app->user->identity->userProfile;
        $memo        = sprintf('?????????? ?????????????? ?? ?????????? %s ???????????????????????? %s [%d]', Yii::$app->name, Yii::$app->user->identity->publicIdentity, $user_id);
        $event       = new PaymentEvent([
            'amount' => $amount,
            'reason' => 'Perfect Money payout',
            'userProfile' => $userProfile,
            'system' => PaymentForm::$methods[PaymentForm::PAYMENT_PERFECTMONEY],
            'direction' => UserAccountStat::DIRECTION_OUT,
            'status' => UserAccountStat::STATUS_REQUESTED,
            'target' => $target,
        ]);
        $this->trigger(self::EVENT_BEFORE_PAYOUT, $event);
        try {
            if ($event->isValid) {
                $transaction = Yii::$app->getDb()->beginTransaction();
                $userProfile->AccountWithdraw($amount, 'Perfect Money payout', true);
                $amountPay   = YII_ENV == 'dev' ? $amount / 100000 : $amount;
                $amountPay = Yii::$app->formatter->asDecimal($amountPay, 2); //?? ???????????????????? decimalSeparator => '.'
                $res         = Yii::$app->pm->transfer($target, $amountPay, time(), $memo);
                if (isset($res['PAYMENT_AMOUNT']) && $userProfile->save()) {
                    $transaction->commit();
//                    $event->operation_id = $res['historyId'];
                    $this->trigger(self::EVENT_AFTER_PAYOUT, $event);
                    Yii::info($res, 'payment\pm\payout\result');
                } else {
                    Yii::error($res, 'payment\pm\payout\error');
                    $res['error'] = $res;
                    $userProfile->AccountCharge($amount, 'Perfect Money transaction rollback', true);
                    $transaction->rollback();
                }
            } else {
                $res['error'] = $event->reason;
            }
        } catch (UnprocessableEntityHttpException $e) {
            $userProfile->AccountCharge($amount, 'Perfect Money transaction rollback', true);
            $transaction->rollback();
            $message      = $e->getMessage();
            Yii::error($message, 'payment\pm\payout\error');
//            Yii::$app->session->addFlash('warning', $message);
            $res['error'] = $message;
        } catch (\Exception $e) {
            $userProfile->AccountCharge($amount, 'Perfect Money transaction rollback', true);
            $transaction->rollback();
            $message      = sprintf('???????????? ???????????? ???????????????? ??????????????. %s', $e->getMessage());
            Yii::error($message, 'payment\pm\payout\error');
//            Yii::$app->session->addFlash('warning', '???????????? ???????????? ???????????????? ??????????????. ????????????????????, ?????????????????? ???????????????? ??????????.');
            $res['error'] = $message;
        }
        return $res;
    }

    public function handlePrPayout($target, $amount)
    {
        $res         = [];
        $user_id     = getMyId();
        $userProfile = Yii::$app->user->identity->userProfile;
        $memo        = sprintf('?????????? ?????????????? ?? ?????????? %s ???????????????????????? %s [%d]', Yii::$app->name, Yii::$app->user->identity->publicIdentity, $user_id);
        $event       = new PaymentEvent([
            'amount' => $amount, // (YII_ENV == 'dev' ? 100000 : 1)
            'reason' => 'Payeer payout',
            'userProfile' => $userProfile,
            'target' => $target,
            'system' => PaymentForm::$methods[PaymentForm::PAYMENT_PAYEER],
            'direction' => UserAccountStat::DIRECTION_OUT,
            'status' => UserAccountStat::STATUS_REQUESTED,
        ]);
        $this->trigger(self::EVENT_BEFORE_PAYOUT, $event);
        try {
            if ($event->isValid) {
                $transaction = Yii::$app->getDb()->beginTransaction();
                $userProfile->AccountWithdraw($amount, 'Payeer payout', true); //FIXME 100000 remove
                $amountPay   = YII_ENV == 'dev' ? $amount / 100000 : $amount;
                $amountPay = Yii::$app->formatter->asDecimal($amountPay, 2);
                $res         = Yii::$app->payeer->transfer($target, $amountPay, time(), $memo);
                if ($res['historyId'] > 0 && $userProfile->save()) {
                    $transaction->commit();
                    $event->operation_id = $res['historyId'];
                    $this->trigger(self::EVENT_AFTER_PAYOUT, $event);
                    Yii::info($res, 'payment\payeer\payout\result');
                } else {
                    Yii::error($res, 'payment\payeer\payout\error');
                    Yii::error(Yii::$app->payeer->getErrors(), 'payment\payeer\payout\error');
                    $res['error'] = Yii::$app->payeer->getErrors();
                    $userProfile->AccountCharge($amount, 'Payeer transaction rollback', true);
                    $transaction->rollback();
                }
            } else {
                $res['error'] = $event->reason;
            }
        } catch (UnprocessableEntityHttpException $e) {
            $userProfile->AccountCharge($amount, 'Payeer transaction rollback', true);
            $transaction->rollback();
            $message      = sprintf('???????????? ?????????????????? ??????????????. %s', $e->getMessage());
            Yii::error($message, 'payment\payeer\payout\error');
//            Yii::$app->session->addFlash('warning', $message);
            $res['error'] = $message;
        } catch (\Exception $e) {
            $userProfile->AccountCharge($amount, 'Payeer transaction rollback', true);
            $transaction->rollback();
            $message      = sprintf('???????????? ???????????? ???????????????? ??????????????. %s', $e->getMessage());
            Yii::error($message, 'payment\payeer\payout\error');
//            Yii::$app->session->addFlash('warning', '???????????? ???????????? ???????????????? ??????????????. ????????????????????, ?????????????????? ???????????????? ??????????.');
            $res['error'] = $message;
        }
        return $res;
    }

}
