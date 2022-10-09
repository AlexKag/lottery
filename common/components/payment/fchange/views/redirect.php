<?php

/**
 *
 * @var \yii\web\View $this
 * @var common\componetns\payment\fchange\Api $api
 * @var $payment_num
 * @var $amount
 * @var $payment_info
 * @var $send_paysys_identificator
 */
use yii\helpers\Url;
?>
<div class="fchange-checkout">
    <p><?= \Yii::t('Fchange', 'Now you will be redirected to the payment system.') ?></p>
    <form id="fchange-checkout-form" name="send_form" action="http://f-change.biz/merchant_pay" method="post">
        <input type="hidden" name="merchant_name" value="<?= $api->merchant_name ?>" />
        <input type="hidden" name="merchant_title" value="<?= \Yii::$app->name ?>" />
        <input type="hidden" name="payed_paysys" value="<?= $send_paysys_identificator ?>" />
        <input type="hidden" name="amount" value="<?= $amount ?>" />
        <input type="hidden" name="payment_info" value="<?= $payment_info ?>" />
        <input type="hidden" name="payment_num" value="<?= $payment_num ?>" />
        <input type="hidden" name="sucess_url" value="<?= Url::to(['@frontendUrl/payment/fchange-success'], 'http') ?>" />
        <input type="hidden" name="error_url" value="<?= Url::to(['@frontendUrl/payment/fchange-failure'], 'http') ?>" />
        <input type="submit" value="Оплатить" />
    </form>
</div>

<?php
$js = <<<JS
    $('#fchange-checkout-form').submit();
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>