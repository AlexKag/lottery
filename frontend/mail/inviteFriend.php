<?php

/**
 * @var $this \yii\web\View
 * @var $inviteUrl string
 * @var $inviteFriendText string
 */
echo $inviteFriendText . "\r\n";
echo '<br>';
echo sprintf('Приглашаем на игру в честную лотерею %s на нашем сайте %s', Yii::$app->name, Yii::$app->formatter->asUrl($inviteUrl));
