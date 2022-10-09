<?php

use yii\helpers\Html;
?>
<div class="raffle">
    <div class="raffle__wrap">
        <div class="raffle__img"><img src="<?= $item['logo_url'] ?>"></div>
        <div class="raffle__name"><?= $item['name'] ?></div>
        <div class="raffle__time">
            <?php
            if (isset($item['countdown_timestamp'])) {
                if (is_numeric($item['countdown_timestamp'])) {
                    echo 'Ближайший розыгрыш через' . Html::tag('div', '', ['class' => 'raffle__count', 'id' => 'raffle__count-' . $key]);
                } else {
                    echo Html::tag('div', $item['countdown_timestamp'], ['class' => 'raffle__count']);
                }
            }
            ?>
        </div>
        <div class="raffle__prize">
            <?php
            if (isset($item['superprize'])) {
                echo 'Суперприз' . Html::tag('div', $item['superprize'], ['class' => 'raffle__prize-summ']);
            }
            ?>
        </div>
        <a class="btn btn--raffle" href="<?= $item['game_url'] ?>" title="Купить билет">Купить билет</a>
    </div>
</div>
