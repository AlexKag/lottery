<?php

$config = [
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => true, //FIXME Set to true in production (linux environment)
            'appendTimestamp' => YII_ENV_DEV
        ],
    ],
    'params' => [
        'maskMoneyOptions' => [
//        'prefix' => 'US$ ',
            'suffix' => ' $',
            'thousands' => ' ',
            'decimal' => '.',
            'precision' => 2,
            'allowZero' => false,
            'allowNegative' => false,
        ],
    ],
    'as locale' => [
        'class' => 'common\behaviors\LocaleBehavior',
        'enablePreferredLanguage' => true
    ]
];

if (YII_DEBUG) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
//        'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
    ];
}

if (YII_ENV_DEV) {
    $config['modules']['gii'] = [
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}


//Yii::$container->set(\yii\imperavi\Widget::className(), [
//    'options' => [
//        'minHeight' => 400,
//        'maxHeight' => 400,
//        'buttonSource' => true,
//        'removeEmptyTags' => false,
//        'removeWithoutAttr' => false,
////                'convertDivs'=>false,
//        'replaceDivs' => false,
//    ],
//]);

return $config;
