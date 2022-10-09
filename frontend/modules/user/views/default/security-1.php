<?php
use yii\bootstrap\Html;
use yii\helpers\Url;
use frontend\assets\BootboxAsset;
//use frontend\models\InviteFriendForm;

BootboxAsset::overrideSystemConfirm();

//For Popover
\yii\web\JqueryAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);
?>

<h2>Безопасность</h2>

<input id='2fa-ga' type="checkbox" checked><label for="2fa-ga">Включить двухэтапную аутентификацию Google Authenticator</label>

<p>
    Даже самый сложный пароль не&nbsp;гарантирует полной защиты: злоумышленники могут его украсть, а&nbsp;вы&nbsp;сами&nbsp;&mdash; просто забыть. Чтобы повысить безопасность аккаунта, включите двухэтапную аутентификацию.
    Воспользуйтесь <a href='https://support.google.com/accounts/answer/1066447' target='_blank'>инструкцией</a> для установки приложения Google Authenticator.
</p>

<?php
//echo Html::a('Пригласить друга', '#', ['class' => 'btn btn--default btn-lg col-md-offset-3', 'role' => 'button', 'id' => 'invite_friend']);

//$modelForm = new InviteFriendForm(['name' => 'publicId']);
//$inviteHtml = $this->render('@frontend/views/site/_invite_friend_form', [
//'model' => $modelForm,
// 'isGuest' => false,
// 'action' => Url::to('/site/invite-friend')
//]);
//$inviteHtml = str_replace(["\r", "\n"], ' ', $inviteHtml);
//Url::remember('', 'referrals');
$inviteHtml = 'Trst text';

$js = <<<JS
$('#2fa-ga').on('click', function(e){
    var dialog = bootbox.dialog({
        title: '<h1 class="text-center">Пригласить друга</h1>',
        message: '{$inviteHtml}',
        closeButton: true
    });
});
JS;

$this->registerJs($js);
?>

<form class = "form--middle">
<label for = "phone">Ваш моб. номер</label><input id = "phone" type = "text" value = "" name = "" placeholder = "Пример +7 495 555 66 77">
<button type = "submit">Сохранить</button>
<div class = "form-file__line"></div>
</form>

<input id = "secret-choice" type = "checkbox" checked name = "" value = ""><label for = "secret-choice">Задать секретный вопрос</label>

<p>
Секретный вопрос понадобится для&nbsp;
восстановления учетной записи и&nbsp;
других данных для&nbsp;
доступа.
</p>

<form class = "form--middle">
<label for = "secret">Секретный вопрос</label>
<select id = "secret" name = "">
<option value = "Россия">Россия</option>
<option value = "ОАЭ">ОАЭ</option>
</select>

<label for = "answer">Ответ</label><input id = "answer" type = "text" value = "" name = "" placeholder = "">
<label for = "password">Текущий пароль</label><input id = "password" type = "text" value = "" name = "" placeholder = "">

<button type = "submit">Сохранить</button>
<div class = "form-file__line"></div>
</form>

<input id = "ip-choice" type = "checkbox" checked name = "" value = ""><label for = "ip-choice">Ограничить доступ к аккаунту по IP-адресу</label>

<form class = "form--middle">
<label for = "ip">Задать IP-адрес</label><input id = "ip" type = "text" value = "" name = "" placeholder = "33.188.205.190">
<button type = "submit">Сохранить</button>
</form>
