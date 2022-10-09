<?php

use yii\bootstrap\Html;
//use yii\bootstrap\Nav;
//use yii\web\View;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('frontend', 'Личный кабинет пользователя');
$user        = Yii::$app->user->identity;
$this->beginContent('@app/views/layouts/main.php');
?>

<div class="container">
    <div class="sidebar">
        <div class="sidebar__info">
            Вы вошли как <?= $user->getPublicIdentity() ?>
            <?= Html::a('Выйти из кабинета', '/user/sign-in/logout', ['title' => 'Выйти из кабинета', 'data-method' => 'post']) ?>
        </div>
        <?php
        echo Html::ul([
            Html::a(Html::tag('i', '', ['class' => 'fa fa-user', 'aria-hidden' => 'true']) . ' Мой профиль', 'profile', ['title' => 'Мой профиль', 'class' => 'sidebar__menu-item' . ('profile' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
//            Html::a(Html::tag('i', '', ['class' => 'fa fa-check', 'aria-hidden' => 'true']) . ' Верификация', 'verify', ['title' => 'Верификация', 'class' => 'sidebar__menu-item' . ('verify' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-envelope', 'aria-hidden' => 'true']) . ' Оповещения', 'notify', ['title' => 'Оповещения', 'class' => 'sidebar__menu-item' . ('notify' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-shield', 'aria-hidden' => 'true']) . ' Безопасность', 'security', ['title' => 'Безопасность', 'class' => 'sidebar__menu-item' . ('security' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-money', 'aria-hidden' => 'true']) . ' Финансы', 'finance', ['title' => 'Финансы', 'class' => 'sidebar__menu-item' . ('finance' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
//            Html::a('Рекламные материалы', 'advertise', ['title' => 'Рекламные материалы', 'class' => 'sidebar__menu-item sidebar__menu-item--advertising' . ('advertise' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-users', 'aria-hidden' => 'true']) . ' Мои друзья', 'referrals', ['title' => 'Мои друзья', 'class' => 'sidebar__menu-item' . ('referrals' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-ticket', 'aria-hidden' => 'true']) . ' Статистика игр', 'stat', ['title' => 'Статистика игр', 'class' => 'sidebar__menu-item' . ('stat' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
//            Html::a(Html::tag('i', '', ['class' => 'fa fa-database', 'aria-hidden' => 'true']) . 'Автопокупка', 'autopay', ['title' => 'Автопокупка', 'class' => 'sidebar__menu-item' . ('autopay' == Yii::$app->controller->action->id ? ' sidebar__menu-item--active' : '')]),
            Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . 'Все лотереи', '/site/lotteries', ['title' => 'Все лотереи', 'class' => 'sidebar__menu-item sidebar__menu-item--lotteries']),
                ], [
            'encode'  => false,
            'class' => 'sidebar__menu',
        ]);
        ?>
    </div>

    <div class="content">
        <div class="main-block">
            <?= $content ?>
        </div>
    </div>
</div>

<?php $this->endContent(); ?>