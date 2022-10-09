<?php

use yii\bootstrap\ActiveForm;
?>

<div class="finance__filter">
    Фильтр операций

    <?php
    $form = ActiveForm::begin([
//                'method' => 'get',
//        'options' => [
//            'id' => 'filter_form'
//        ],
                'fieldConfig' => [
                    'template' => '{input}',
                    'options' => [
                        'tag' => false,
                    ],
                ]
    ]);
    echo $form->field($model, 'category')->dropDownList([
        'withdraw' => 'Списано',
        'charge' => 'Начислено',
//        'noaction' => 'Отменено',
            ],
            ['prompt' => 'Все записи',
                'onchange'=>'$("form").submit();',
//                    'class' => 'pull-right .col-sm-4',
                'id' => 'сharging'
                ]
    );
    ActiveForm::end();
    ?>
</div>
<!--<select id="сharging" name="">
    <option value="Начисление выигрыша">Начисление выигрыша</option>
    <option value="Начисление баланса">Начисление баланса</option>
</select>-->