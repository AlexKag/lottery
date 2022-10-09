<?php

namespace frontend\assets;

//use Yii;
use yii\web\AssetBundle;

class TetherAsset extends AssetBundle
{

    public $sourcePath = '@vendor/npm/tether/dist';
    public $js         = [
        'js/tether.min.js',
    ];
    public $css        = [
        'css/tether.min.css',
        'css/tether-theme-arrows.min.css',
//        'css/tether-theme-arrows-dark.min.css',
        'css/tether-theme-basic.min.css',
    ];

//    public static function overrideSystemConfirm()
//    {
//        Yii::$app->view->registerJs('
//            yii.confirm = function(message, ok, cancel) {
//                bootbox.confirm(message, function(result) {
//                    if (result) { !ok || ok(); } else { !cancel || cancel(); }
//                });
//            }
//        ');
//    }

}
