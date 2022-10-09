<?php

use yii\helpers\Html;
use common\components\lottery\widgets\LotteryWidget;
use yii\bootstrap\Collapse;
use common\models\Page;

/* @var $this yii\web\View */
$this->title = Yii::$app->name;

$this->title                   = "Лотерея {$model->name}. Тираж № $model->id";
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
//$this->params['breadcrumbs'][] = ['label' => '6 из 45', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="body-content">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-8"></div>
        <div class="col-xs-6 col-md-4">
            <div class="sidebar-game">
                <?= Html::img('@web/img/lotto_2.png', ['alt' => $model->name, 'height' => '100%', 'width' => 'auto']) ?>
            </div>
        </div>
    </div>
    <?php
    echo LotteryWidget::widget(['numbers' => 45, 'lottery' => $model, 'pricing' => $pricing, 'betDefault' => $betDefault]);
    echo Html::tag('p');
    $page                          = Page::find()->where(['slug' => 'rules6x45', 'status' => Page::STATUS_PUBLISHED])->one();
    if ($page) {
        echo '<div class="row">' .
        Collapse::widget([
            'items' => [
                [
                    'label' => "Правила игры в лотерею {$model->name}",
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
