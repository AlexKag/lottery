<?php

use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\helpers\Json;
use common\components\lottery\models\BaseLottery;

$this->title                   = 'Все лотереи';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];
$l6x45                         = $lotteries['l6x45'];
//$l6x45prev                     = $lotteries['l6x45prev'];
$tagAOptions                   = ['role' => 'button', 'class' => 'btn btn-default btn-block'];
?>
<div class="row">
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            <p class="text-center"><small>Мгновенная игра!</small></p>
            <?= Html::a(Html::img('/img/lotto_4.png', ['alt' => "1 из 3"]), '/instant1x3/index'); ?>
            <div class="caption">
                <h2 class="text-danger text-center">&laquo;1 из 3&raquo;</h2>
                <p class="text-center">Чувствуешь, что удача с тобой? Испытай её здесь и сейчас!</p>
                <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) .' Ставка от ' . Yii::$app->formatter->asCurrency(1), '/instant1x3/index', $tagAOptions) ?>
                <p></p>
                <div class="btn-group-vertical btn-block">
                    <?=
                    Html::a('Быстрая ставка', '/instant1x3/index', $tagAOptions + [
                        'data' => [
                            'method' => 'post',
                            'params' => ['betDefault' => true],
                        ]
                    ]);
                    ?>
                    <?= Html::a('Правила игры', '/page/rules1x3', $tagAOptions) ?>
                    <?php // Html::a('Статистика', '/page/rules1x3', $tagAOptions) ?>
                </div>
            </div>
        </div>
    </div>
    <?php if (isset($l6x45) && $l6x45 instanceof BaseLottery) { ?>
        <div class="col-sm-6 col-md-4">
            <div class="thumbnail">
                <?php
                $timeInterval = explode(',', Yii::$app->formatter->asDuration($l6x45->draw_at - time()));
                echo '<p class="text-center">'.Html::tag('small', "Розыгрыш тиража № $l6x45->id через " . array_shift($timeInterval)).'</p>';
                echo Html::a(Html::img('/img/lotto_2.png', ['alt' => "6 из 45"]), '/lottery6x45/index');
                $pricing      = Json::decode(Yii::$app->keyStorage->get('lottery.6x45.pricing'));
                $pricing      = array_shift($pricing);
                ?>
                <div class="caption">
                    <h2 class="text-danger text-center">&laquo;6 из 45&raquo;</h2>
                    <p class="text-center">Суперприз на ближайшую игру <?= $l6x45->superprizeReadable ?>. Испытай удачу и выиграй!</p>
                    <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) .' Ставка от ' . Yii::$app->formatter->asCurrency($pricing), '/lottery6x45/index', $tagAOptions) ?>
                    <p></p>
                    <div class="btn-group-vertical btn-block">
                        <?php
                        echo Html::a('Быстрая ставка', '/lottery6x45/index', $tagAOptions + [
                            'data' => [
                                'method' => 'post',
                                'params' => ['betDefault' => true],
                            ]
                        ]);
//                        if (isset($l6x45prev) && $l6x45prev instanceof BaseLottery) {
//                            echo Html::a("Результаты тиража №{$l6x45prev->id}", ['/lottery6x45/view', 'id' => $l6x45prev->id], $tagAOptions);
//                        }
                        echo Html::a('Архив тиражей', '/lottery6x45/list', $tagAOptions);
                        echo Html::a('Проверить билет', '/lottery6x45/check-ticket', $tagAOptions);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            <p class="text-center"><small>Мгновенная игра!</small></p>
            <?= Html::a(Html::img('/img/lotto_5.png', ['alt' => "3 из 9"]), '/instant3x9/index'); ?>
            <div class="caption">
                <h2 class="text-danger text-center">&laquo;3 из 9&raquo;</h2>
                <p class="text-center">Чувствуешь, что удача с тобой? Испытай её здесь и сейчас!</p>
                <?= Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) .' Ставка от ' . Yii::$app->formatter->asCurrency(1), '/instant3x9/index', $tagAOptions) ?>
                <p></p>
                <div class="btn-group-vertical btn-block">
                    <?=
                    Html::a('Быстрая ставка', '/instant3x9/index', $tagAOptions + [
                        'data' => [
                            'method' => 'post',
                            'params' => ['betDefault' => true],
                        ]
                    ]);
                    ?>
                    <?= Html::a('Правила игры', '/page/rules3x9', $tagAOptions) ?>
                    <?php // Html::a('Статистика', '/page/rules3x9', $tagAOptions) ?>
                </div>
            </div>
        </div>
    </div>
</div>