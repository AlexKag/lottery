<?php

$config = [
    'name' => 'FreedomLOTTO',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'sourceLanguage' => 'en-US',
    'language' => 'ru-RU',
    'bootstrap' => ['log'],
    'components' => [

        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => '{{%rbac_auth_item}}',
            'itemChildTable' => '{{%rbac_auth_item_child}}',
            'assignmentTable' => '{{%rbac_auth_assignment}}',
            'ruleTable' => '{{%rbac_auth_rule}}'
        ],
        'googleAuth' => [
            'class' => 'Google\Authenticator\GoogleAuthenticator',
//            'passCodeLength' => 6,
//            'secretLength'   => 16,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
            'cachePath' => '@common/runtime/cache'
        ],
        'commandBus' => [
            'class' => 'trntv\bus\CommandBus',
            'middlewares' => [
                [
                    'class' => '\trntv\bus\middlewares\BackgroundCommandMiddleware',
                    'backgroundHandlerPath' => '@console/yii',
                    'backgroundHandlerRoute' => 'command-bus/handle',
                ]
            ]
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru-RU',
            'dateFormat' => 'short',
            'nullDisplay' => '',
            #    'timeZone' => 'Europe/Kaliningrad',
            #'datetimeFormat' => 'php:d-m-Y H:i a',
            'datetimeFormat' => 'php:d-m-Y H:i',
            #    'timeFormat' => 'php:H:i A',
            'thousandSeparator' => '&nbsp;',
            'numberFormatterSymbols' => [
                NumberFormatter::CURRENCY_SYMBOL => '$'
            ],
            'decimalSeparator' => '.',
        ],
        'glide' => [
            'class' => 'trntv\glide\components\Glide',
            'sourcePath' => '@storage/../../home/lottery/storage/source',
            'cachePath' => '@storage/../../home/lottery/storage/cache',
//            'cachePath' => '@storage/cache',
            'urlManager' => 'urlManagerStorage',
            'maxImageSize' => env('GLIDE_MAX_IMAGE_SIZE'),
            'signKey' => env('GLIDE_SIGN_KEY')
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport'        => [
//                'class'         => 'Swift_SmtpTransport',
//                'host'          => 'mx.freedom-lotto.com',
//                'username'      => env('ROBOT_EMAIL'),
//                'password'      => 'eVv-w9D-PDx-Cyb',
//                'port'          => '587',
//                'encryption'    => 'tls',
//                'streamOptions' => [
//                    'ssl' => [
//                        'verify_peer'       => false,
//                        'verify_peer_name'  => false,
//                        'allow_self_signed' => true,
//                    ],
//                ],
//            ],
//            'messageConfig'    => [
//                'charset' => 'UTF-8',
//                'from'    => env('ROBOT_EMAIL')
            ]
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => env('DB_DSN'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'tablePrefix' => env('DB_TABLE_PREFIX'),
            'charset' => 'utf8',
            'enableSchemaCache' => YII_ENV_PROD,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                'db' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning'],
                    'except' => ['yii\web\HttpException:*', 'yii\i18n\I18N\*', 'payment\*', 'PerfectMoney', 'account\*', 'lottery\*'],
                    'prefix' => function () {
                $user   = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                $userId = $user ? $user->id : 'guest';
                $url    = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                $ip     = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userIP : null;
                $host   = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userHost : null;
                return sprintf('[%s][%s]-[%s][%s][%s]', Yii::$app->id, $userId, $url, $ip, $host);
            },
                    'logVars' => [],
                    'logTable' => '{{%system_log}}'
                ],
                'payment' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['payment\*', 'PerfectMoney'],
                    'logVars' => ['_POST', '_GET'],
                    'logTable' => '{{%payment_log}}',
                    'prefix' => function ($message) {
                $user   = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                $userId = $user ? $user->id : 'guest';
                $url    = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                $ip     = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userIP : null;
                $host   = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userHost : null;
                return sprintf('[%s][%s]-[%s][%s][%s]', Yii::$app->id, $userId, $url, $ip, $host);
            }
                ],
                'account' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['account\*'],
                    'logVars' => [],
                    'logTable' => '{{%account_log}}',
                    'prefix' => function ($message) {
//                die($message);
                if (preg_match('/\[(\d+)\]/', $message[0], $res)) {
                    $userId = $res[1];
                } else {
                    $user   = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                    $userId = $user ? $user->id : 0;
                }
                $url             = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->getUrl() : null;
                $ip              = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userIP : null;
                $host            = !Yii::$app->request->isConsoleRequest ? Yii::$app->request->userHost : null;
                return sprintf('[%s][%s]-[%s][%s][%s]', Yii::$app->id, $userId, $url, $ip, $host);
            }
                ],
                'game' => [
                    'class' => 'yii\log\DbTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['lottery\*'],
                    'logVars' => [],
                    'logTable' => '{{%gamedraw_log}}',
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                ],
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'fileMap' => [
                        'common' => 'common.php',
                        'backend' => 'backend.php',
                        'frontend' => 'frontend.php',
                        'payment' => 'payment.php',
                        'Payeer' => 'Payeer.php',
                    ],
                    'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
                ],
            /* Uncomment this code to use DbMessageSource
              '*'=> [
              'class' => 'yii\i18n\DbMessageSource',
              'sourceMessageTable'=>'{{%i18n_source_message}}',
              'messageTable'=>'{{%i18n_message}}',
              'enableCaching' => YII_ENV_DEV,
              'cachingDuration' => 3600,
              'on missingTranslation' => ['\backend\modules\i18n\Module', 'missingTranslation']
              ],
             */
            ],
        ],
        'fileStorage' => [
            'class' => '\trntv\filekit\Storage',
            'baseUrl' => '@storageUrl/source',
            'filesystem' => [
                'class' => 'common\components\filesystem\LocalFlysystemBuilder',
                'path' => '@storage/../../home/lottery/storage/source'
            ],
            'as log' => [
                'class' => 'common\behaviors\FileStorageLogBehavior',
                'component' => 'fileStorage'
            ]
        ],
        'keyStorage' => [
            'class' => 'common\components\keyStorage\KeyStorage'
        ],
        'urlManagerBackend' => \yii\helpers\ArrayHelper::merge(
                [
            'hostInfo' => Yii::getAlias('@backendUrl')
                ], require(Yii::getAlias('@backend/config/_urlManager.php'))
        ),
        'urlManagerFrontend' => \yii\helpers\ArrayHelper::merge(
                [
            'hostInfo' => Yii::getAlias('@frontendUrl')
                ], require(Yii::getAlias('@frontend/config/_urlManager.php'))
        ),
//        'urlManagerDesign' => \yii\helpers\ArrayHelper::merge(
//                [
//            'hostInfo' => Yii::getAlias('@designUrl')
//                ], require(Yii::getAlias('@design/config/_urlManager.php'))
//        ),
//        'urlManagerMix' => \yii\helpers\ArrayHelper::merge(
//                [
//            'hostInfo' => Yii::getAlias('@mixUrl')
//                ], require(Yii::getAlias('@mix/config/_urlManager.php'))
//        ),
        'urlManagerStorage' => \yii\helpers\ArrayHelper::merge(
                [
            'hostInfo' => Yii::getAlias('@storageUrl')
                ], require(Yii::getAlias('@storage/config/_urlManager.php'))
        ),
        'random' => [
            'class' => 'common\components\random\RandomGen',
        ],
        'pm' => [
            'class' => '\yiidreamteam\perfectmoney\Api',
            'accountId' => '8261560',
            'accountPassword' => 'freed0m',
            'walletNumber' => 'U12672057',
            'merchantName' => 'Freedom Lotto',
            'alternateSecret' => 'FReecT08pOEZLoTtOdFmSiAwxyu',
//            'resultUrl'       => ['/perfect-money/result'],
            'resultUrl' => ['/payment/pm-status'],
            'successUrl' => ['/payment/pm-success'],
            'failureUrl' => ['/payment/pm-failure'],
        ],
        'payeer' => [
//            'class'          => '\yiidreamteam\payeer\Api',
            'class' => '\common\components\payment\payeer\Api',
            'accountNumber' => 'P44668382',
            'apiId' => '229934941',
            'apiSecret' => '9wCtDaDqfE19awPV',
            'merchantId' => '230590528',
            'merchantSecret' => 'pIuPKyXYT82rNgxx'
        ],
        'fchange' => [
//            'class'          => '\yiidreamteam\payeer\Api',
            'class' => '\common\components\payment\fchange\Api',
            'merchant_name' => 'yandexmoney2',
            'merchantSecret' => 'QwP9cAyE',
            'recive_paysys_identificator' => 'PMUSD',
        ],
    ],
    'params' => [
        'adminEmail' => env('ADMIN_EMAIL'),
        'robotEmail' => env('ROBOT_EMAIL'),
        'contactEmail' => env('CONTACT_EMAIL'),
        'availableLocales' => [
            'ru-RU' => 'Русский (РФ)',
            'en-US' => 'English (US)',
        ],
        'availableLotteries' => [
            '6x45' => '6 из 45',
            '5x36' => '5 из 36',
        ],
        'icon-framework' => 'fi',
        'referralStringLength' => 5,
        'pm.backup.wallet' => 'U12672057',
        'pr.backup.wallet' => 'P44668382',
    //Число неразыгранных лотерей, которые должны быть в базе данных
    //Н-р, мультиставка
//        'featureLotteriesCount' => 10,
    //ReCaptcha from Google
//        'reCaptchaSecretKey' => '6Le-_SMTAAAAAKrr9OkHCzEh6WkbSciABG2tM6Jc',
//        'reCaptchaSiteKey' => '6Le-_SMTAAAAAIF-LBxSk_ni_wVavEgdG1p1BCd3',
    ],
];

if (YII_ENV_PROD) {
    $config['components']['log']['targets']['email'] = [
        'class' => 'yii\log\EmailTarget',
        'except' => ['yii\web\HttpException:*'],
        'levels' => ['error', 'warning'],
        'message' => ['from' => env('ROBOT_EMAIL'), 'to' => env('ADMIN_EMAIL')]
    ];
}

if (YII_ENV_DEV) {
    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module'
    ];

    $config['components']['cache']               = [
        'class' => 'yii\caching\DummyCache'
    ];
//    $config['components']['mailer']['transport'] = [
//        'class' => 'Swift_SmtpTransport',
//        'host' => env('SMTP_HOST'),
//        'port' => env('SMTP_PORT'),
//    ];
}

\Yii::$container->set('himiklab\yii2\recaptcha\ReCaptchaValidator', [
    'secret' => '6Le-_SMTAAAAAKrr9OkHCzEh6WkbSciABG2tM6Jc',
]);
\Yii::$container->set('himiklab\yii2\recaptcha\ReCaptcha', [
    'siteKey' => '6Le-_SMTAAAAAIF-LBxSk_ni_wVavEgdG1p1BCd3',
]);
\Yii::$container->set('borales\extensions\phoneInput\PhoneInput', [
    'jsOptions' => [
        'preferredCountries' => ['ru', 'ua', 'kz'],
        'onlyCountries' => ['ru', 'ua', 'kz', 'us', 'gb', 'de', 'fr'],
//            'autoHideDialCode'=>false,
        'initialCountry' => 'ru',
        'nationalMode' => false,
    ]
]);

return $config;
