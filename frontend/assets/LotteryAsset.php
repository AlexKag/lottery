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
class LotteryAsset extends AssetBundle
{

    public $basePath  = '@webroot';
    public $baseUrl   = '@web';
    public $css       = [
        'css/style.css',
        'css/custom.css',
    ];
    public $js        = [
        'js/script.js',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_END];
    public $depends   = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset',
        'frontend\assets\TetherSelectAsset',
        'yii\bootstrap\BootstrapAsset',
//        'common\assets\Html5shiv',
        'frontend\assets\BootboxAsset',
        'frontend\assets\CountdownAsset',
//        'raoul2000\bootswatch\BootswatchAsset',
    ];

}
