<?php

use yii\helpers\Html;
//use frontend\assets\SlotMachineAsset;
use common\components\slots\widgets\SlotsFiveWidget;
use yii\bootstrap\Collapse;
use common\models\Page;
use common\components\slots\models\SlotsFiveTicket;

//use yii\web\NotFoundHttpException;

/* @var $this yii\web\View */

//SlotMachineAsset::register($this);

$this->title = $gameName;
$this->params['breadcrumbs'][] = ['label' => 'Слоты', 'url' => ['/site/slots']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>

<div class="body-content">
    <div class="row">
        <div class="col-xs-6 col-md-4">
            <div class="sidebar-game">
                <?= Html::img('@web/img/lotto_6.png', ['alt' => $gameName, 'height' => '100%', 'width' => 'auto']) ?>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-8"><p class="lead">Сделайте ставку.</p></div>
    </div>
    <?php
    echo SlotsFiveWidget::widget(['numbers' => 3, 'gameName' => $gameName, 'fields' => SlotsFiveTicket::FIELDS]);
    $page = Page::find()->where(['slug' => 'rulesslotsfive', 'status' => Page::STATUS_PUBLISHED])->one();
    if ($page) {
//        throw new NotFoundHttpException(Yii::t('frontend', 'Page not found'));
        echo '<p /><div class="row">' .
        Collapse::widget([
            'items' => [
                [
                    'label' => "Правила игры в слоты $gameName",
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