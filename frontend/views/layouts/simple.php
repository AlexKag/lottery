<?php

/* @var $this \yii\web\View */
/* @var $content string */

use kartik\alert\AlertBlock;
use kartik\growl\Growl;

$this->beginContent('@frontend/views/layouts/header&footer.php');
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

<?php $this->endContent(); ?>