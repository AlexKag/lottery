<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Оплата завершена';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = ['label' => 'Пополнение баланса', 'url' => ['/payment']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-success">
    <div class="jumbotron">
        <h1><?= $this->title ?></h1>
        <table class="finance__table">
            <tr>
                <td>Начислено</td>
                <td><?= $model->PAYMENT_AMOUNT . ' ' . $model->PAYMENT_UNITS ?></td>
            </tr>
            <tr>
                <td>Транзакция №</td>
                <td><?= $model->PAYMENT_BATCH_NUM ?></td>
            </tr>
            <tr>
                <td>Дата</td>
                <td><?= Yii::$app->formatter->asDate($model->TIMESTAMPGMT) ?></td>
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