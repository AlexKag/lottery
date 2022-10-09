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
class JQueryEasingAsset extends AssetBundle
{

    public $sourcePath = '@vendor/bower/jquery.easing/js';
    public $baseUrl    = '@web';
    public $js         = [
        'jquery.easing.min.js',
    ];
    public $jsOptions  = ['position' => \yii\web\View::POS_END];
    public $depends    = [
        'yii\web\JqueryAsset',
    ];

}
