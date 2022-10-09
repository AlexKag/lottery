<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Json;
use yii\helpers\Url;
use common\components\lottery\models\L6x45Ticket;
use common\components\lottery\models\L1x3Ticket;
use common\components\lottery\models\L3x9Ticket;
use common\components\slots\models\SlotsFiveTicket;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = Yii::t('frontend', 'Статистика игр');
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<div class="user-tickets-form">
    <h1><?= $this->title ?></h1>
    <?php
    echo $this->render('_search_stat', ['model' => $searchModel]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
//        'filterPosition' => GridView::FILTER_POS_HEADER,
        'pager' => [
//                'pageCssClass' => 'pull-right',
            'options' => [
                'class' => 'finance__nav',
            ]
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
//            'draw_at:date:Дата розыгрыша',
            [
                'label' => 'Лотерея',
                'content' => function($model, $key) {
                    $name      = Yii::t('common', $model['name']);
                    $tagConfig = [
                        'class' => 'label label-success',
                        'data-toggle' => 'tooltip',
                        'title' => "Тираж №{$model['lottery_id']}",
                    ];
                    switch ($model['type']) {
                        case L6x45Ticket::ID:
//                            $text = empty($model['draw']) ? Url::to("/lottery{$model['type']}/list") : [Url::to("/lottery{$model['type']}/view"), 'id' => $model['lottery_id']];
                            $text = empty($model['draw']) ? Html::tag('span', '«' . $name . '»', ['class' => 'label label-info'] + $tagConfig) : Html::a('«' . $name . '»', [Url::to("/lottery{$model['type']}/view"), 'id' => $model['lottery_id']], $tagConfig);
                            break;
                        case L1x3Ticket::ID:
                        case L3x9Ticket::ID:
                        default :
                            $text = Html::tag('span', '«' . $name . '»', [
                                        'class' => 'label label-info',
                                        'data-toggle' => 'tooltip',
                                        'title' => "Игра №{$model['lottery_id']}",
                            ]);
                    }

                    return sprintf('<span class="label label-default">%s</span>&nbsp;%s', Yii::$app->formatter->asDate($model['draw_at']), $text);
                }
                    ],
//            [
//                'label'   => 'Лотерея',
//                'content' => function($model, $key) {
//                    $name = Yii::t('common', $model['name']);
//                    switch ($model['type']) {
//                        case L6x45Ticket::ID:
//                            $text = Html::a($name, Url::to("/lottery{$model['type']}/list"));
//                            break;
//                        case L1x3Ticket::ID:
//                        case L3x9Ticket::ID:
//                        default :
//                            $text = $name;
//                    }
//                    return $text;
//                }
//            ],
//                    [
//                        'label'   => 'Тираж',
//                        'content' => function($model, $key) {
//                            $id = $model['lottery_id'];
//                            switch ($model['type']) {
//                                case L6x45Ticket::ID:
//                                    $text = empty($model['draw']) ? $id : Html::a($id, [Url::to("/lottery{$model['type']}/view"), 'id' => $id]);
//                                    break;
//                                case L1x3Ticket::ID:
//                                case L3x9Ticket::ID:
//                                default :
//                                    $text = $id;
//                            }
//                            return $text;
//                        }
//                            ],
//            [
//                'label'   => 'Тираж',
//                'content' => function($model, $key) {
//                    $id = $model['lottery_id'];
//                    switch ($model['type']) {
//                        case L6x45Ticket::ID:
//                            $text = empty($model['draw']) ? $id : Html::a($id, [Url::to("/lottery{$model['type']}/view"), 'id' => $id]);
//                            break;
//                        case L1x3Ticket::ID:
//                        case L3x9Ticket::ID:
//                        default :
//                            $text = $id;
//                    }
//                    return $text;
//                }
//                    ],
//                    [
//                        'label'   => Yii::t('frontend', 'Выигрышная комбинация'),
//                        'content' => function($model, $key) {
//                            try {
//                                $draw = Json::decode($model['draw']);
//                                $draw = implode('&nbsp;', $draw);
//                            } catch (Exception $ex) {
//                                $draw = is_numeric($model['draw']) ? $model['draw'] : '';
//                            }
//                            return $draw;
//                        }
//                    ],
                    [
                        'label' => 'Билет',
                        'content' => function($model, $key) {
                            $class = $model['win_cnt'] > 0 ? 'label label-success' : 'label label-info';
                            $text  = Html::tag('span', $model['id'], [
                                        'data-toggle' => 'tooltip',
                                        'title' => sprintf("Куплен %s. Цена %s.", Yii::$app->formatter->asDate($model['created_at']), Yii::$app->formatter->asCurrency($model['paid'])),
                                        'class' => $class
                            ]);
//                            $text = sprintf("№&nbsp;%d\n от&nbsp;%s\n%s", $model['id'], Yii::$app->formatter->asDate($model['created_at']), Yii::$app->formatter->asCurrency($model['paid']));
                            switch ($model['type']) {
                                case L6x45Ticket::ID:
                                    $text = Html::a($text, ["/lottery{$model['type']}/check-ticket", 'ticket_id' => $model['id']]);
                                    break;
                                case L1x3Ticket::ID:
                                case L3x9Ticket::ID:
                                default :
//                                    $text = $model['name'];
                            }
                            return $text;
                        }
                            ],
                            [
                                'label' => Yii::t('frontend', 'Ставка'),
//                'attribute' => 'lottery._draw',
                                'content' => function($model, $key) {
                                    try {
                                        $bet = Json::decode($model['bet']);
                                        sort($bet);
                                        $bet = implode('&nbsp;', $bet);
                                    } catch (Exception $ex) {
                                        $bet = is_numeric($model['bet']) ? $model['bet'] : '';
                                    }

                                    try {
                                        $draw = Json::decode($model['draw']);
                                        sort($draw);
                                        $draw = implode(' ', $draw);
                                    } catch (Exception $ex) {
                                        $draw = is_numeric($model['draw']) ? $model['draw'] : '';
                                    }

                                    $class = $model['win_cnt'] > 0 ? 'label label-success' : 'label label-info';
                                    switch ($model['type']) {
                                        case SlotsFiveTicket::ID:
                                            $bet  = Json::decode($model['bet']);
                                            $bet  = sprintf('По %s на %d линий', Yii::$app->formatter->asCurrency($bet['denomination'] * $bet['betPerLine']), $bet['linesCount']);
                                            $wins = Json::decode($model['draw']);
                                            $draw = [];
                                            foreach ($wins as $key=>$win){
                                                $draw[]= sprintf('линия %d [%d Х %s]', $key, $win['count'], $win['sign']);
                                            }
                                            $draw = implode(', ', $draw);
                                            return Html::tag('span', $bet, [
                                                        'class' => $class,
                                                        'data-toggle' => 'tooltip',
                                                        'data-title' => empty($draw) ? 'Без выигрыша' : 'Выигрыш: ' . $draw,
                                            ]);
                                            break;
                                        case L1x3Ticket::ID:
                                        case L3x9Ticket::ID:
                                        default :
                                            return Html::tag('span', $bet, [
                                                        'class' => $class,
                                                        'data-toggle' => 'tooltip',
//                                                'data-title' => 'Выигрышная комбинация',
//                                                'data-content' => empty($draw) ? 'Не сыграно' : $draw,
                                                        'data-title' => empty($draw) ? 'Не сыграно' : 'Выиграла комбинация: ' . $draw,
                                            ]);
                                    }
                                }
                                    ],
                                    [
                                        'label' => Yii::t('frontend', 'Результат'),
                                        'content' => function($model, $key) {
                                            if (is_null($model['draw'])) {
                                                return 'Не сыграно';
                                            } else {
                                                return $model['win_cnt'] > 0 ? "Выиграл [{$model['win_cnt']}]" : 'Проиграл';
                                            }
                                        },
                                        'contentOptions' => function ($model, $key, $index, $column) {
                                            if (is_null($model['draw'])) {
                                                return [];
                                            } else {
                                                return $model['win_cnt'] > 0 ? ['class' => 'finance__table-win'] : ['class' => 'finance__table-lose'];
                                            }
                                        },
                                            ],
                                            'paid_out:currency:Выигрыш',
                                        ],
                                        'tableOptions' => [
                                            'class' => 'finance__table finance__table--tickets'
                                        ],
                                    ]);
                                    ?>
                                </div>
                                <?php
                                \yii\web\JqueryAsset::register($this);
                                \yii\bootstrap\BootstrapPluginAsset::register($this);
                                $js = <<< 'SCRIPT'
/* Initialize BS3 tooltips */
$(function () {
    $('body').tooltip({selector: '[data-toggle="tooltip"]'});
});
/* To initialize BS3 popovers set this below */
$(function () {
    $('body').popover({selector: '[data-toggle="popover"]'});
});
SCRIPT;
// Register tooltip/popover initialization javascript
                                $this->registerJs($js);
                                