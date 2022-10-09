<?php

/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace common\components\payment\payeer;

use common\components\payment\payeer\CPayeer;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yiidreamteam\payeer\events\GatewayEvent;
use Yii;

class Api extends Component
{

    /** @var string */
    public $accountNumber;

    /** @var string */
    public $apiId;

    /** @var string */
    public $apiSecret;

    /** @var string Shop id */
    public $merchantId;

    /** @var string Secret sequence from shop settings */
    public $merchantSecret;

    /** @var string Shop currency */
    public $merchantCurrency = 'USD';

    /** @var \CPayeer */
    public $payeer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        assert(isset($this->accountNumber));
        assert(isset($this->apiId));
        assert(isset($this->apiSecret));

        $this->payeer = new CPayeer($this->accountNumber, $this->apiId, $this->apiSecret);
        if (!$this->payeer->isAuth())
            throw new InvalidConfigException('Invalid payeer credentials');
    }

    /**
     * Validates incoming request security sign
     *
     * @param array $data
     * @return boolean
     */
    protected function checkSign($data)
    {
        $parts = [
            ArrayHelper::getValue($data, 'm_operation_id'),
            ArrayHelper::getValue($data, 'm_operation_ps'),
            ArrayHelper::getValue($data, 'm_operation_date'),
            ArrayHelper::getValue($data, 'm_operation_pay_date'),
            ArrayHelper::getValue($data, 'm_shop'),
            ArrayHelper::getValue($data, 'm_orderid'),
            ArrayHelper::getValue($data, 'm_amount'),
            ArrayHelper::getValue($data, 'm_curr'),
            ArrayHelper::getValue($data, 'm_desc'),
            ArrayHelper::getValue($data, 'm_status'),
            $this->merchantSecret,
        ];

        $sign = strtoupper(hash('sha256', implode(':', $parts)));
        return ArrayHelper::getValue($data, 'm_sign') == $sign;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function processResult($data)
    {
        // required parameters
        if (!array_key_exists('m_operation_id', $data) || !array_key_exists('m_sign', $data))
            return false;

        // we process only succeeded payments
        if (ArrayHelper::getValue($data, 'm_status') != 'success')
            return false;

        if (!$this->checkSign($data))
            return false;

        $event       = new GatewayEvent(['gatewayData' => $data]);
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->trigger(GatewayEvent::EVENT_PAYMENT_REQUEST, $event);
            if (!$event->handled)
                throw new \Exception();
            $this->trigger(GatewayEvent::EVENT_PAYMENT_SUCCESS, $event);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            \Yii::error('Payment processing error: ' . $e->getMessage(), 'Payeer');
            return false;
        }

        return true;
    }

    public function balance()
    {
        $res = $this->payeer->getBalance();
//        Array
//(
//    [auth_error] => 0
//    [errors] => Array
//        (
//        )
//
//    [balance] => Array
//        (
//            [BTC] => Array
//                (
//                    [BUDGET] => 0.00000000
//                    [DOSTUPNO] => 0.00000000
//                    [DOSTUPNO_SYST] => 0.00000000
//                )
//
//            [EUR] => Array
//                (
//                    [BUDGET] => 0.00
//                    [DOSTUPNO] => 0.00
//                    [DOSTUPNO_SYST] => 0.00
//                )
//
//            [RUB] => Array
//                (
//                    [BUDGET] => 0.00
//                    [DOSTUPNO] => 0.00
//                    [DOSTUPNO_SYST] => 0.00
//                )
//
//            [USD] => Array
//                (
//                    [BUDGET] => 1.00
//                    [DOSTUPNO] => 1.00
//                    [DOSTUPNO_SYST] => 1.00
//                )
//
//        )
//
//)
//        return isset($res['balance']['USD']['DOSTUPNO_SYST']) ? $res['balance']['USD']['DOSTUPNO_SYST'] : null;
        return $res;
    }

    public function transfer($target, $amount, $paymentId = null, $memo = null)
    {
        $params     = [
            'ps'                   => '1136053', //id выбранной платежной системы (Payeer id)
            'sumIn'                => $amount,
            'curIn'                => 'USD',
//		'sumOut' => 1,
		'curOut' => 'USD',
            'param_ACCOUNT_NUMBER' => $target
        ];
        $initOutput = $this->payeer->initOutput($params);

        if ($initOutput) {
//            $historyId = $this->payeer->output();
            $params['historyId'] = $this->payeer->output();
//            if ($params['historyId'] > 0) {
////                Yii
//                echo "Выплата успешна";
//            }
//            else {
//                echo '<pre>' . print_r($this->payeer->getErrors(), true) . '</pre>';
//            }
        }
        else {
            $params['historyId'] = null;
            Yii::error($this->payeer->getErrors(), 'payment\payeer\payout\error');
//            echo '<pre>' . print_r($this->payeer->getErrors(), true) . '</pre>';

        }
        return $params;
    }

    public function getErrors(){
        return $this->payeer->getErrors();
    }

}
