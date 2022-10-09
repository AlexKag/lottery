<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<div class="site-error">

    <h1><?php echo Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?php echo nl2br(Html::encode($message)) ?>
    </div>

    <p>
        При обработке вашего запроса возникла ошибка.
    </p>
    <p>
        Сообщите нам, если считаете, что это ошибка сервера. Спасибо.
    </p>

</div>