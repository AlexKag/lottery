<?php

use yii\helpers\Html;
use frontend\assets\LotteryAsset;
use cybercog\yii\googleanalytics\widgets\GATracking;
use yii\widgets\YandexMetrikaCounter;

/* @var $this \yii\web\View */
/* @var $content string */

//raoul2000\bootswatch\BootswatchAsset::$theme = 'slate';
LotteryAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::$app->language ?>">
    <head>
        <meta charset="<?php echo Yii::$app->charset ?>"/>
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <?php echo Html::csrfMetaTags() ?>
        <link rel="icon" type="image/png" href="/favicon.png" />
        <link rel="apple-touch-icon" href="/favicon.png"/>
        <?php
        echo \common\widgets\DbText::widget([
            'key' => 'google-analytics'
        ]);
        echo \common\widgets\DbText::widget([
            'key' => 'og-sharing'
        ]);
        ?>
    </head>
    <body>
        <?php $this->beginBody() ?>

        <?=
        \common\widgets\DbText::widget([
            'key' => 'yandex-metrika'
        ]);
        ?>
        <?= $content ?>
        <?php $this->endBody() ?>
    </body>
</html>
<?php $this->endPage() ?>
