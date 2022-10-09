<?php

return [
    'id'         => 'frontend',
    'basePath'   => dirname(__DIR__),
    'components' => [
        'urlManager' => require(__DIR__ . '/_urlManager.php'),
        'cache'      => require(__DIR__ . '/_cache.php'),
    ],
    'params'     => [
        'growlNotificationShowTime' => 20000, //mss
    ]
];
