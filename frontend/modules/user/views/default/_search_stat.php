<?php

use yii\bootstrap\ActiveForm;
use frontend\modules\user\models\StatSearchForm;
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
        StatSearchForm::WIN => 'Выиграл',
        StatSearchForm::LOOSE => 'Проиграл',
        StatSearchForm::NOTDRAWED => 'Не сыграно',
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