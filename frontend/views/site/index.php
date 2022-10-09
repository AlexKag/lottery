<?php

use common\components\lottery\widgets\LotteryCarouselWidget;
use yii\helpers\Html;
use common\widgets\DbCarouselIndex;
use common\models\Page;

$sumParts = numberExplode($jackpot['summ']);
$digits   = function($val) {
    $cnt = strlen($val);
    switch ($cnt) {
        case 1:
            return 'one';
            break;
        case 2:
            return 'two';
            break;
        case 3:
            return 'three';
            break;
        default:
            return null;
    }
};
?>
<main>
    <?=
    DbCarouselIndex::widget([
        'key'          => 'index',
        'options'      => [
            'class' => 'banner banner--main', // enables slide effect
        ],
        'itemsOptions' => [
            'class' => 'banner__slide',
        ],
        'controls'     => [
            '<a href="#" class="banner__arrow banner__arrow--left banner__arrow--main-left" title="Предыдущий"></a>',
            '<a href="#" class="banner__arrow banner__arrow--right banner__arrow--main-right" title="Следующий"></a>'
        ],
    ]);
    ?>
</div>
</div>


<div class="jackpot">
    <div class="jackpot__summ">
        <div class="jackpot__summ-item jackpot__summ-item--<?php echo $digits($sumParts['millions'])?>"><?= $sumParts['millions'] ?></div>
        <div class="jackpot__summ-item jackpot__summ-item--<?=$digits($sumParts['thousands'])?>"><?= $sumParts['thousands'] ?></div>
        <div class="jackpot__summ-item jackpot__summ-item--<?=$digits($sumParts['ones'])?>" id="jackpot__summ-item"><?= $sumParts['ones'] ?></div>
    </div>
    <div class="jackpot__next">
        <?php
        printf('Следующий розыгрыш состоится <span>%s в 13:00 МСК</span>', Yii::$app->formatter->asDate($jackpot['draw_at']));
        $js      = <<<JS
// Джекпот и пожертвования
summ();

function summ(){
    var count = Math.round(parseInt(Date.now().toString().substr(7,3))/2);
    if (count > 900 || count < 100) count += 100;
    $("#jackpot__summ-item").html(count);

    var charity = Math.round(parseInt(Date.now().toString().substr(6,3))/2);
    if (charity > 900 || charity < 100) charity += 100;
    $("#charity__summ-item").html(charity);

    setTimeout(summ,1000);
}
JS;
        $this->registerJs($js);
        ?>
    </div>
</div>


<div class="charity">
    <div class="charity__name">Каждый десятый рубль с продажи лотерейных билетов</div>
    <div class="charity__desc">Идет на благотворительность!</div>
    <div class="charity__summ">
        Уже собрано
        <div class="charity__summ-item">238</div>
        <div class="charity__summ-item" id="charity__summ-item">700</div>
    </div>
</div>


<div class="banner__name">Актуальные розыгрыши</div>
<div class="banner banner--topical">
    <div class="container">
        <div class="banner__wrap banner__wrap--topical">

            <a class="banner__raffle banner__raffle--left" href="/site/random-game">Поучаствовать наугад в любой лотерее</a>
            <a class="banner__raffle banner__raffle--right" href="/site/lotteries">Самые популярные</a>
            <a class="banner__raffle banner__raffle--right" href="/site/lotteries">Список всех лотерей</a>

            <a href="#" class="banner__arrow banner__arrow--left banner__arrow--topical-left" title="Previous slide"></a>
            <a href="#" class="banner__arrow banner__arrow--right banner__arrow--topical-right" title="Next slide"></a>
            <?=
            LotteryCarouselWidget::widget([
                'items' => $items,
            ]);
            ?>
        </div>
    </div>
</div>


<div class="about-project">
    <div class="container">
        <!--            <div class="about-project__name">О ПРОЕКТЕ <?=Yii::$app->name ?></div>-->
        <?php
        $article = Page::find()->where(['slug' => 'about_brief', 'status' => Page::STATUS_PUBLISHED])->one();
        echo $article->body;
        if (Yii::$app->user->isGuest) {
            echo Html::a('<i class="fa fa-user" aria-hidden="true"></i> Зарегистрироваться', '/user/sign-in/signup', ['class' => 'btn btn--danger']);
        }
        else {
            echo Html::a('<i class="fa fa-arrow-circle-o-right" aria-hidden="true"></i> Играть', '/site/random-game', ['class' => 'btn btn--danger']);
        }
        ?>
    </div>
</div>


<div class="advantages">
    <div class="container">
        <div class="advantages__name">Наши преимущества</div>

        <div class="advantages__item advantages__item--high">
            Крупные денежные призы
            <span></span>
        </div>
        <div class="advantages__item advantages__item--everyday">
            Ежедневные розыгрыши
            <span></span>
        </div>
        <div class="advantages__item advantages__item--control">
            100% контроль честности лотереи
            <span></span>
        </div>
        <div class="advantages__item advantages__item--warranty">
            100% гарантия выплат по выигрышам
            <span></span>
        </div>
        <div class="advantages__item advantages__item--support">
            Оперативная поддержка участников
            <span></span>
        </div>
    </div>
</div>
</main>
