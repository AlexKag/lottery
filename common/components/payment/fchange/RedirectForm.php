<?php
/**
 * @author Alexey Samoylov <alexey.samoylov@gmail.com>
 */

namespace common\components\payment\fchange;

use yii\base\Widget;

class RedirectForm extends Widget
{
    /** @var Api */
    public $api;
    public $amount;
    public $payment_info = '';
    public $payment_num = '';
    public $send_paysys_identificator = 'YAMRUB';

    public function init()
    {
        parent::init();
        assert(isset($this->api));
//        assert(isset($this->payment_num));
        assert(isset($this->amount));
    }

    public function run()
    {
        $amount = number_format($this->amount, 2, '.', '');
//        $description = base64_encode($this->payment_info);

        return $this->render('redirect', [
            'api' => $this->api,
            'amount' => $amount,
            'payment_num' => $this->payment_num,
            'payment_info' => $this->payment_info,
            'send_paysys_identificator' => $this->send_paysys_identificator,
        ]);
    }
}