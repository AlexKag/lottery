<?php

use yii\helpers\Html;
use common\models\Page;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\UserAccountStat */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Запрос на вывод денежных средств.';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = ['label' => 'Вывод денег', 'url' => ['/payment/payout']];
$this->params['breadcrumbs'][] = $this->title;

$article = Page::find()->where(['slug' => 'payout_postponed', 'status' => Page::STATUS_PUBLISHED])->one();
?>
<div class="payment-postponed">
    <h1><?= $this->title ?></h1>
    <?php
    if ($article) {
        echo "<h2>$article->title</h2>";
        echo $article->body;
    }
    ?>
    <p class="lead">Вы подтверждаете запрос на вывод денежных средств?</p>
    <table class="table">
        <tr>
            <td>Платежная система</td>
            <td><?= $model->system ?></td>
        </tr>
        <tr>
            <td>Счёт</td>
            <td><?= $model->target ?></td>
        </tr>
        <tr>
            <td>Сумма</td>
            <td><?= Yii::$app->formatter->asCurrency($model->amount) ?></td>
        </tr>
    </table>
<?= Html::a('Подтвердить', Url::to(['/payment/payout-postponed', 'id' => $model->id, 'confirm' => 1]), ['class' => 'btn btn--green btn-lg', 'role' => 'button']); ?>
    &nbsp;
<?= Html::a('Отменить', Url::to(['/payment/payout-postponed', 'id' => $model->id, 'confirm' => 0]), ['class' => 'btn btn--danger btn-lg', 'role' => 'button']); ?>
</div>
