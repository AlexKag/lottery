<?php

use yii\helpers\Url;
use common\models\Page;

//партнерская программа
$article = Page::find()->where(['slug' => 'partner', 'status' => Page::STATUS_PUBLISHED])->one();
?>
<h2>Рекламные материалы</h2>

<form class="form--middle">
    <label for="referral">Реферальная ссылка</label><input id="referral" type="text" value="<?= Url::to(['/user/sign-in/signup', 'ref' => $model->referral_id], true) ?>" name="" placeholder="<?=Url::to(['/'], true)?>/ref-179">
</form>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 468x60 px</div>
    <img id="img1" src="/img/refferal-banner__486x60.jpg" alt="">
    <a id="btn1" title="">Копировать</a>
</div>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 728x90 px</div>
    <img id="img2" class="refferal-banner__responsive" src="/img/refferal-banner__728x90.jpg" alt="">
    <a id="btn2" title="">Копировать</a>
</div>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 300x250 px</div>
    <img id="img3" src="/img/refferal-banner__300x250.jpg" alt="">
    <a id="btn3" title="">Копировать</a>
</div>
<!--http://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript-->
<?php
if ($article) {
    echo "<h2>$article->title</h2>";
    echo \yii\helpers\StringHelper::truncate($article->body, 500, '...', null, true);
}
?>
<p> </p>
<a class="black-link" href="/page/partner" title="">Читать условия полностью »</a>

<?php
//$baseUrl = Url::to([''], true);
$js = <<<JS
var selectRefInput = document.querySelector('#referral');
selectRefInput.addEventListener('focus', function(event){
selectRefInput.select();
    try{
        document.execCommand('copy');
    }catch(err){}
});

var copyBanner1 = document.querySelector('#btn1');
copyBanner1.addEventListener('click', function(event){
    img1 = document.querySelector('#img1');
    text = img1['src'];
    copyToClipboard(text);
});

var copyBanner2 = document.querySelector('#btn2');
copyBanner2.addEventListener('click', function(event){
    img2 = document.querySelector('#img2');
    text = img2['src'];
    copyToClipboard(text);
});

var copyBanner3 = document.querySelector('#btn3');
copyBanner3.addEventListener('click', function(event){
    img3 = document.querySelector('#img3');
    text = img3['src'];
    copyToClipboard(text);
});

function copyToClipboard(text) {
    if (window.clipboardData && window.clipboardData.setData) {
        // IE specific code path to prevent textarea being shown while dialog is visible.
        return clipboardData.setData("Text", text);

    } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
        var textarea = document.createElement("textarea");
        textarea.textContent = text;
        textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
        document.body.appendChild(textarea);
        textarea.select();
        try {
            return document.execCommand("copy");  // Security exception may be thrown by some browsers.
        } catch (ex) {
            console.warn("Copy to clipboard failed.", ex);
            return false;
        } finally {
            document.body.removeChild(textarea);
        }
    }
}
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
