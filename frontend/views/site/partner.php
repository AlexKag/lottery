<?php

use common\models\Page;

//For Popover
\yii\web\JqueryAsset::register($this);
\yii\bootstrap\BootstrapPluginAsset::register($this);

$this->title                   = 'Стать партнером';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];

//партнерская программа
$article = Page::find()->where(['slug' => 'partner', 'status' => Page::STATUS_PUBLISHED])->one();

if ($article) {
    echo "<h2>$article->title</h2>";
    echo $article->body;
}

$js = <<<JS
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
JS;
$this->registerJs($js);
?>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 468x60 px</div>
    <img id="img1" src="/img/refferal-banner__486x60.jpg" alt="">
    <a id="btn1" class="refferal-banner__button" data-content="Ссылка скопирована в буфер обмена" data-title="Баннер 468x60 px" data-toggle="popover">Копировать</a>
</div>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 728x90 px</div>
    <img id="img2" class="refferal-banner__responsive" src="/img/refferal-banner__728x90.jpg" alt="">
    <a id="btn2" class="refferal-banner__button" data-content="Ссылка скопирована в буфер обмена" data-title="Баннер 728x90 px" data-toggle="popover">Копировать</a>
</div>

<div class="refferal-banner">
    <div class="refferal-banner__name">Баннер 300x250 px</div>
    <img id="img3" src="/img/refferal-banner__300x250.jpg" alt="">
    <a id="btn3" class="refferal-banner__button" data-content="Ссылка скопирована в буфер обмена" data-title="Баннер 300x250 px" data-toggle="popover">Копировать</a>
</div>
<!--http://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript-->
<?= $this->render('@frontend/views/site/_button_back'); ?>