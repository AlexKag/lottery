<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
use himiklab\yii2\recaptcha\ReCaptcha;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\SignupForm */

$this->title                   = Yii::t('frontend', 'Регистрация');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?php echo Html::encode($this->title) ?></h1>

    <?php
    $form                          = ActiveForm::begin([
                'id' => 'form-signup',
    ]);
    ?>
    <?php echo $form->field($model, 'ref')->hiddenInput()->label(false) ?>
    <?php echo $form->field($model, 'username') ?>
    <?php // echo $form->field($model, 'email', ['enableAjaxValidation' => true]) ?>
    <?php echo $form->field($model, 'email') ?>
    <?php
//echo $form->field($model, 'phone')->widget(PhoneInput::className(), [
//    'options' => [
//        'class' => 'form-control',
//    ],
//])
    ?>
    <?php echo $form->field($model, 'password')->passwordInput() ?>
    <?php echo $form->field($model, 'password_repeat')->passwordInput() ?>
    <?php echo $form->field($model, 'confirm')->checkbox(['label' => ''], false)->label('&nbsp;'); ?>
    <?php echo 'Мне больше 18 лет и я принимаю условия ' . Html::a('Договора-оферты', '/page/agreement', ['target' => '_blank']); ?>
    <?php
    echo ReCaptcha::widget([
        'name' => 'reCaptcha',
    ])
    ?>
    <?php echo Html::submitButton(Yii::t('frontend', 'Отправить'), ['name' => 'signup-button']) ?>
    <?php ActiveForm::end(); ?>
</div>