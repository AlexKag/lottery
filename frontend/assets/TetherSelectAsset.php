<?php

namespace frontend\assets;

//use Yii;
use yii\web\AssetBundle;

class TetherSelectAsset extends AssetBundle
{

    public $sourcePath = '@vendor/npm/tether-select/dist';
    public $js         = [
        'js/select.min.js',
    ];
    public $css        = [
        'css/select-theme-default.css',
    ];
    public $depends = [
        'frontend\assets\TetherAsset',
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
