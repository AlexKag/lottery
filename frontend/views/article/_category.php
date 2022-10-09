<?php

/**
 * @var $this yii\web\View
 * @var $model common\models\ArticleCategory
 */
use yii\helpers\Html;

echo Html::a($model->title, ['article/index', 'ArticleSearch[category_id]' => $model->id]);
