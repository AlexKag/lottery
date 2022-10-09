<?php

use yii\bootstrap\Html;
use frontend\modules\user\models\NotifyEmailForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@frontend/views/layouts/_clear.php');
//Icon::map($this);
$user    = Yii::$app->user->identity;
$isGuest = Yii::$app->user->isGuest;

$model = new NotifyEmailForm();
?>

<header>

    <div class="top-line">
        <div class="container">
            <span class="top-line__note">
                <?= Yii::$app->name ?> - честная лотерея!
                <?php
                if ($isGuest) {
                    echo Html::a('Регистрируйтесь прямо сейчас', '/user/sign-in/signup', ['title' => 'Регистрация', 'class' => 'top-line__note-link']);
                    echo ' и убедитесь в этом!';
                }
                ?>
            </span>
            <?php
//                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-money', 'aria-hidden' => 'true']) . 123456, '/user/sign-in/signup', ['title' => 'Регистрация', 'class' => 'top-line__link']);
            if ($isGuest) {
                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-user', 'aria-hidden' => 'true']) . ' Регистрация', '/user/sign-in/signup', ['title' => 'Регистрация', 'class' => 'top-line__link']);
            }
            else {
                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-sign-out', 'aria-hidden' => 'true']) . ' Выйти', '/user/sign-in/logout', ['title' => 'Выйти', 'class' => 'top-line__link', 'data-method' => 'post']);
            }
            if ($isGuest) {
                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-lock', 'aria-hidden' => 'true']) . ' Вход', '/user/sign-in/login', ['title' => 'Вход', 'class' => 'top-line__link']);
            }
            else {
                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-money', 'aria-hidden' => 'true']) . sprintf(' Ваш баланс: [<span class="account">%s</span>]', Yii::$app->formatter->asCurrency(Yii::$app->user->identity->userProfile->account)), '/user/default/finance', ['title' => 'Ваш баланс', 'class' => 'top-line__link']);
                $uname = $user->getPublicIdentity();
//                $message = strlen($uname) > 10 ? $uname : 'Вы вошли как ' . $uname;
                echo Html::a(Html::tag('i', '', ['class' => 'fa fa-user', 'aria-hidden' => 'true']) . ($isGuest ? '' : 'Вы вошли как ' . $uname), '/user/default/index', ['title' => $isGuest ? '' : 'Вы вошли как ' . $uname, 'class' => 'top-line__link']);
            }
//            echo Html::a('Рус', ['/site/set-locale', 'locale' => 'ru-RU'], ['title' => 'Русский язык', 'class' => 'top-line__link top-line__link--lang-rus' . (Yii::$app->language == 'ru-RU' ? ' top-line__link--active' : '')]);
//            echo Html::a('Eng', ['/site/set-locale', 'locale' => 'en-US'], ['title' => 'English Language', 'class' => 'top-line__link top-line__link--lang-en' . (Yii::$app->language == 'en-US' ? ' top-line__link--active' : '')]);
            ?>
        </div>
    </div>

    <div class="color-lines">
        <div class="color-lines__green color-lines__green--left"></div>
        <div class="color-lines__red color-lines__red--first"></div>
        <div class="color-lines__red color-lines__red--second"></div>
        <div class="color-lines__blue"></div>
    </div>

    <div class="top-nav">
        <div class="container">
            <nav class="top-nav__menu">
                <?=
                Html::ul([
                    Html::a('Главная', Url::to('/', true), ['title' => 'Главная']),
                    Html::a('Лотереи', '/site/lotteries', ['title' => 'Лотереи']),
                    Html::a('Как это работает', '/site/howitworks', ['title' => 'Как это работает']),
                    Html::a('О нас', '/page/about', ['title' => 'О нас']),
                    Html::a('Новости', '/article/index', ['title' => 'Новости']),
                    Html::a('Благотворительность', '/page/philanthropy', ['title' => 'Благотворительность']),
                    Html::a('Контакты', '/site/contact', ['title' => 'Контакты']),
                        ], [
                    'encode' => false,
                ])
                ?>
            </nav>
            <span class="top-nav__logo top-nav__logo--in"><?= Html::img('/img/logo.png', ['alt' => Yii::$app->name]) ?></span>
        </div>
    </div>

</header>

<?php echo $content ?>

<footer>
    <div class = "container">
        <a class = "footer__logo" href = "#" title = ""><img src = "/img/footer__logo.png" alt = ""></a>
        <div class = "footer__menu footer__menu--first">
            <div>Информация</div>
            <ul>
                <li><a href = "/page/about" title = "">О компании</a></li>
                <li><a href = "/article/index" title = "">Новости</a></li>
                <li><a href = "/page/philanthropy" title = "">Благотворительность</a></li>
                <li><a href = "/page/getmoney" title = "">Как получить выигрыш</a></li>
                <li><a href = "/site/partner" title = "">Стать партнером</a></li>
                <li><a href = "/site/contact" title = "">Контакты</a></li>
            </ul>
        </div>

        <div class = "footer__menu footer__menu--second">
            <div>Личный кабинет</div>
            <ul>
                <li><a href = "/user/default/index" title="Мой профиль">Мой профиль</a></li>
                <li><a href = "/user/default/finance" title="Финансы">Финансы</a></li>
                <li><a href = "/user/default/referrals" title = "Мои друзья">Мои друзья</a></li>
                <li><a href = "/user/default/stat" title = "Статистика игр">Статистика игр</a></li>
                <li><a href = "/site/invite-friend" title = "Пригласить друга">Пригласить друга</a></li>
            </ul>
        </div>

        <div class = "footer__subscribe">
            <div class = "footer__subscribe-name">Хотите узнавать о всех суперрозыгрышах первыми?</div>
            <?php
            $form = ActiveForm::begin([
                        'action'      => Url::to(['/subscription']),
//                        'layout' => 'inline',
                        'fieldConfig' => [
                            'template' => '{input}',
                            'options'  => [
                                'tag' => false,
                            ],
                        ],
                        'options'     => ['class' => 'footer__subscribe-form'],
//                        'view' => 'subscribe',
            ]);
            echo $form->field($model, 'email', [ 'enableLabel' => false, 'inputOptions' => ['placeholder' => 'Ваш e-mail', 'type' => 'email', 'class' => 'footer__subscribe-input']])->textInput();
            echo Html::submitButton(Yii::t('frontend', 'Хочу!'), ['class' => 'footer__subscribe-button']);
            ActiveForm::end();
            ?>
            <span>
                Подпишитесь на&nbsp;
                нашу рассылку и&nbsp;
                вы&nbsp;
                сможете быть в&nbsp;
                курсе всех самых денежных розыгрышей <?=Yii::$app->name?>!
            </span>
        </div>

        <!--        <div class = "footer__social">
                    <div>Мы в соцсетях</div>
                    <i class = "footer__social-icon footer__social-icon--tw"></i>
                    <i class = "footer__social-icon footer__social-icon--vk"></i>
                    <i class = "footer__social-icon footer__social-icon--fb"></i>
                    <i class = "footer__social-icon footer__social-icon--ag"></i>
                    <i class = "footer__social-icon footer__social-icon--ok"></i>
                </div>-->

        <div class = "footer__copyright">
            © Все права защищены. <?= Yii::$app->name ?>. <?= date('Y') ?>.
        </div>
    </div>
</footer>

<div class="color-lines">
    <div class="color-lines__green color-lines__green--left"></div>
    <div class="color-lines__red color-lines__red--first"></div>

    <div class="color-lines__red color-lines__red--second"></div>
    <div class="color-lines__blue"></div>
</div>

<?php $this->endContent() ?>