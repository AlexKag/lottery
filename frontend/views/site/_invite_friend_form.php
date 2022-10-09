<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */

$config = isset($action)?['id' => 'form-invite-friend', 'action' => $action]:['id' => 'form-invite-friend'];

$form = ActiveForm::begin($config);
echo $form->field($model, 'name');
echo $form->field($model, 'email');

if ($isGuest) {
    echo ReCaptcha::widget([
        'name' => 'reCaptcha',
    ]);
}

echo Html::submitButton(Yii::t('frontend', 'Пригласить'), ['name' => 'signup-button']);
ActiveForm::end();
