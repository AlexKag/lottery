<?php

use yii\widgets\ListView;
use common\widgets\ListView as SimpleListView;
use common\widgets\DbCarouselNews;
use kop\y2sp\ScrollPager;
use yii\data\Pagination;

/* @var $this yii\web\View */
$this->title                   = Yii::t('frontend', 'Новости');
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<div class="container">
    <?php
    echo DbCarouselNews::widget([
        'key'          => 'news',
        'options'      => [
            'class' => 'banner banner--news', // enables slide effect
        ],
        'itemsOptions' => [
            'class' => 'banner__slide',
        ],
        'caption'      => 'Главные новости',
        'controls'     => [
            '<a title="" href="#" class="banner__arrow banner__arrow--left banner__arrow--news-left"></a>',
            '<a title="" href="#" class="banner__arrow banner__arrow--right banner__arrow--news-right"></a>'
        ],
    ]);

    echo SimpleListView::widget([
        'dataProvider' => $dataProvider2,
        'summary'      => '',
        'itemView'     => '_category',
        'layout'       => '{items}',
        'separator'    => '&nbsp;',
        'options'      => [
            'class' => 'news__filter'
        ],
//        'itemOptions' => [
//            'class' => 'inline',
//        ]
    ]);
    ?>
    <h3>Все новости</h3>
    <?=
    ListView::widget([
        'dataProvider' => $dataProvider,
//        'summary' => '',
        'layout'       => '{items}{pager}',
        'options'      => [
            'class' => 'news__wrap',
        ],
        'itemView'     => '_item',
        'itemOptions'  => [
            'class' => 'news__item',
        ],
        'pager'        => [
//            'hideOnSinglePage' => true,
            'class'            => ScrollPager::className(),
            'container'        => '.news__wrap',
            'item'             => '.news__item',
            'triggerText'      => 'Показать больше новостей',
            'triggerTemplate'  => '<div class="clearfix"></div><a class="news__more" style="text-align: center;">{text}</a>',
            'noneLeftText'     => 'Конец',
            'noneLeftTemplate' => '<div class="clearfix"></div><br><div class="alert alert-info" style="text-align: center;">{text}</div>',
//            'enabledExtensions' => [
//                ScrollPager::EXTENSION_TRIGGER,
//ScrollPager::EXTENSION_PAGING,
//            ],
//            'pagination' => [
//                'class' => Pagination::className(),
//                'pageSize' => 4,
//            ],
        ],
    ])
    ?>

    <!--<a title="" href="#" class="news__more">Показать больше новостей »</a>-->
</div>
