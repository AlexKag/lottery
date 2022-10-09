<?php

use yii\widgets\ActiveForm;
use kartik\touchspin\TouchSpin;
use yii\helpers\Html;
use yii\helpers\Url;
//use yii\helpers\Json;
//use kartik\growl\GrowlAsset;
use frontend\assets\SlotMachineAsset;
//use kartik\growl\Growl; //http://stackoverflow.com/questions/38915060/showing-kartik-growl-via-ajax-in-yii2

//GrowlAsset::register($this)->addTheme(Growl::TYPE_PASTEL);
SlotMachineAsset::register($this);

$tmp            = Yii::$app->formatter->asCurrency(0);
$currencySymbol = substr($tmp, -1);

$account = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->userProfile->account;
//Yii::$app->session->addFlash('warning', "<p class='text-primary'>Вы не угадали ни одного числа! <br>Выиграли числа [1234]! <hr>Вам обязательно повезет в другой раз! Удачи!</p>");
?>
<div class="row">
    
    <div class="col-xs-6 col-md-4">
        <div class="sidebar-game__header">&laquo;<?= $gameName ?>&raquo;<br>Слоты</div>
        <p class="lead">Ваш баланс: <span class="label label-default account"><?php echo Yii::$app->formatter->asCurrency($account) ?></span></p>
        <p class="lead">Ваш выигрыш: <span class="label label-default win">0</span></p>
        <p />
        <?php
        $form    = ActiveForm::begin(['options' => ['id' => 'bet_form', 'class'=>'slots_bet_form']]);
        echo '<label class="control-label">Ставка на линию</label>';
        echo TouchSpin::widget([
            'model'         => $model,
            'name'          => 'betPerLine',
            'readonly'      => true,
            'options'       => [
                'placeholder' => 'Ставка на линию',
                'id'          => 'betPerLine'
            ],
            'pluginOptions' => [
                'buttonup_class'   => 'btn btn-primary',
                'buttondown_class' => 'btn btn-info',
//                'buttonup_txt'     => '<i class="glyphicon glyphicon-plus-sign"></i>',
//                'buttondown_txt'   => '<i class="glyphicon glyphicon-minus-sign"></i>',
                'initval'          => 1,
                'min'              => 1,
                'max'              => 10,
//                'step'             => 0.1,
//                'decimals'         => 2,
                'boostat'          => 5,
                'maxboostedstep'   => 10,
            ]
        ]);
        echo '<label class="control-label">Линии</label>';
        echo TouchSpin::widget([
            'model'         => $model,
            'name'          => 'linesCount',
            'readonly'      => true,
            'options'       => [
                'placeholder' => 'Линии',
                'id'          => 'linesCount'
            ],
            'pluginOptions' => [
                'buttonup_class'   => 'btn btn-primary',
                'buttondown_class' => 'btn btn-info',
//                'buttonup_txt'     => '<i class="glyphicon glyphicon-plus-sign"></i>',
//                'buttondown_txt'   => '<i class="glyphicon glyphicon-minus-sign"></i>',
                'initval'          => 1,
                'min'              => 1,
                'max'              => 5,
//                'step'             => 0.1,
//                'decimals'         => 2,
//                'boostat'          => 5,
//                'maxboostedstep'   => 10,
            ]
        ]);
        echo '<label class="control-label">Цена кредита</label>';
        echo TouchSpin::widget([
            'model'         => $model,
            'name'          => 'denomination',
            'readonly'      => true,
            'options'       => [
                'placeholder' => 'Цена кредита',
                'id'          => 'denomination'
            ],
            'pluginOptions' => [
                'buttonup_class'   => 'btn btn-primary',
                'buttondown_class' => 'btn btn-info',
//                'buttonup_txt'     => '<i class="glyphicon glyphicon-plus-sign"></i>',
//                'buttondown_txt'   => '<i class="glyphicon glyphicon-minus-sign"></i>',
                'initval'          => 1,
                'min'              => 0.1,
                'max'              => 5,
                'step'             => 0.5,
                'decimals'         => 1,
//                'boostat'          => 0.1,
//                'maxboostedstep'   => 0.1,
            ]
        ]);
        ActiveForm::end();
        echo '<p /><p />';
        echo Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . ' Максимальная ставка', ['class' => 'btn btn-primary col-lg-offset-0 btn-block', 'id' => 'btn-betmax']);
        echo '<p />';
        echo Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . ' Сделать ставку', ['class' => 'btn btn-primary col-lg-offset-0 btn-block', 'id' => 'btn-submit']);
        ?>
    </div>
    <div class="col-xs-11 col-md-7 col-sm-5 col-sm-1 col-md-offset-1 col-xs-offset-1">
        <div class="fancy center-block">
            <?php
            foreach ($fields as $key => $field) {
                    echo Html::ul($field,[
                        'class' => 'slot ' . $key,
                        'item' => function($item, $index){
                            return Html::tag('li', Html::tag('span', $item['html']));
                        },
                    ]);
//                foreach ($field as item){
//                    Html::ul($items);
//                }
            }
            ?>
<!--            <ul class="slot slot-prev">
                <li><span class="glyphicon glyphicon-leaf">9</span></li>
                <li><span>0</span></li>
                <li><span>1</span></li>
                <li><span>2</span></li>
                <li><span>3</span></li>
                <li><span>4</span></li>
                <li><span>5</span></li>
                <li><span>6</span></li>
                <li><span>7</span></li>
                <li><span>8</span></li>
                <li><span>9</span></li>
            </ul><br>
            <ul class="slot slot-main">
                <li><span>1</span></li>
                <li><span>2</span></li>
                <li><span>3</span></li>
                <li><span>4</span></li>
                <li><span>5</span></li>
                <li><span>6</span></li>
                <li><span>7</span></li>
                <li><span>8</span></li>
                <li><span>9</span></li>
                <li><span>0</span></li>
            </ul>
            <ul class="slot slot-next">
                <li><span>2</span></li>
                <li><span>3</span></li>
                <li><span>4</span></li>
                <li><span>5</span></li>
                <li><span>6</span></li>
                <li><span>7</span></li>
                <li><span>8</span></li>
                <li><span>9</span></li>
                <li><span>0</span></li>
                <li><span>1</span></li>
            </ul>-->
        </div>

    </div>
</div>
<?php
//$template = <<<TMPL
//<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{1}" role="alert">
//<button type="button" class="close" data-notify="dismiss"><span aria-hidden="true">×</span></button>
//<span data-notify="title">{1}</span>
//<span class=".kv-alert-separator"></span>
//<span data-notify="message">{2}</span>
//<div class="progress kv-progress-bar" data-notify="progressbar"><div class="progress-bar progress-bar-{1}" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;"></div></div>
//</div>
//TMPL;
//$template = '';
//$template = str_replace(["\r", "\n"], ' ', $template);
$drawUrl = Url::to('slots-five/draw');
$js      = <<<JS
var currencySymbol = '$currencySymbol';
var notify = {};
notify.config = {
        'win': {"delay":3000,"mouse_over":"pause","type":"info", "newest_on_top": true},
        'loose': {"delay":3000,"mouse_over":"pause","type":"warning", "newest_on_top": true}
};

$('#btn-betmax').on('click', function(){
        $('#betPerLine').val(10);
        $('#linesCount').val(5);
        $('#denomination').val(2);
        $('#btn-submit').click();
    }
);
var slts = [], stat;
$('.fancy .slot').jSlots({
    number: 5,
    winnerNumber : [1,2,3,4,5,6,7,8,9,10],
    spinner: '#btn-submit',
    easing: 'easeOutSine',
    time : 3500,
    loops: 5,
    infinite: true,
    minimumSpeed: 1000,
//        endsAt : [7, 6, 5, 4, 3],
    onStart: function (jslot) {
        $('.slot').removeClass('winner');
        slts.push(jslot);
        if (jslot.\$el.hasClass('slot-main')) {
            var req = {};
            req.betPerLine = $('#betPerLine').val();
            req.linesCount = $('#linesCount').val();
            req.denomination = $('#denomination').val();
            $.post(
                '$drawUrl',
                req,
                function (data) {
                $.each(slts, function () {
                this.stop(data.draw)
            });
                stat = data;
                notify.growl = data.growl;
                notify.flag = true;
                }
            );
        }
    },
    onEnd: function(finalNumbers) {
        $('.win').text(Number(stat.win).formatMoney(2, ',', ' ') + ' ' + currencySymbol);
        $('.account').text(Number(stat.account).formatMoney(2, ',', ' ') + ' ' + currencySymbol);
        if (notify.flag) {
            notify.flag = false;
            if(stat.win){
                $.notify( notify.growl, notify.config.win );
            } else {
                $.notify( notify.growl, notify.config.loose );
            }
        }
    },
});

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };
JS;

$this->registerJs($js, \yii\web\View::POS_READY);
?>
<!--http://www.cyberforum.ru/javascript-jquery/thread542345.html-->

<style type="text/css">

    .fancy ul {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .jSlots-wrapper {
        overflow: hidden;
        height: 20px;
        display: inline-block; /* to size correctly, can use float too, or width*/
        border: 1px solid #999;
    }

    .slot {
        float: left;
    }



    /* ---------------------------------------------------------------------
       FANCY EXAMPLE
    --------------------------------------------------------------------- */
    .fancy .jSlots-wrapper {
        overflow: hidden;
        height: 100px;
        display: inline-block; /* to size correctly, can use float too, or width*/
        border: 1px solid #999;
    }

    .fancy .slot li {
        width: 100px;
        line-height: 100px;
        text-align: center;
        font-size: 70px;
        /*padding: 5px;*/
        font-weight: bold;
        color: #fff;
        text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.8);
        font-family: 'Gravitas One', serif;
        border-left: 1px solid #999;
    }

    .fancy .slot:first-child li {
        border-left: none;
    }

    .fancy .slot-prev li:nth-child(11),
    .fancy .slot-prev li:nth-child(1),
    .fancy .slot-main li:nth-child(10),
    .fancy .slot-next li:nth-child(9)
    {
        background-color: #2F4909;
    }
    .fancy .slot-prev li:nth-child(10),
    .fancy .slot-main li:nth-child(9),
    .fancy .slot-next li:nth-child(8)
    {
        background-color: #3B5B0B;
    }
    .fancy .slot-prev li:nth-child(9),
    .fancy .slot-main li:nth-child(8),
    .fancy .slot-next li:nth-child(7)
    {
        background-color: #476D0D;
    }
    .fancy .slot-prev li:nth-child(8),
    .fancy .slot-main li:nth-child(7),
    .fancy .slot-next li:nth-child(6)
    {
        background-color: #537F10;
    }
    .fancy .slot-prev li:nth-child(7),
    .fancy .slot-main li:nth-child(6),
    .fancy .slot-next li:nth-child(5)
    {
        background-color: #5E9112;
    }
    .fancy .slot-prev li:nth-child(6),
    .fancy .slot-main li:nth-child(5),
    .fancy .slot-next li:nth-child(4)
    {
        background-color: #6AA314;
    }
    .fancy .slot-prev li:nth-child(5),
    .fancy .slot-main li:nth-child(4),
    .fancy .slot-next li:nth-child(3)
    {
        background-color: #76B616;
    }
    .fancy .slot-prev li:nth-child(4),
    .fancy .slot-main li:nth-child(3),
    .fancy .slot-next li:nth-child(2)
    {
        background-color: #7BBD17;
    }
    .fancy .slot-prev li:nth-child(3),
    .fancy .slot-main li:nth-child(2),
    .fancy .slot-next li:nth-child(1),
    .fancy .slot-next li:nth-child(11)
    {
        background-color: #8DDA1B;
    }
    .fancy .slot-prev li:nth-child(2),
    .fancy .slot-main li:nth-child(1),
    .fancy .slot-next li:nth-child(10),
    .fancy .slot-main li:nth-child(11)
    {
        background-color: #A0E637;
    }

    .fancy .slot li span {
        display: block;
    }

    /* ---------------------------------------------------------------------
       ANIMATIONS
    --------------------------------------------------------------------- */

    @-webkit-keyframes winner {
        0%, 50%, 100% {
            -webkit-transform: rotate(0deg);
            font-size:70px;
            color: #fff;
        }
        25% {
            -webkit-transform: rotate(20deg);
            font-size:90px;
            color: #FF16D8;
        }
        75% {
            -webkit-transform: rotate(-20deg);
            font-size:90px;
            color: #FF16D8;
        }
    }
    @-moz-keyframes winner {
        0%, 50%, 100% {
            -moz-transform: rotate(0deg);
            font-size:70px;
            color: #fff;
        }
        25% {
            -moz-transform: rotate(20deg);
            font-size:90px;
            color: #FF16D8;
        }
        75% {
            -moz-transform: rotate(-20deg);
            font-size:90px;
            color: #FF16D8;
        }
    }
    @-ms-keyframes winner {
        0%, 50%, 100% {
            -ms-transform: rotate(0deg);
            font-size:70px;
            color: #fff;
        }
        25% {
            -ms-transform: rotate(20deg);
            font-size:90px;
            color: #FF16D8;
        }
        75% {
            -ms-transform: rotate(-20deg);
            font-size:90px;
            color: #FF16D8;
        }
    }


    @-webkit-keyframes winnerBox {
        0%, 50%, 100% {
            box-shadow: inset 0 0  0px yellow;
            background-color: #FF0000;
        }
        25%, 75% {
            box-shadow: inset 0 0 30px yellow;
            background-color: aqua;
        }
    }
    @-moz-keyframes winnerBox {
        0%, 50%, 100% {
            box-shadow: inset 0 0  0px yellow;
            background-color: #FF0000;
        }
        25%, 75% {
            box-shadow: inset 0 0 30px yellow;
            background-color: aqua;
        }
    }
    @-ms-keyframes winnerBox {
        0%, 50%, 100% {
            box-shadow: inset 0 0  0px yellow;
            background-color: #FF0000;
        }
        25%, 75% {
            box-shadow: inset 0 0 30px yellow;
            background-color: aqua;
        }
    }



    .winner li {
        -webkit-animation: winnerBox 2s infinite linear;
        -moz-animation: winnerBox 2s infinite linear;
        -ms-animation: winnerBox 2s infinite linear;
    }
    .winner li span {
        -webkit-animation: winner 2s infinite linear;
        -moz-animation: winner 2s infinite linear;
        -ms-animation: winner 2s infinite linear;
    }
</style>
