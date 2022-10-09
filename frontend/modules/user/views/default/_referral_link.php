<?php

//use yii\helpers\Url;

//For Popover
\yii\web\JqueryAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);

//$urlParams = Yii::$app->user->isGuest ? ['/user/sign-in/signup'] : ['/user/sign-in/signup', 'ref' => Yii::$app->user->identity->referral_id];
?>

<div class="refferal-banner">
    <span class="refferal-banner__name pull-left">Реферальная ссылка&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
    <div class="col-md-offset-2">
        <span id="referral" class="badge" data-content="Ссылка скопирована в буфер обмена" data-title="Реферальная сссылка" data-toggle="popover"><?= getMyRefUrl() ?></span><br>
        <a id="btn1" class="refferal-banner__button" data-content="Ссылка скопирована в буфер обмена" data-title="Реферальная сссылка" data-toggle="popover">Копировать</a>
    </div>
</div>

<?php
$js = <<<JS

/* To initialize BS3 popovers set this below */
$(function () {
    $('body').popover({selector: '[data-toggle="popover"]'});
});

$(document).on('click', function (e) {
    $('[data-toggle="popover"],[data-original-title]').each(function () {
        //the 'is' for buttons that trigger popups
        //the 'has' for icons within a button that triggers a popup
        if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
            (($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
        }

    });
});

var copyBanner1 = document.querySelector('#btn1');
copyBanner1.addEventListener('click', function(event){
    refUrl = document.querySelector('#referral');
    text = refUrl.textContent;
    copyToClipboard(text);
});
var copyBanner2 = document.querySelector('#referral');
copyBanner2.addEventListener('click', function(event){
    refUrl = document.querySelector('#referral');
    text = refUrl.textContent;
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

$this->registerJs($js);
