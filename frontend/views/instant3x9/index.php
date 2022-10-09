<?php

use yii\helpers\Html;
use common\components\lottery\widgets\Instant1x3Widget;
use yii\bootstrap\Collapse;
use common\models\Page;

//use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */
//$this->title = Yii::$app->name;

$this->title                   = "Мгновенная лотерея $gameName";
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
//$this->params['breadcrumbs'][] = ['label' => '6 из 45', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="body-content">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-8"><p class="lead">Выберите три числа и сделайте ставку.</p></div>
        <div class="col-xs-6 col-md-4">
            <div class="sidebar-game">
                <?= Html::img('@web/img/lotto_5.png', ['alt' => $gameName, 'height' => '100%', 'width' => 'auto']) ?>
            </div>
        </div>
    </div>
    <?php
    echo Instant1x3Widget::widget(['numbers' => 9, 'betDefault' => $betDefault, 'gameName' => $gameName, 'betMax' => $betMax, 'betNumberMax' => 9]);
    $page                          = Page::find()->where(['slug' => 'rules3x9', 'status' => Page::STATUS_PUBLISHED])->one();
    if ($page) {
        echo '<div class="row">' .
        Collapse::widget([
            'items' => [
                [
                    'label' => "Правила игры в мгновенную лотерею $gameName",
                    'content' => $page->body,
                    'contentOptions' => [],
                    'options' => []
                ],
            ]
        ]) .
        '</div>';
    }
    ?>
</div>
