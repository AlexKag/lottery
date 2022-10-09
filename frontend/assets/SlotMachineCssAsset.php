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
class SlotMachineCssAsset extends AssetBundle
{

    public $sourcePath  = '@common/components/jQuery.jSlots';
//    public $baseUrl   = '@web';
    public $css       = [
        'slots.css',
    ];
////    public $js        = [
////        'jquery.jSlots.min.js',
////    ];
//    public $jsOptions = ['position' => \yii\web\View::POS_END];
//    public $depends   = [
//        'yii\web\JqueryAsset',
//        'frontend\assets\JQueryEasingAsset',
//    ];

}
