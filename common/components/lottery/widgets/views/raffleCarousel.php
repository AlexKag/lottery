<?php

use yii\helpers\Html;

$content = '';
$bannerClassActive = ' banner__slide--active';

$cnt = count($items);
$slidesCnt = ceil($cnt / $itemsOnSlide);

$slides = [];
//$i = 0;
foreach ($items as $key => $item) {
    $content .= $this->render('_raffleItem', [
        'item' => $item,
        'key' => $key
    ]);
    if (($key + 1) % $itemsOnSlide == 0 || $key == $cnt - 1) {
        $slides[] = Html::tag('div', $content, [
                    'class' => 'banner__slide' . $bannerClassActive
        ]);
        $content = '';
        $bannerClassActive = '';
    }
}
echo Html::tag('div', implode('', $slides), ['class' => 'banner__wrap']);

$this->registerJs($js, \yii\web\View::POS_READY);
