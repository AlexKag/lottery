<?php

//Вывод билетов

use yii\widgets\ListView;
use kop\y2sp\ScrollPager;
use yii\helpers\Html;

$lst = trim(array_reduce($dataProvider->models, function($carry, $item) {
            $carry .= $item->id . ', ';
            return $carry;
        }), ', ');
        $tickets = $dataProvider->totalCount > 1 ? 'билеты':'билет';
$this->title                   = "Лотерея «6 из 45», $tickets $lst";
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
$this->params['breadcrumbs'][] = ['label' => 'Лотерея &laquo;6 из 45&raquo;', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = ['label' => 'Проверка билетов', 'url' => ['/lottery6x45/check-ticket']];
//$this->params['breadcrumbs'][] = ['label' => $lst ? $lst : 'Нет'];
?>
<h1><?= Html::img('@web/img/lotto_2.png', ['alt' => 'Лотерея «6 из 45»', 'height' => '100%', 'width' => 'auto']) ?>&nbsp;<?= $this->title ?></h1>
<?php
//http://kop.github.io/yii2-scroll-pager/
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_ticket',
    'itemOptions'  => ['class' => 'item'],
    'layout'       => '<div class="items">{items}</div>{pager}',
//    'layout'       => '<div class="items">{items}</div>{pager}',
    'pager'        => [
        'class' => ScrollPager::className(),
        'noneLeftText' => '&nbsp;'
    ],
]);
?>
<?= $this->render('@frontend/views/site/_button_back'); ?>