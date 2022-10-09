<?php

use yii\helpers\Url;

$url = Url::to(['lottery6x45/view', 'id' => $model->id]);
$isSuperprizeWin = false;
$label = $isSuperprizeWin ? 'gift':'list-alt';
?>
<div class="col-md-4 col-sm-8">
    <div class="dashboard-block">
        <div class="rotate">
            <i class="glyphicon glyphicon-<?= $label ?>"></i>
        </div>
        <div class="list-group details">
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= $model->draw_d ?></span>
                Дата</a>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= $model->id ?></span>
                Тираж</a>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= $model->drawReadable ?></span>
                Выигрышная комбинация</a>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= $model->superprizeReadable ?></span>
                Суперприз</a>
        </div>
        <a href="<?= $url ?>"><i class="glyphicon glyphicon-stats more"></i></a>
    </div>
</div>