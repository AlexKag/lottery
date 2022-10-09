<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title = "Результаты тиража № $model->id";
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
$this->params['breadcrumbs'][] = ['label' => 'Лотереи &laquo;6 из 45&raquo;', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<h1><?= Html::img('@web/img/lotto_2.png', ['alt' => $model->name, 'height' => '100%', 'width' => 'auto']) ?><?= "&nbsp;Лотерея &laquo;6 из 45&raquo;, результаты тиража № $model->id от $model->draw_d" ?></h1>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-block">
                <div class="rotate">
                    <i class="glyphicon glyphicon-list-alt"></i>
                </div>
                <div class="details">
                    <span class="title">Куплено билетов</span>
                    <span class="sub"><?= Yii::$app->formatter->asInteger($model->tickets) ?></span>
                </div><!--/details-->
                <!--<i class="fa fa-chevron-right fa-2x more"></i>-->
            </div><!--/dashboard-block1-->
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-block">
                <div class="rotate">
                    <i class="glyphicon glyphicon-credit-card"></i>
                </div>
                <div class="details">
                    <span class="title">Выплачено денег</span>
                    <span class="sub"><?= Yii::$app->formatter->asCurrency($model->paid_out) ?></span>
                </div><!--/details-->
                <!--<i class="fa fa-chevron-right fa-2x more"></i>-->
            </div><!--/dashboard-block2-->
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-block">
                <div class="rotate">
                    <i class="glyphicon glyphicon-gift"></i>
                </div>
                <div class="details">
                    <span class="title">Суперприз</span>
                    <span class="sub"><?= $model->superprizeReadable ?></span>
                </div><!--/details-->
                <!--<i class="fa fa-chevron-right fa-2x more"></i>-->
            </div><!--/dashboard-block3-->
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="dashboard-block">
                <div class="rotate">
                    <i class="glyphicon glyphicon-stats"></i>
                </div>
                <div class="details">
                    <span class="title">Увеличение суперприза на следующую игру</span>
                    <span class="sub"><?= Yii::$app->formatter->asCurrency($model->superprize_gain) ?></span>
                </div><!--/details-->
                <!--<i class="fa fa-chevron-right fa-2x more"></i>-->
            </div><!--/dashboard-block4-->
        </div>
    </div><!--/row-->
</div>
<?php
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
//    'rowOptions'   => function ($model, $key, $index, $grid) {
//        $class = $index % 2 ? 'odd' : 'even';
//        return [
//            'class' => $class
//        ];
//    },
    'columns' => [
        'correct_numbers:integer:' . Yii::t('frontend', 'Угадано чисел'),
        'winners:integer:' . Yii::t('frontend', 'Количество выигравших ставок'),
        'paid_out:currency:' . Yii::t('frontend', 'Выигрыш победителя'),
        'paid_out_total:currency:' . Yii::t('frontend', 'Общий выигрыш')
    ],
]);
?>
<?= $this->render('@frontend/views/site/_button_back'); ?>