<?php

use yii\bootstrap\Html;
use common\components\lottery\models\L6x45;
use common\components\lottery\models\L1x3;
use common\components\lottery\models\L3x9;
use common\models\UserAccountStat;
use common\components\slots\models\SlotsFiveTicket;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = Yii::t('frontend', 'Финансы');
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];
?>
<h1><?= $this->title ?></h1>

<div class="white-block finance">
    Баланс вашего аккаунта
    <div class="finance__summ"><?= $model->userProfile->_account ?></div>
    <a href="/payment" title="">Пополнить баланс</a>
    <a href="/payment/payout" title="">Снять деньги</a>
</div>

<h2>История операций</h2>

<?php
echo $this->render('_search_finance', ['model' => $searchModel]);

echo yii\grid\GridView::widget([
    'dataProvider' => $dataProvider,
    'tableOptions' => [
        'class' => 'finance__table'
    ],
    'pager' => [
//                'pageCssClass' => 'pull-right',
        'options' => [
            'class' => 'finance__nav',
        ]
    ],
    'columns' => [
        [
            'label' => "Дата",
            'format' => 'date',
            'value' => function ($model) {
                return (int) $model->log_time;
            }
        ],
//        'message:text:Операция',
//        'category:text:Сумма',
        [
            'label' => 'Операция',
            'content' => function($model, $key) {
                $res = $model->message;
                if (preg_match('/6x45[\w\s]+bet/', $model->message)) {
                    $res = 'Ставка в лотерею <br>' . L6x45::NAME;
                } elseif (preg_match('/1x3[\w\s]+bet/', $model->message)) {
                    $res = 'Ставка в лотерею <br>' . L1x3::NAME;
                } elseif (preg_match('/3x9[\w\s]+bet/', $model->message)) {
                    $res = 'Ставка в лотерею <br>' . L3x9::NAME;
                } elseif (preg_match('/slots_five[\w\s]+bet/', $model->message)) {
                    $res = 'Ставка в слоты  <br>' . SlotsFiveTicket::NAME;
                } elseif (preg_match('/6x45[\w\s]+win/', $model->message)) {
                    $res = 'Выигрыш в лотерею <br>' . L6x45::NAME;
                } elseif (preg_match('/1x3[\w\s]+win/', $model->message)) {
                    $res = 'Выигрыш в лотерею <br>' . L1x3::NAME;
                } elseif (preg_match('/3x9[\w\s]+win/', $model->message)) {
                    $res = 'Выигрыш в лотерею <br>' . L3x9::NAME;
                } elseif (preg_match('/slots_five[\w\s]+win/', $model->message)) {
                    $res = 'Выигрыш в слоты  <br>' . SlotsFiveTicket::NAME;
                } elseif (preg_match('/Referral payment/', $model->message)) {
                    $res = 'Реферальные начисления';
                } elseif (preg_match('/Perfect Money incoming payment/', $model->message)) {
                    $res = 'Зачисление средств<br>Perfect Money';
                } elseif (preg_match('/Perfect Money payout/', $model->message)) {
                    $res = 'Вывод средств<br>Perfect Money';
                } elseif (preg_match('/Perfect Money transaction rollback/', $model->message)) {
                    $res = 'Отмена вывода средств<br>Perfect Money';
                } elseif (preg_match('/Payeer incoming payment/', $model->message)) {
                    $res = 'Зачисление средств<br>Payeer';
                } elseif (preg_match('/Payeer payout/', $model->message)) {
                    $res = 'Вывод средств<br>Payeer';
                } elseif (preg_match('/Payeer transaction rollback/', $model->message)) {
                    $res = 'Отмена вывода средств<br>Payeer';
                } elseif (preg_match('/Payout cancel/', $model->message)) {
                    $res = 'Отмена вывода средств';
                } elseif (preg_match('/F-Change incoming payment/', $model->message)) {
                    $res = 'Зачисление средств<br>F-Change';
                } else {
                    if (preg_match('/\[(\D+)\]/', $model->message, $tmp)) {
                        $res = $tmp[1];
                    } else {
                        $res = '-';
                    }
                }
                return $res;
            }
        ],
        [
            'label' => 'Тип',
            'content' => function($model) {
                $res = '';
                if (strpos($model->category, 'withdraw')) {
                    $res = 'Списано';
                } elseif (strpos($model->category, 'charge')) {
                    $res = 'Начислено';
                } elseif (strpos($model->category, 'noaction')) {
                    $res = 'Отменено';
                }
                return $res;
            }
        ],
        [
            'label' => 'Сумма',
            'content' => function($model, $key) {
                preg_match_all('/\[([\d\.]+)\]/', $model->message, $res);
                return isset($res[1][1]) ? Yii::$app->formatter->asCurrency($res[1][1]) : '&nbsp;';
            }
        ],
        [
            'label' => 'Остаток',
            'content' => function($model, $key) {
                preg_match_all('/\[([\d\.]+)\]/', $model->message, $res);
                return isset($res[1][2]) ? Yii::$app->formatter->asCurrency($res[1][2]) : '&nbsp;';
            }
        ],
    ]
]);

if ($dataProviderAccount->getCount()) {
    echo '<a name="postponed"></a><h2>Запросы на вывод денежных средств</h2>';

    echo yii\grid\GridView::widget([
        'dataProvider' => $dataProviderAccount,
        'tableOptions' => [
            'class' => 'finance__table'
        ],
        'pager' => [
//                'pageCssClass' => 'pull-right',
            'options' => [
                'class' => 'finance__nav',
            ]
        ],
        'columns' => [
            'created_at:date:Дата',
//            'system:text:Платежная система',
            [
                'label' => 'Платежная система',
                'format' => 'html',
                'value' => function ($model) {
                    return implode('<br>', [$model->system, $model->target]);
                }
                    ],
                    'amount:currency:Сумма',
                    [
                        'label' => "Состояние",
                        'format' => 'text',
                        'value' => function ($model) {
                            switch ($model->status) {
                                case UserAccountStat::STATUS_REQUESTED:
                                case UserAccountStat::STATUS_ERRORED:
                                    $res = Yii::t('frontend', 'Ожидает вашего подтверждения');
                                    break;
//                        case UserAccountStat::STATUS_ERRORED:
//                            $res = Yii::t('frontend', 'Ошибка');
//                            break;
                                default:
                                    $res = Yii::t('frontend', 'На проверке');
                                    break;
                            }
//                    return $model->status;
                            return $res;
                        }
                    ],
                    [
                        'label' => "Действие",
                        'format' => 'html',
                        'value' => function ($model) {
                            $res = [];
                            if ($model->status == UserAccountStat::STATUS_ERRORED) {
                                $res[] = Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-repeat']), ['confirm-payout', 'id' => $model->id], ['class' => 'text-success', 'title' => 'Повторить']);
                            }
                            if ($model->status == UserAccountStat::STATUS_REQUESTED) {
                                $res[] = Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-ok']), ['confirm-payout', 'id' => $model->id], ['class' => 'text-success', 'title' => 'Подтвердить']);
                            }
                            $res[] = Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']), ['cancel-payout', 'id' => $model->id], ['class' => 'text-danger', 'title' => 'Отменить']);
                            return
                                    implode('&nbsp;', $res);
                        }
                            ],
                        ]
                    ]);
                }