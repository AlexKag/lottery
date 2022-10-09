<?php
/**
 * @var $this \yii\web\View
 * @var $url \common\models\User
 */
?>
<?php echo Yii::t('frontend', 'Перейдите по ссылке для активации учетной записи: {url}', ['url' => Yii::$app->formatter->asUrl($url)]) ?>
