<?php

use yii\bootstrap\Html;
//use yii\helpers\Url;
use frontend\assets\BootboxAsset;

//use frontend\models\InviteFriendForm;

BootboxAsset::overrideSystemConfirm();

//For Popover & Bootbox dialog
\yii\web\JqueryAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);

$ga_enabled       = empty(Yii::$app->user->identity->ga_token) ? '' : 'checked';
$ip_check_enabled = (bool) Yii::$app->user->identity->is_ip_check ? 'checked' : '';
?>

<h2>Безопасность</h2>
<p class="lead">
    Даже самый сложный пароль не&nbsp;гарантирует полной защиты: злоумышленники могут его украсть. Чтобы повысить безопасность аккаунта, включите двухэтапную аутентификацию.
    <br>При включении нескольких способов аутентификации одновременно действует только один &mdash; тот, который расположен выше в списке.
</p>
    <div class = "form-file__line"></div>
<br>
<form class="form--middle">
    <input id='2fa-ga' type="checkbox" <?= $ga_enabled ?>><label for="2fa-ga">Двухэтапная аутентификация Google Authenticator</label>
    <p>Воспользуйтесь одним из приложениий для двухэтапной аутентификации (<a href='https://support.google.com/accounts/answer/1066447' target='_blank'>Google Authenticator</a> или <a href='https://www.authy.com/' target='_blank'>Authy 2-Factor Authentication</a>) и генерируйте уникальные коды подтверждения при каждом входе на сайт FreedomLOTTO.</p>
    <p><a href='https://support.google.com/accounts/answer/1066447' target='_blank'>Google Authenticator</a> и <a href='https://www.authy.com/' target='_blank'>Authy 2-Factor Authentication</a> &mdash; кроссплатформенное программное обеспечение для двухэтапной аутентификации с помощью Time-based One-time Password Algorithm и HMAC-based One-time Password Algorithm от Google (RFC 6238 и RFC 4226).</p>
    <p>
    <div class = "form-file__line"></div>
</form>
<form class="form--middle">
    <input id='ip-check' type="checkbox" <?= $ip_check_enabled ?>><label for="ip-check">Двухэтапная аутентификация e-mail</label>
    <p>
        При входе в систему с неизвестного IP-адреса <?=Yii::$app->name ?> отправит вам сообщение на e-mail с кодом подтверждения.
    </p>
</form>
<?php
//echo Html::a('Пригласить друга', '#', ['class' => 'btn btn--default btn-lg col-md-offset-3', 'role' => 'button', 'id' => 'invite_friend']);

$enableGAHtml    = $this->render(empty($ga_enabled) ? '_2fa_ga_enable' : '_2fa_ga_disable', [
    'model' => $model,
        ]);
$enableGAHtml    = str_replace(["\r", "\n"], ' ', $enableGAHtml);
$enableEmailHtml = $this->render(empty($ip_check_enabled) ? '_2fa_mail_enable' : '_2fa_mail_disable', [
    'model' => $modelEmail,
        ]);
$enableEmailHtml = str_replace(["\r", "\n"], ' ', $enableEmailHtml);
//$disableHtml = $this->render('_2fa_ga_disable', [
//    'model' => $model,
//        ]);
//$disableHtml = str_replace(["\r", "\n"], ' ', $disableHtml);
/*
  $js = <<<JS
  $('#2fa-ga').on('click', function(e){
  if($('#2fa-ga').prop('checked')){
  var dialog_enable = bootbox.dialog({
  title: '<h1 class="text-center">Двухэтапная аутентификация<br>Google Authenticator</h1>',
  message: '{$enableHtml}',
  closeButton: true
  });
  }else{
  var dialog_disable = bootbox.dialog({
  title: '<h1 class="text-center">Двухэтапная аутентификация<br>Google Authenticator</h1>',
  message: '{$disableHtml}',
  closeButton: true
  });
  }
  });
  JS;
 */
$js              = <<<JS
$('#2fa-ga').on('click', function(e){
    var dialog_enable = bootbox.dialog({
        title: '<h1 class="text-center">Двухэтапная аутентификация<br>Google Authenticator</h1>',
        message: '{$enableGAHtml}',
        closeButton: true,
        onEscape: true,
        backdrop: true
    });
    return false;
});
$('#ip-check').on('click', function(e){
    $('#ip-check').load("/user/default/send-auth-code");
    var dialog_enable = bootbox.dialog({
        title: '<h1 class="text-center">Двухэтапная аутентификация e-mail</h1>',
        message: '{$enableEmailHtml}',
        closeButton: true,
        onEscape: true,
        backdrop: true
    });
    return false;
});
JS;

$this->registerJs($js);
