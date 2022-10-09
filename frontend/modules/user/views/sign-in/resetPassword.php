<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\modules\user\models\ResetPasswordForm */

$this->title = Yii::t('frontend', 'Reset password');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php
            $form = ActiveForm::begin([
                        'id' => 'reset-password-form',
//                        'fieldConfig' => [
//                            'template' => '{label}{input}',
//                            'options' => [
//                                'tag' => false,
//                            ],
//                        ],
            ]);
            ?>
                <?php echo $form->field($model, 'password')->passwordInput() ?>
            <?php echo $form->field($model, 'password_confirm')->passwordInput() ?>
            <div class="form-group">
<?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
            </div>
<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
