<?php

/**
 * @var $this \yii\web\View
 * @var $code integer
 * @var $enable boolean
 */
$message = $enable ? 'подключения' : 'отключения';
echo Yii::t('frontend', 'Ваш код для {message} двухэтапной аутентификации e-mail: {code}', ['code' => $code, 'message' => $message]);
