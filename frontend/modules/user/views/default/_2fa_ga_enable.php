<?php

//Google Authenticator config
//use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

//$secret        = Yii::$app->googleAuth->generateSecret();
$model->enable = 1;
$model->token  = Yii::$app->googleAuth->generateSecret();
$url           = Yii::$app->googleAuth->getUrl(Yii::$app->user->identity->getPublicIdentity(), Yii::$app->name, $model->token);
?>

<div class="ga-form">
    <?php $form          = ActiveForm::begin(); ?>

    <h2>Подключить Google Authenticator<small> с генерацией кода по времени</small></h2>

    <?php // $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'enable')->hiddenInput()->label('') ?>

    <?php echo $form->field($model, 'token')->hiddenInput()->label('') ?>

    <div class="row">
        <div class="col-md-4">Ваш персональный ключ:</div>
        <div class="col-md-8"><strong><?= $model->token ?></strong></div>
        <div class="col-md-12"><div class="alert alert-warning" role="alert">Сохраните в надежном месте персональный ключ. Это позволит вам быстро восстановить доступ к учетной записи в случае утраты устройства с Google Authenticator.</div></div>
    </div>
    <p></p>
    <p class="center-block">или</p>
    <p></p>
    <div class="row">
        <div class="col-md-4">QR ключ:</div>
        <div class="col-md-8"><?= Html::img($url) ?></div>
    </div>
    <hr>
    <div class="row"><div class="col-md-12"><p class="center-block">Настройте Google Authenticator и подтвердите настройки с помощью сгенерированного кода.</p></div></div>
    <?php echo $form->field($model, 'code')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Подключить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
