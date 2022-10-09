<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use himiklab\yii2\recaptcha\ReCaptcha;
use common\models\Page;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title                   = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
$article                       = Page::find()->where(['slug' => 'contact', 'status' => Page::STATUS_PUBLISHED])->one();
?>
<div class="site-contact">
    <h1>Обратная связь</h1>

    <div class="row">
        <div class="col-lg-12">
            <?php
            if ($article) {
                echo $article->body;
                echo '<p></p>';
            }
            ?>
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
            <?php echo $form->field($model, 'name') ?>
            <?php echo $form->field($model, 'email') ?>
            <?php echo $form->field($model, 'subject') ?>
            <?php echo $form->field($model, 'body')->textArea(['rows' => 6]) ?>
            <?php
            echo ReCaptcha::widget([
                'name' => 'reCaptcha',
//                'siteKey' => Yii::$app->params['reCaptchaSiteKey'],
//                'widgetOptions' => ['class' => 'col-sm-offset-3']
//                'widgetOptions' => ['class' => 'pull-right']
            ])
            ?>
            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('frontend', 'Отправить'), ['name' => 'contact-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
