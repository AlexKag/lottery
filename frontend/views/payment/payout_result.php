<?php

use yii\helpers\Html;
use frontend\models\PayoutForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Вывод денежных средств';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = ['label' => 'Вывод денежных средств', 'url' => ['/payment/payout']];
$this->params['breadcrumbs'][] = 'Операция выполнена';
?>
<div class="payout-result">
    <div class="jumbotron">
        <h1><?= $this->title ?></h1>
        <table class="finance__table">
            <tr>
                <td>Платежная система</td>
                <td><?= PayoutForm::$methods[$model['method']] ?></td>
            </tr>
            <tr>
                <td>Счёт</td>
                <td><?= $model['toAccount'] ?></td>
            </tr>
            <tr>
                <td>Транзакция №</td>
                <td><?= $result['PAYMENT_BATCH_NUM'] ?></td>
            </tr>
            <tr>
                <td>Сумма</td>
                <td><?= number_format($model['amount'], 2, '.', ' ') . ' ' . $model['units'] ?></td>
            </tr>
            <tr>
                <td>Комиссия <?= Yii::$app->name ?></td>
                <td><?= number_format($model['amount'] * $handling_fee / 100, 2, '.', ' ') ?></td>
            </tr>
            <tr>
                <td>Баланс</td>
                <td><?= number_format($account, 2, '.', ' ') ?></td>
            </tr>
            <tr>
                <td>Результат</td>
                <td>Операция выполнена</td>
            </tr>
        </table>
    </div>
    <?php
    echo Html::a('Играть', Url::to(['/site/lotteries']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    echo '&nbsp;';
    echo Html::a('Финансы', Url::to(['/user/default/finance']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    echo '&nbsp;';
    echo Html::a('Статистика игр', Url::to(['/user/default/stat']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    ?>
</div>

<!--[
    'method' => 'pm',
    'amount' => '10',
    'units' => 'USD',
    'amountMax' => 2314.6999999999998,
    'toAccount' => 'U12672057',
]-->