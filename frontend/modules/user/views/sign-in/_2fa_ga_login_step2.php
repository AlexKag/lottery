<?php

//Google Authenticator config
//use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="ga-form">
    <?php $form          = ActiveForm::begin(); ?>

    <h2>Двухэтапная авторизация Google Authenticator</h2>

    <?php // $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'type')->hiddenInput()->label('') ?>
    <?php echo $form->field($model, 'token')->hiddenInput()->label('') ?>
    <div class="row"><div class="col-md-12"><p>Подтвердите вашу личность с помощью сгенерированного кода.</p></div></div>
    <?php echo $form->field($model, 'code')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Подтвердить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>