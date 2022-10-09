<?php

use yii\bootstrap\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title                   = Yii::t('frontend', 'Оповещения'); //Уведомления
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];
?>
<div class="user-notify-form">
    <h2><?= $this->title ?></h2>

    <p>Куда вы желаете получать оповещения о событиях на сайте?</p>

    <div class="white-block white-block--success">
        <label for="email-choice">На электронную почту</label>
        <?php
        $form = ActiveForm::begin(['options' => ['id' => 'notify_email', 'class' => 'form--small'], 'action' => Url::to(['/subscription'])]);
        echo $form->field($model, 'email', ['inputOptions' => ['placeholder' => $email]])->textInput();
        // echo $form->field($model, 'email')->textInput();
        echo Html::submitButton(Yii::t('frontend', 'Сохранить'), ['class' => '']);
        ActiveForm::end();
        ?>
    </div>

    <!--<div class="white-block">
        <label for="sms-choice">С помощью СМС</label>
        <input id="sms-choice" type="checkbox" name="" value=""><label for="sms-choice">С помощью СМС</label>
        <form class="form--small">
            <label for="phone">Ваш моб.</label><input id="phone" type="text" value="" name="" placeholder="Пример +7 495 555 66 77">
            <button type="submit">Сохранить</button>
        </form>
    </div>-->
</div>