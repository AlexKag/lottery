<?php

//Google Authenticator config
//use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$model->enable = 0;
?>

<div class="ga-form">
    <?php $form          = ActiveForm::begin(); ?>

    <h2>Отключить Google Authenticator</h2>

    <?php // $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'enable')->hiddenInput()->label('') ?>
    <div class="row"><div class="col-md-12"><p>Подтвердите отключение двухэтапной аутентификации с помощью сгенерированного кода.</p></div></div>
    <?php echo $form->field($model, 'code')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Отключить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>