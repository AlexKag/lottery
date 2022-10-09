<?php

//use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

//use yii\helpers\Json;
//use common\components\lottery\models\BaseLottery;

$this->title                   = 'Проверка билетов';
$this->params['breadcrumbs'][] = ['label' => 'Лотереи', 'url' => ['/site/lotteries']];
$this->params['breadcrumbs'][] = ['label' => 'Лотерея «6 из 45»', 'url' => ['/lottery6x45']];
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];
?>

<div class="check-ticket">
    <h1><?= Html::img('@web/img/lotto_2.png', ['alt' => 'Лотерея «6 из 45»', 'height' => '100%', 'width' => 'auto']) ?>&nbsp;<?= $this->title ?></h1>

    <div class="row">
        <div class="col-lg-12">
            <?php $form                          = ActiveForm::begin(['id' => 'check-ticket-form']); ?>
            <?php //echo $form->field($model, 'lottery_type', [])->dropDownList(Yii::$app->params['availableLotteries']) ?>
            <?php
            DynamicFormWidget::begin([
                'widgetContainer' => 'check_tickets_wrapper',
                'widgetBody'      => '.tickets',
                'widgetItem'      => '.ticket',
                'formId'          => 'check-ticket-form',
                'limit'           => 10,
                'insertButton'    => '.add-ticket',
                'deleteButton'    => '.delete-ticket',
                'min'             => 1,
                'limit'           => 5,
                'model'           => $model,
                'formFields'      => [
//                    'lottery_type',
                    'ticket_id'
                ],
            ]);
            ?>
            <div class="tickets">
                <div class="ticket input-group">
                    <?php
                    echo $form->field($model, '[0]ticket_id', [
//                        'template' => "{label}\n<i class='fa fa-user'></i>\n{input}\n{hint}\n{error}"
//                        'template'     => "{beginWrapper}\n{label}\n{input}<div class=\"delete-ticket input-group-addon\">--</div>\n{hint}\n{error}\n{endWrapper}",
//                        'template'     => "{beginWrapper}\n{label}\n<br />{input}\n{hint}\n{error}\n{endWrapper}",
//                        'inputTemplate' => '{input}<span class="input-group-addon delete-ticket"><i class="glyphicon glyphicon-minus"></i></span>',
                        'inputTemplate' => '<div class="input-group"><span class="input-group-addon delete-ticket"><i class="glyphicon glyphicon-minus"></i></span>{input}</div>',
                        'inputOptions'  => [
                            'placeholder' => 123,
                        ],
//                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}"
                    ])->textInput(['maxlength' => true]);
                    ?>
                </div>
            </div>
            <span class="label label-success add-ticket">Добавить билет</span>
            <?php
            DynamicFormWidget::end();
            ?>
            <div class="form-group">
                <?php echo Html::submitButton(Yii::t('frontend', 'Отправить'), ['name' => 'check-ticket-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>