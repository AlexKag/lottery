<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\PasswordResetRequestForm */

$this->title                   = Yii::t('frontend', 'Запрос на восстановление пароля');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-request-password-reset">
    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form                          = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>
            <?php echo $form->field($model, 'identity') ?>
            <?php
            echo \himiklab\yii2\recaptcha\ReCaptcha::widget([
                'name'          => 'reCaptcha',
                'widgetOptions' => ['class' => 'col-sm-offset-3']
            ])
            ?>
            <div class="form-group">
<?php echo Html::submitButton('Отправить') ?>
            </div>
<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
