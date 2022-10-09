<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;

$tmp = Yii::$app->formatter->asCurrency(0);
$currencySymbol = substr($tmp, -1);

$account = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->userProfile->account;
?>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-8">

        <?php
        $content = '<ul class="list-inline" id="lottery1">';

        for ($i = 1; $i <= $count; $i++) {
            $content .= "<li class='btn btn-default btn-sq vertical-center'>$i</li>";
        }
        $content .= '</ul><hr>';
        echo $content;
        if ($betMax >= 1) {
            $content = '<ul class="list-inline" id="bet1">';
            $base = [1, 10, 25, 50, 100, 150, 250, 500, 1000, 2500, 5000, 10000];
            foreach ($base as $val) {
                if ($val > $betMax) {
                    break;
                }
                $content .= "<li class='btn btn-default btn-sq-sm vertical-center'>$val&nbsp$currencySymbol</li>";
            }
            $content .= '</ul>';
            echo '<p class="text-info">Быстрая ставка</p>' . $content;
        } else {
            echo '<p class="alert alert-warning">Недостаточно средств! Пожалуйста, пополните счёт!</p>';
        }
        ?>

    </div>
    <div class="col-xs-6 col-md-4">
        <h1 class="text-primary"><?= $gameName ?></h1>
        <p class="lead">Мгновенная лотерея</p>
        <p class="lead">Ваш баланс: <span class="label label-default" id="balance"><?php echo Yii::$app->formatter->asCurrency($account) ?></span></p>

        <div class="row">
            <div class="col-md-10">
                <?php $form = ActiveForm::begin(); ?>

                <?php echo $form->field($model, 'bet')->hiddenInput()->label(false); ?>

                <?php
                echo $form->field($model, 'paid', [
                    'template' => "{label}<br>\n<div class='input-group col-lg-10'><div class='input-group-addon'>$currencySymbol</div>{input}</div>\n{hint}\n{error}",
                ])->label('Ваша ставка');
                ?>

                <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . ' Сделать ставку', ['class' => 'btn btn-primary col-lg-offset-0 btn-block', 'id' => 'btn-submit']) ?>
                <?=
                Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . ' Случайная ставка', null, [
                    'class' => 'btn btn-primary col-lg-offset-0 btn-block',
                    'id' => 'btn-random',
                ]);
                ?>
                <?= Html::submitButton(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-right', 'aria-hidden' => 'true']) . ' Очистить поле', ['class' => 'btn btn-primary col-lg-offset-0 btn-block', 'id' => 'btn-clear']) ?>

                <?php ActiveForm::end(); ?>
            </div></div>
        <hr>
        <h3>Всего: <span class="label label-default" id="total">0&nbsp;<?= $currencySymbol ?></span></h3>

    </div>
</div>
<?php
$paid = $bet = '';
$validationMessage = $minNumbers == 1 ? 'Выберите число и сделайте ставку.' : "Выберите $minNumbers числа и сделайте ставку.";
if (is_array($betDefault)) {
    $bet = implode(',', $betDefault);
    $paid = $betMax >= 1 ? 1 : '';
}
$js = <<<JS
    Array.prototype.getUnique = function () {
        var u = {}, a = [];
        for (var i = 0, l = this.length; i < l; ++i) {
            if (u.hasOwnProperty(this[i])) {
                continue;
            }
            a.push(this[i]);
            u[this[i]] = 1;
        }
        return a;
    }

   /**
    * @description determine if an array contains one or more items from another array.
    * @param {array} haystack the array to search.
    * @param {array} arr the array providing items to check for in the haystack.
    * @return {boolean} true|false if haystack contains at least one item from arr.
    */
    Array.prototype.hasItems = function (haystack, arr) {
        return arr.some(function (v) {
            return haystack.indexOf(v) >= 0;
        });
    };
        
    function getRandomInt(min, max)
    {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
        
    function getRandomArray(min, max, count){
        var arr = [];
        var rundomNumber;

        while (arr.length < count) {
            rundomNumber = getRandomInt(min, max)
            if (arr.indexOf(rundomNumber) == -1) {
                arr.push(rundomNumber);
            }
        }
        return arr;
    }

    function toggleNumber(item) {
        $(item).toggleClass('$defaultClass');
        $(item).toggleClass('$selectedClass');
    }

    function setBet(numbers){
        fields = $("#lottery1 li");
        itemsCount = fields.length;
        for (i = 0; i < itemsCount; i++) {
            itm = $(fields[i]);
            if(numbers.indexOf(parseInt(itm.text())) >= 0){
                toggleNumber(itm);
            }
        }
    }

    function setPaid(numbers){
        fields = $("#bet1 li");
        itemsCount = fields.length;
        for (i = 0; i < itemsCount; i++) {
            itm = $(fields[i]);
            if(numbers.indexOf(parseInt(itm.text())) >= 0){
                toggleNumber(itm);
            }
        }
    }

    function getBet(numbers) {
        var _bet = [];
//            numbers = $("#lottery1 li");
        itemsCount = numbers.length;
        for (i = 0; i < itemsCount; i++) {
            it = $(numbers[i]);
            if (it.hasClass('$selectedClass')) {
                _bet.push(parseInt(it.text()));
            }
        }
        return _bet;
    }

    function _clear(numbers) {
        itemsCount = numbers.length;
        for (i = 0; i < itemsCount; i++) {
            item = $(numbers[i]);
            item.removeClass('$selectedClass');
            item.addClass('$defaultClass');
        }
    }
    function clearBet(numbers) {
        _clear(numbers);
        bet = [];
    }
    function clearPaid(numbers) {
        _clear(numbers);
        paid = [];
    }

    function calcPrice() {
        price = Number($('#betform-paid').val());
        price = isNaN(price) ? 0 : price;
        $("#total").text(price + ' ' + currencySymbol);
//        return price;
    }

    var bet = [], paid = [];
    $(function () {
        $("#btn-clear").click(function () {
            clearBet($("#lottery1 li"));
            clearPaid($("#bet1 li"));
            $("#total").text(0);
            $('#betform-paid').val(0);
            return false;
        });
        $("#betform-paid").change(function(){
            clearPaid($("#bet1 li"));
            pd = [parseInt($('#betform-paid').val())];
            pd = isNaN(pd)?0:pd;
            $("#betform-paid").val(pd);
            setPaid(pd);
            calcPrice();
            paid = [parseFloat($('#betform-paid').val())];
        });
        $("#btn-random").click(function () {
            clearPaid($("#bet1 li"));
            clearBet($("#lottery1 li"));
            betOld = bet;
            while (bet === betOld) {
                bet = getRandomArray(1, $betNumberMax, $minNumbers);
            }
            setBet(bet);
            $("#betform-bet").val(JSON.stringify(bet));
            paid = [1];
            setPaid(paid);
            $("#betform-paid").val(paid[0]);
            return false;
        });
        $("#btn-submit").click(function () {
//            paid = getBet($("#bet1 li"));
//            bet = getBet($("#lottery1 li"));
//            betSum = Number($('#betform-paid').val());
            if(!paid.length){
                $('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ["Сделайте ставку."]);
            }
            if(bet.length < $minNumbers){
                $('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ["$validationMessage"]);
            }
            return paid.length > 0 && bet.length <= $maxNumbers && bet.length >= $minNumbers
        });

        $("#lottery1 li").click(function (event) {
            if(1 == $maxNumbers){
                clearBet($("#lottery1 li"));
            }
            toggleNumber(this);
            bet = getBet($("#lottery1 li"));
            if (bet.length > $maxNumbers) {
                toggleNumber(this);
                bet = getBet($("#lottery1 li"));
            } else {
                calcPrice();
            }
            $("#betform-bet").val(JSON.stringify(bet));
//            $("#betform-paid").val(pricing[bet.length]);
            return false;
        });

        $("#bet1 li").click(function (event) {
            clearPaid($("#bet1 li"));
            toggleNumber(this);
            paid = getBet($("#bet1 li"));
            $("#betform-paid").val(paid);
            calcPrice();
            return false;
        });

        var betDefault = [$bet];
        var paidDefault = [$paid];
        var paidLimit = $account;
        if(betDefault.length > 0){
            bet = betDefault;
            setBet(betDefault);
            $("#betform-bet").val(JSON.stringify(bet));
        }
        if(paidDefault.length > 0){
            paid = paidDefault;
            setPaid(paidDefault);
            $("#betform-paid").val(paidDefault[0]);
        }
        if(paidDefault.length > 0 || betDefault.length > 0){
            calcPrice();
        }

    });
var currencySymbol = '$currencySymbol';
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>
<!--http://www.cyberforum.ru/javascript-jquery/thread542345.html-->