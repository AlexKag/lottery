<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $token string */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user/sign-in/reset-password', 'token' => $token]);
?>

Здравствуйте, <?php echo Html::encode($user->username) ?>.

Перейдите по ссылке, чтобы восстановить пароль:

<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
