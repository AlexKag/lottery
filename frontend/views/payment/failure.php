<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Оплата отменена';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = ['label' => 'Пополнение баланса', 'url' => ['/payment']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-failure">
    <div class="jumbotron">
        <h1><?= $this->title ?></h1>
    </div>
    <?php
    echo Html::a('Играть', Url::to(['/site/lotteries']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    echo '&nbsp;';
    echo Html::a('Финансы', Url::to(['/user/default/finance']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    echo '&nbsp;';
    echo Html::a('Статистика игр', Url::to(['/user/default/stat']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
    ?>
</div>
