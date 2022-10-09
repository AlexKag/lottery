<?php

use yii\widgets\ListView;
use kop\y2sp\ScrollPager;
use yii\helpers\Html;

$this->title                   = "Архив тиражей";
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
$this->params['breadcrumbs'][] = ['label' => 'Лотерея &laquo6 из 45&raquo;', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<h1><?= Html::img('@web/img/lotto_2.png', ['alt' => 'Лотерея 6 из 45', 'height' => '100%', 'width' => 'auto']) ?>&nbsp;Лотерея &laquo;6 из 45&raquo;, архив тиражей</h1>
<?php
//http://kop.github.io/yii2-scroll-pager/
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'itemView'     => '_lottery_summary',
    'itemOptions'  => ['class' => 'item'],
    'layout'       => '{items}{pager}',
    'pager'        => [
        'class'            => ScrollPager::className(),
        'triggerText'      => 'Показать больше результатов',
        'triggerTemplate'  => '<div class="clearfix"></div><br><div class="alert alert-success" style="text-align: center;">{text}</div>',
        'noneLeftText'     => 'Конец',
        'noneLeftTemplate' => '<div class="clearfix"></div><br><div class="alert alert-info" style="text-align: center;">{text}</div>',
    ],
]);
?>

<?= $this->render('@frontend/views/site/_button_back'); ?>