<?php

//use yii\bootstrap\Html;
use kartik\alert\AlertBlock;
use kartik\growl\Growl;
use yii\widgets\Breadcrumbs;

/* @var $this \yii\web\View */
/* @var $content string */

$this->beginContent('@frontend/views/layouts/header&footer.php');
?>

<main>
    <div class="banner banner--in">
        <div class="container">
            <?=
            Breadcrumbs::widget([
                'homeLink'     => ['label' => 'Главная', 'url' => '/'],
                'links'        => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                'activeItemTemplate' => "<li><span>{link}</span></li>\n",
                'options'      => [
                    'class' => 'breadcrumbs'
                ],
                'encodeLabels' => false
            ])
            ?>
            <h1><?= $this->title ?></h1>
        </div>
    </div>
    <?php
    $config = [
//            'title'         => '<h3>Upgrade your account</h3>',
        'showSeparator' => true,
//                    'icon'          => 'glyphicon glyphicon-ok-sign',
        'pluginOptions' => [
            'showProgressbar' => true,
            'delay'           => Yii::$app->params['growlNotificationShowTime'],
            'mouse_over'      => 'pause',
        ]
    ];
    AlertBlock::widget([
        'useSessionFlash' => true,
        'type'            => AlertBlock::TYPE_GROWL,
        'delay'           => 1000,
        'alertSettings'   => [
            'info'    => ['type' => Growl::TYPE_INFO] + $config,
            'success' => ['type' => Growl::TYPE_SUCCESS] + $config,
            'warning' => ['type' => Growl::TYPE_WARNING] + $config,
            'error'   => ['type' => Growl::TYPE_DANGER] + $config,
        ],
    ]);
    ?>

    <?= $content ?>

</main>
<?php $this->endContent(); ?>