<?php

//use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
//use yii\bootstrap\Tabs;
//use frontend\models\PaymentForm;
use kartik\form\ActiveForm;

//use kartik\money\MaskMoney;

/* @var $this yii\web\View */
//$this->registerCssFile('@web/css/pricing_flat.css');

$this->title                   = 'Вывод денег';
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['/user/default/index']];
$this->params['breadcrumbs'][] = ['label' => 'Финансы', 'url' => ['/user/default/finance']];
$this->params['breadcrumbs'][] = $this->title;
if ($model->amount == 0) {
    $model->amount = '';
}
?>
<div class="payout-index">
    <?php
    $form  = ActiveForm::begin([
                'id' => 'payment-form',
                'type' => ActiveForm::TYPE_HORIZONTAL,
//                'formConfig' => ActiveForm::  //['labelSpan' => 2]
    ]);
    echo '<p class="lead">Детали перевода</p>';
    echo $form->field($model, 'method')->radioButtonGroup($methods);
    $hint  = ($handling_fee > 0) ? 'Комиссия ' . Yii::$app->name . " за вывод средств $handling_fee%. Доступно для вывода с учетом комиссии: " . Yii::$app->formatter->asCurrency($model->amountMax) : '';
    echo $form->field($model, 'amount', [
        'addon' => ['prepend' => ['content' => 'USD']],
    ])->hint($hint);
    //Число знаков в маске
//    $signs = ceil(log10($model->amountMax));
//    $mask  = '$' . str_repeat('9', $signs);
//    echo $form->field($model, 'amount', [
//        'addon' => ['prepend' => ['content' => 'USD']],
//    ])->widget(\yii\widgets\MaskedInput::className(), [
//        'mask' => $mask,
//    ])->hint($hint);
//    $model->amount *=100;
    /*    echo $form->field($model, 'amount')->widget(MaskMoney::classname(), [
      //            'name' => 'amount',
      //            'value' => 200,
      'pluginOptions' => [
      //            'prefix' => '$ ',
      'suffix' => ' $',
      'thousands' => ' ',
      'decimal' => '.',
      'precision' => 0,
      'allowZero' => false,
      'allowNegative' => false,
      ]
      ])->hint($hint);
     */
//    echo $form->field($model, 'term')->radioButtonGroup($terms);
    echo $form->field($model, 'toAccount')->textInput(['maxlength' => true])->hint('Например, U12345678 (Perfect Money), P11223344 (Payeer)');
    echo Html::submitButton('Продолжить', ['class' => 'btn btn-primary col-md-offset-2 col-md-2', 'name' => 'payment-button'])
    ?>
    <?php ActiveForm::end(); ?>
</div>
