<?php
/**
 * Require core files
 */
require_once(__DIR__ . '/../helpers.php');

/**
 * Setting path aliases
 */
Yii::setAlias('@base', realpath(__DIR__.'/../../'));
Yii::setAlias('@common', realpath(__DIR__.'/../../common'));
Yii::setAlias('@frontend', realpath(__DIR__.'/../../frontend'));
Yii::setAlias('@backend', realpath(__DIR__.'/../../backend'));
//Yii::setAlias('@design', realpath(__DIR__.'/../../design'));
//Yii::setAlias('@mix', realpath(__DIR__.'/../../mix'));
Yii::setAlias('@tlgrm', realpath(__DIR__.'/../../tlgrm'));
Yii::setAlias('@console', realpath(__DIR__.'/../../console'));
Yii::setAlias('@storage', realpath(__DIR__.'/../../storage'));
Yii::setAlias('@tests', realpath(__DIR__.'/../../tests'));

/**
 * Setting url aliases
 */
Yii::setAlias('@frontendUrl', env('FRONTEND_URL'));
Yii::setAlias('@backendUrl', env('BACKEND_URL'));
Yii::setAlias('@designUrl', env('DESIGN_URL'));
//Yii::setAlias('@mixUrl', env('MIX_URL'));
Yii::setAlias('@storageUrl', env('STORAGE_URL'));



