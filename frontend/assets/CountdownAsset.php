<?php

namespace frontend\assets;

//use Yii;
use yii\web\AssetBundle;

class CountdownAsset extends AssetBundle
{

    public $sourcePath = '@vendor/bower/jquery.countdown/dist';
    public $js         = [
        'jquery.countdown.min.js',
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
