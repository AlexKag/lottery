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
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="body-content">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-8"><p class="lead">Выберите одно число и сделайте ставку.</p></div>
        <div class="col-xs-6 col-md-4">
            <div class="sidebar-game">
                <?= Html::img('@web/img/lotto_4.png', ['alt' => $gameName, 'height' => '100%', 'width' => 'auto']) ?>
            </div>
        </div>
    </div>
    <?php
    echo Instant1x3Widget::widget(['numbers' => 3, 'betDefault' => $betDefault, 'gameName' => $gameName, 'betMax' => $betMax, 'betNumberMax' => 3]);
    $page                          = Page::find()->where(['slug' => 'rules1x3', 'status' => Page::STATUS_PUBLISHED])->one();
    if ($page) {
//        throw new NotFoundHttpException(Yii::t('frontend', 'Page not found'));
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
