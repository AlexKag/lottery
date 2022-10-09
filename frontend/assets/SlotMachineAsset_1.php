<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SlotMachineAsset extends AssetBundle
{

    public $sourcePath  = '@vendor/bower/jquery-slotmachine/dist';
    public $css       = [
        'jquery.slotmachine.min.css',
        '/css/slots.css',
    ];
    public $js        = [
        'jquery.slotmachine.min.js',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_BEGIN];
    public $depends   = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
//        'frontend\assets\LotteryAsset',
    ];

}
