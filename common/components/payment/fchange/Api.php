<?php

namespace common\components\payment\fchange;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\authclient\InvalidResponseException;
use yii\helpers\ArrayHelper;
use yiidreamteam\payeer\events\GatewayEvent;

class Api extends Component
{

    /** @var string */
    public $merchant_name;

    /** @var string */
    public $merchantSecret;

    /** @var string */
    public $recive_paysys_identificator = 'PMUSD';
    protected $merchant_cources;
    protected $merchant_cources_url     = 'http://f-change.biz/obmen/get_merchant_cources/euro-lotto';
    private $agent                      = 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0';
    public $errors;

    public function init()
    {
        parent::init();
        $this->merchant_cources_url = 'http://f-change.biz/obmen/get_merchant_cources/' . $this->merchant_name;
        if (!$this->isPaysysActive($this->recive_paysys_identificator)) {//FIXME
            throw new InvalidConfigException("F-Change pay system {$this->recive_paysys_identificator} is not active.");
        }
    }

    /**
     * Запрос действующих направлений переводов и условий их осуществления
     * @return array
     */
    public function getCources()
    {
        $this->merchant_cources = $this->getResponse($this->merchant_cources_url);
        return $this->merchant_cources;
    }

    public function getMethods()
    {
        $res = [];
        foreach ($this->merchant_cources as $key => $item) {
            if ($item['recive_paysys_identificator'] == $this->recive_paysys_identificator) {
                $res[$item['send_paysys_identificator']] = $item['send_paysys_title'];
            }
        }
        if (!count($res)) {
            throw new Exception('F-change. Нет активных направлений обмена.');
        }
        return $res;
    }

    /**
     * Проверка доступности переводов выбранной платежной системы
     * @param string $paysys_identificator
     * @return boolean
     */
    public function isPaysysActive($paysys_identificator = '')
    {
        if (empty($this->merchant_cources)) {
            $cources = $this->cources;
        }
        if (empty($paysys_identificator)) {
            $paysys_identificator = $this->recive_paysys_identificator;
        }
        $res = false;
        foreach ($this->cources as $cource) {
            if (array_key_exists('recive_paysys_identificator', $cource) && $cource['recive_paysys_identificator'] == $paysys_identificator) {
                $res = true;
                break;
            }
        }
        return $res;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function processResult(array $data)
    {
        // required parameters
        if (!array_key_exists('payment_num', $data) || !array_key_exists('verificate_hash', $data)) {
            throw new InvalidResponseException(Yii::$app->response, 'Ожидались данные: verificate_hash или payment_num.');
        }

        if (!$this->checkSign($data)) {
            throw new InvalidResponseException(Yii::$app->response, 'Неверная подпись данных.');
        }

        $event       = new GatewayEvent(['gatewayData' => $data]);
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->trigger(GatewayEvent::EVENT_PAYMENT_REQUEST, $event);
            if (!$event->handled)
                throw new Exception('Unhandled request from F-change.');
            $this->trigger(GatewayEvent::EVENT_PAYMENT_SUCCESS, $event);
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollback();
            throw new Exception('Payment processing error: ' . $e->getMessage(), 'F-Change');
//            \Yii::error('Payment processing error: ' . $e->getMessage(), 'F-Change');
            return false;
        }

        return true;
    }

    /**
     * Validates incoming request security sign
     *
     * @param array $data
     * @return boolean
     */
    protected function checkSign(array $data)
    {

        if (!isset($data['verificate_hash']) || empty($data['verificate_hash'])) {
            throw new InvalidResponseException(Yii::$app->response, 'F-Change. Mailformed data.');
        }
        $hash = $data['verificate_hash'];
        unset($data['verificate_hash']);

        $my_hash = '';
        foreach ($data as $one_post) {
            if ($my_hash == '') {
                $my_hash = $one_post;
            } else {
                $my_hash = $my_hash . '::' . $one_post;
            }
        }
        $my_hash = $my_hash . "::" . $this->merchantSecret;
        $my_hash = hash("sha256", $my_hash);

        if ($my_hash == $hash) {
            return true;
        }
        Yii::error('Hash check failed: ' . print_r($data, 1) . "\r\n Hash: $hash. \r\nCalculated hash: $my_hash.", 'payment\fchange\verify_hash\error');
        return false;
    }

    private function getResponse($url, array $arPost = [])
    {
        if (!function_exists('curl_init')) {
            throw Exception('Curl library not installed');
        }

//        if ($this->isAuth()) {
//            $arPost = array_merge($arPost, $this->auth);
//        }

        $data = array();
        foreach ($arPost as $k => $v) {
            $data[] = urlencode($k) . '=' . urlencode($v);
        }
        $data = implode('&', $data);

        $handler = curl_init();
        curl_setopt($handler, CURLOPT_URL, $url);
        curl_setopt($handler, CURLOPT_HEADER, 0);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $data);
        curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($handler, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);

        $content = curl_exec($handler);
        //print_r($content);

        $arRequest = curl_getinfo($handler);
        //print_r($arRequest);

        curl_close($handler);
        //print_r($content);

        $content = json_decode($content, true);

        if (isset($content['errors']) && !empty($content['errors'])) {
            $this->errors = $content['errors'];
        }

        return $content;
    }

}
