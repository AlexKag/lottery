<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = Yii::t('frontend', 'Профиль');
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];
?>
<div class="user-profile-form">
    <?php $form                          = ActiveForm::begin(['options' => ['id' => 'profile']]); ?>


    <h2><?php echo Yii::t('frontend', 'Профиль') ?></h2>

    <?= $form->errorSummary($modelProfile); ?>

    <?php echo $form->field($modelProfile, 'username')->textInput() ?>

    <?php echo $form->field($modelProfile, 'firstname')->textInput() ?>

    <?php echo $form->field($modelProfile, 'middlename')->textInput() ?>

    <?php echo $form->field($modelProfile, 'lastname')->textInput() ?>

    <?php
    echo $form->field($modelProfile, 'phone')->widget(PhoneInput::className(), [
        'options' => [
            'class' => 'form-control',
        ],
//        'jsOptions' => [
////            'preferredCountries' => ['ru', 'ua', 'kz', 'us'],
//            'onlyCountries' => ['ru', 'ua', 'kz', 'us'],
////            'autoHideDialCode'=>false,
//            'initialCountry'=>  'ru',
//            'nationalMode' => false,
//        ]
    ])
    ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Сохранить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php $form                          = ActiveForm::begin(['options' => ['id' => 'password']]); ?>

    <h2><?php echo Yii::t('frontend', 'Изменить пароль') ?></h2>

    <?= $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'password')->passwordInput() ?>

    <?php echo $form->field($model, 'password_new')->passwordInput() ?>

    <?php echo $form->field($model, 'password_new_confirm')->passwordInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton(Yii::t('frontend', 'Сохранить'), ['class' => '']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>