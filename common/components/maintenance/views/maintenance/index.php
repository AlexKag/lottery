<?php

/**
 * @author Eugene Terentev <eugene@terentev.net>
 * @var string $maintenanceText
 * @var int|string $retryAfter
 */
use frontend\modules\user\models\NotifyEmailForm;
use yii\helpers\Url;

$model = new NotifyEmailForm();
?>

<main>
    <div class="coming-soon">
        <div class="coming-soon__block">
            <img class="coming-soon__logo" src="/img/coming-soon-logo.png" alt="">
            <div class="coming-soon__title">Сайт находится в разработке.</div>
            <div class="coming-soon__desc">Мы скоро откроемся для вас!</div>
                            Оставьте Ваш e-mail и Вы узнаете об открытии сайта первыми!
            <?php
            //TODO СДелать доступным контроллер подписки в режиме maintanace
            $form = ActiveForm::begin([
                        'action' => Url::to(['/subscription']),
//                        'layout' => 'inline',
                        'fieldConfig' => [
                            'template' => '{input}',
                            'options' => [
                                'tag' => false,
                            ],
                        ],
                        'options' => ['class' => 'coming-soon__form'],
//                        'view' => 'subscribe',
            ]);
            echo $form->field($model, 'email', [ 'enableLabel' => false, 'inputOptions' => ['placeholder' => 'Ваш E-mail', 'type' => 'email', 'class' => '']])->textInput();
            echo Html::submitButton(Yii::t('frontend', 'Отправить'), ['class' => '']);
            ActiveForm::end();
            ?>
            <!--            <form class="coming-soon__form" action="profile.html">
                            Оставьте Ваш e-mail и Вы узнаете об открытии сайта первыми!
                            <input type="email" name="" value="" placeholder="Ваш e-mail"><button>Отправить</button>
                        </form>-->
        </div>
    </div>
</main>
