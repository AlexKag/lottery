<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\LoginForm */

$this->title                   = Yii::t('frontend', 'Вход');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form                          = ActiveForm::begin(['id' => 'login-form']); ?>
            <?php echo $form->field($model, 'identity') ?>
            <?php echo $form->field($model, 'password')->passwordInput() ?>
            <?php echo $form->field($model, 'rememberMe')->checkbox() ?>
            <div style="color:#999;margin:1em 0">
                <?php
                echo Yii::t('frontend', '<a href="{link}">Восстановить</a> забытый пароль' , [
                    'link' => yii\helpers\Url::to(['sign-in/request-password-reset'])
                ])
                ?>
            </div>
            <?php
            echo \himiklab\yii2\recaptcha\ReCaptcha::widget([
                'name' => 'reCaptcha',
//    'siteKey' => Yii::$app->params['reCaptchaSiteKey'],
//                'widgetOptions' => ['class' => 'col-sm-offset-3']
            ])
            ?>
            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('frontend', 'Вход'), ['name' => 'login-button']) ?>
            </div>
            <div class="form-group">
                <?php echo Html::a(Yii::t('frontend', 'Нужна учетная запись? Зарегистрируйтесь.'), ['signup']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
</div>
