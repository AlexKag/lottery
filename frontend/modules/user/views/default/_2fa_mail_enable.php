<?php

//Google Authenticator config
//use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$model->enable = 1;
?>

<div class="ga-form">
    <?php $form          = ActiveForm::begin(); ?>

    <h2>Подключить двухэтапную аутентификацию e-mail</h2>

    <?php // $form->errorSummary($model); ?>
    <?php echo $form->field($model, 'enable')->hiddenInput()->label('') ?>
    <div class="row"><div class="col-md-12"><p class="center-block">На ваш e-mail оправлено письмо. Подтвердите настройки с помощью кода, полученного на ваш e-mail адрес.</p></div></div>
    <?php echo $form->field($model, 'code')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Подключить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>