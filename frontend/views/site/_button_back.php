<?php

use yii\helpers\Html;

echo '<p />';
echo Html::tag('div', '', ['class' => 'clearfix']);
echo '<hr>';
echo Html::a(Html::tag('i', '', ['class' => 'fa fa-arrow-circle-o-left', 'aria-hidden' => 'true']) . ' Назад', Yii::$app->request->referrer, ['class' => 'btn btn--green', 'title' => 'Вернуться на предыдущую страницу']);
