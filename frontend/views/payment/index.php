<?php

//use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
//use yii\bootstrap\Tabs;
//use frontend\models\PaymentForm;
use kartik\form\ActiveForm;
//use kartik\money\MaskMoney;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title = 'Пополнение баланса';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-index">

    <?php
//    $model->method = PaymentForm::PAYMENT_TELR;
    $form = ActiveForm::begin([
                'id' => 'payment-form',
                'type' => ActiveForm::TYPE_HORIZONTAL,
//                'formConfig' => ActiveForm::  //['labelSpan' => 2]
    ]);
    echo '<p class="lead">Детали перевода</p>';
    echo $form->field($model, 'method')->radioButtonGroup($methods, 
            [
                'class' => 'btn-group-sm',
                'itemOptions' => ['labelOptions' => ['class' => 'btn btn-default btn--payment']]
                ]
            );
//    echo $form->field($model, 'method')->multiselect($methods, ['selector'=>'radio']);
    echo $form->field($model, 'amount', [
        'addon' => ['prepend' => ['content' => 'USD']],
    ]);
/*    echo $form->field($model, 'amount')->widget(MaskMoney::classname(), [
        'pluginOptions' => [
//            'prefix' => '$ ',
            'suffix' => ' $',
            'thousands' => ' ',
            'decimal' => '.',
            'precision' => 0,
            'allowZero' => false,
            'allowNegative' => false,
        ]
    ]);
 */
//    echo $form->field($model, 'requestedStatus')->radioButtonGroup($statuses);
//    echo $form->field($model, 'term')->radioButtonGroup($terms);
    echo Html::submitButton('Продолжить', ['class' => 'btn btn-primary col-md-offset-2 col-md-2', 'name' => 'payment-button'])
    ?>
    <?php ActiveForm::end(); ?>
</div>
