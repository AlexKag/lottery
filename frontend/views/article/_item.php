<?php

/**
 * @var $this yii\web\View
 * @var $model common\models\Article
 */
use yii\helpers\Html;
?>
<!--<a href="#">-->
<!--<div class="news__item">-->
    <div class="news__item-pic">
        <?php
        if ($model->thumbnail_path) {
            echo Html::img(
                    Yii::$app->glide->createSignedUrl([
                        'glide/index',
                        'path' => $model->thumbnail_path,
                        'w' => 168,
                        'h' => 265,
                            ], true), [
//                'class' => 'article-thumb img-rounded pull-left',
                'alt' => $model->title
                    ]
            );
        }
        ?>
        <div class="news__item-date"><?= Yii::$app->formatter->asDate($model->created_at) ?></div></div>
    <div class="news__item-name"><?= Html::a($model->title, ['view', 'slug' => $model->slug]) ?></div>
    <div class="news__item-text"><?= strip_tags(\yii\helpers\StringHelper::truncate($model->body, 100, '...', null, true)) ?></div>
<!--</div>-->
<!--</a>-->
