<?php

use common\models\Page;
use yii\helpers\Html;
use yii\helpers\Url;

//For Popover
\yii\web\JqueryAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);

$this->title                   = 'Как это работает';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];

//как это работает
$article = Page::find()->where(['slug' => 'howitworks', 'status' => Page::STATUS_PUBLISHED])->one();

if ($article) {
    echo "<h2>$article->title</h2>";
    echo $article->body;
}

echo Html::a('Как получить выигрыш', Url::to(['/page/getmoney']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
echo '&nbsp;';
echo Html::a('Стать партнером', Url::to(['/site/partner']), ['class' => 'btn btn-primary btn-lg', 'role' => 'button']);
echo $this->render('@frontend/views/site/_button_back');
