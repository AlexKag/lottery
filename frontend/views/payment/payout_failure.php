<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Вывод денежных средств';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = ['label' => 'Вывод денежных средств', 'url' => ['/payment/payout']];
$this->params['breadcrumbs'][] = 'Операция не выполнена';
?>
<div class="payout-result">
    <div class="jumbotron">
        <h1><?= $this->title ?></h1>
        <p>Ошибка вывода денежных средств. Повторите операцию позже.</p>
        <p>Извините за неудобства.</p>
        <?php
        if (!empty($error)) {
            echo Html::tag('div', implode('<br>', $error), ['role' => 'alert', 'class' => 'alert alert-danger']);
        }
        ?>
    </div>
</div>
