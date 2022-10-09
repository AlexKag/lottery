<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use frontend\assets\BootboxAsset;
use frontend\models\InviteFriendForm;

//For Popover
//\yii\web\JqueryAsset::register($this);
//\yii\bootstrap\BootstrapPluginAsset::register($this);

BootboxAsset::overrideSystemConfirm();

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title                   = Yii::t('frontend', 'Мои друзья');
$this->params['breadcrumbs'][] = ['label' => 'Личный кабинет', 'url' => ['index']];
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];
?>
<h1><?= $this->title ?></h1>

<div class="finance__title-before">
    Приглашенные друзья:
    <ul>
        <li>1 уровень: <?= (int) $refs[1] ?></li>
        <li>2 уровень: <?= (int) $refs[2] ?></li>
        <li>3 уровень: <?= (int) $refs[3] ?></li>
    </ul>
</div>
<hr>
<?=
GridView::widget([
    'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
//        'filterPosition' => GridView::FILTER_POS_HEADER,
    'pager'        => [
//                'pageCssClass' => 'pull-right',
        'options' => [
            'class' => 'finance__nav',
        ]
    ],
    'columns'      => [
//            ['class' => 'yii\grid\SerialColumn'],
        [
            'attribute' => 'created_at',
            'format'    => ['date', 'LLLL Y'],
            'label'     => 'Период'
        ],
//            'dt:text:Период',
        'paid_in:currency:Потрачено друзьями',
        'paid_ref:currency:Начислено вам', //FIXME Исправить расчёт начислений, включить формулу начислений по уровням
    ],
    'tableOptions' => [
        'class' => 'finance__table finance__table--static'
    ],
]);

//Заработок за 6 месяцев
$dataProvider->pagination->page = 0;
$res                            = $dataProvider->getModels();
$res                            = array_slice($res, 0, 6);
$sum                            = array_reduce($res, function($sum, $item) {
    $sum += $item['paid_ref'];
    return $sum;
});
?>

<div class="finance__title-after">
    Вы заработали благодаря своим друзьям за последние 6 месяцев: <span><?= Yii::$app->formatter->asCurrency((real) $sum) ?></span>
</div>
<?=
$this->render('_referral_link', [
    'model' => $model,
]);
?>
<hr>
<?php
echo Html::a('Пригласить друга', '#', ['class' => 'btn btn--default btn-lg col-md-offset-3', 'role' => 'button', 'id' => 'invite_friend']);

$modelForm  = new InviteFriendForm(['name' => $model->publicIdentity]);
$inviteHtml = $this->render('@frontend/views/site/_invite_friend_form', [
    'model'   => $modelForm,
    'isGuest' => false,
    'action'  => Url::to('/site/invite-friend')
        ]);
$inviteHtml = str_replace(["\r", "\n"], ' ', $inviteHtml);
Url::remember('', 'referrals');

$js = <<<JS
$('#invite_friend').on('click', function(e){
    var dialog = bootbox.dialog({
        title: '<h1 class="text-center">Пригласить друга</h1>',
        message: '{$inviteHtml}',
        closeButton: true
    });
});
JS;

$this->registerJs($js);
