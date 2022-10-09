<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;

$tmp = Yii::$app->formatter->asCurrency(0);
$currencySymbol = substr($tmp, -1);

$account = Yii::$app->user->isGuest ? 0 : Yii::$app->user->identity->userProfile->account;
$message1 = Yii::$app->user->isGuest ? 'Недостаточно средств. Войдите на сайт или зарегистрируйтесь.' : "Недостаточно средств. Сумма на счету: $account $currencySymbol. Пожалуйста, пополните счёт, чтобы снять ограничения.";
?>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-8">

        <?php
        $content = '<ul class="list-inline" id="lottery1">';

        for ($i = 1; $i <= $count; $i++) {
            $content .= "<li class='btn btn-default btn-sq vertical-center'>$i</li>";
        }
        $content .= '</ul>';
        echo $content;
        ?>

    </div>
    <div class="col-xs-6 col-md-4">
        <h1 class="text-primary"><?= $lottery->name ?> <span class="badge">Розыгрыш №<?= $lottery->id ?></span></h1>
        <p class="lead"><?php echo Yii::$app->formatter->asDatetime($lottery->draw_at) ?>&nbsp;МСК</p>
        <p class="lead">Ваш баланс: <span id="balance" class="label label-default"><?php echo Yii::$app->formatter->asCurrency($account) ?></span></p>
        <div class="row">
            <div class="col-md-10">
                <?php $form = ActiveForm::begin(); ?>

                <?php echo $form->field($model, 'bet')->hiddenInput()->label(false) ?>

                <?php echo $form->field($model, 'paid')->hiddenInput()->label(false) ?>

                <?php
                $items = range(1, Yii::$app->keyStorage->get('lottery.common.multidraw_count'));
                echo $form->field($model, 'multidraw_count', [
//            'template' => "{label}<br>\n<div class='input-group'><div class='col-sm-4'>{input}</div></div>\n{hint}\n{error}",
                ])->dropDownList($items, [
                    'class' => 'form-control'
                ]);
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
        <h3>Всего: <span class="label label-default" id="total">0</span></h3>

    </div>
</div>
<?php
$jPricing = Json::encode($pricing);
$bet = '';
if (is_array($betDefault)) {
    $bet = implode(',', $betDefault);
}
$js = <<<JS
//    $('#betform-multidraw_count').each(function(){
//        multidraw_count = new Select({
//            el: this
//        });
//
//    });
//console.log(multidraw_count.value);

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

    function getBet(numbers) {
        bet = [];
//            numbers = $("#lottery1 li");
        itemsCount = numbers.length;
        for (i = 0; i < itemsCount; i++) {
            it = $(numbers[i]);
            if (it.hasClass('$selectedClass')) {
                bet.push(it.text());
            }
        }
        return bet;
    }

    function clearBet(numbers) {
        itemsCount = numbers.length;
        for (i = 0; i < itemsCount; i++) {
            item = $(numbers[i]);
            item.removeClass('$selectedClass');
            item.addClass('$defaultClass');
        }
        $("#betform-multidraw_count").val(0);
        multidraw_count.change('0');
        bet = [];
        calcPrice();
    }

    function calcPrice() {
        price = Number(pricing[bet.length]) * (1 + Number($("#betform-multidraw_count").val()))
        price = isNaN(price) ? 0 : price + ' ' + currencySymbol;
        $("#total").text(price);
//        return price;
    }

    var pricing = $jPricing;
    var bet = [];
    var currencySymbol = '$currencySymbol';
    $(function () {
        $("#btn-clear").click(function () {
            clearBet($("#lottery1 li"));
            return false;
        });
        $("#betform-multidraw_count").change(function(){
            calcPrice();
        });
        $("#btn-random").click(function () {
//            clearPaid($("#bet1 li"));
            betOld = bet;
            clearBet($("#lottery1 li"));
            while (bet === betOld || bet.length < 1) {
                nums = getRandomInt($minNumbers,$maxNumbers);
                bet = getRandomArray(1, $betNumberMax, nums);
            }
            setBet(bet);
            $("#betform-bet").val(JSON.stringify(bet));
            calcPrice();
            $("#betform-paid").val(pricing[bet.length]);
            return false;
        });
        $("#btn-submit").click(function () {
//            paidLimit = parseFloat($("#balance").text());
            total = parseFloat($("#total").text());
            if(total > paidLimit){
                //$('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ["Недостаточно средств. Необходимо " + $("#total").text() + "."]);
                $('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ["$message1"]);
            }
            if(bet.length < $minNumbers){
                $('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ['Выберите не менее $minNumbers чисел или нажмите кнопку "Случайная ставка".']);
            }
            if(bet.length > $maxNumbers){
                $('#w0').yiiActiveForm('updateAttribute', 'betform-paid', ['Выберите не более $maxNumbers чисел или нажмите кнопку "Случайная ставка".']);
            }
            return bet.length <= $maxNumbers && bet.length >= $minNumbers && total <= paidLimit
        });
        $("#lottery1 li").click(function (event) {
            toggleNumber(this);
//            $(this).toggleClass('$defaultClass');
//            $(this).toggleClass('$selectedClass');
//            bet = [];
//            numbers = $("#lottery1 li");
//            itemsCount = numbers.length;
//            for (i = 0; i < itemsCount; i++) {
//                it = $(numbers[i]);
//                if (it.hasClass('$selectedClass')) {
//                    bet.push(it.text());
//                }
//            }

//            console.log($(this).text());
            bet = getBet($("#lottery1 li"));
            if (bet.length > $maxNumbers) {
                toggleNumber(this);
                bet = getBet($("#lottery1 li"));
            } else {
                calcPrice();
                //Price calculation
//                price = calcPrice();
//                $("#total").text(price);
            }
            $("#betform-bet").val(JSON.stringify(bet));
            $("#betform-paid").val(pricing[bet.length]);
//            console.log('Items selected:' + bet.length);
            return false;
        });

        var betDefault = [$bet];
        var paidLimit = $account;
        if(betDefault.length > 0){
            bet = betDefault;
            setBet(betDefault);
            calcPrice();
            $("#betform-bet").val(JSON.stringify(bet));
            $("#betform-paid").val(pricing[bet.length]);
        }
    });
//    setBet([1,7,9,11,25]);
    $('#betform-multidraw_count').each(function () {
        multidraw_count = new Select({
            el: this
        });
    });
JS;
$this->registerJs($js, \yii\web\View::POS_READY);
//if (is_array($betDefault)) {
//    $bet = implode(',', $betDefault);
////    $js = $js + "setBet([$bet]);";
//    $this->registerJs("setBet([$bet]); calcPrice();", \yii\web\View::POS_READY);
//}
?>
<!--http://www.cyberforum.ru/javascript-jquery/thread542345.html-->