<?php

//Билет
use yii\helpers\Url;

$url = '#';
$isWin = (bool) $model->win_cnt;
$label = $isWin ? 'gift' : 'list-alt';
$draw = $model->lottery->draw;
if (empty($draw)) {
    $gameStatus = 'Не сыграно';
    $textCls = 'alert-info';
} else {
    $gameStatus = $isWin ? 'Выиграл' : 'Проиграл';
    $textCls = $isWin ? 'alert-success' : 'alert-danger';
}
?>
<div class="col-md-4 col-sm-8">
    <div class="dashboard-block">
        <div class="rotate">
            <i class="glyphicon glyphicon-<?= $label ?>"></i>
        </div>
        <div class="list-group details">
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= $model->lottery_id ?></span>
                Тираж</a>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= is_array($model->_bet) ? implode('&nbsp;', $model->_bet) : ''; ?></span>
                Ваши номера</a>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge"><?= empty($model->win_cnt) ? '&mdash;' : $model->win_cnt ?></span>
                Совпало номеров</a>
            <?php if (empty($draw)) { ?>
                <a class="list-group-item" href="<?= $url ?>">
                    <span class="badge"><?= $model->lottery->draw_d ?></span>
                    Дата розыгрыша</a>
                <?php
            } else {
                ?>
                <a class="list-group-item" href="<?= $url ?>">
                    <span class="badge"><?= $model->lottery->drawReadable ?></span>
                    Выигрышная комбинация</a>
                <a class="list-group-item" href="<?= $url ?>">
                    <span class="badge"><?= $model->paid_out > 0 ? $model->paid_out_readable : '&mdash;'; ?></span>
                    Сумма выигрыша</a>
            <?php } ?>
            <a class="list-group-item" href="<?= $url ?>">
                <span class="badge <?= $textCls ?>"><?= $gameStatus ?></span>
                Результат</a>
        </div>
        <a href="<?= $url ?>"><i class="more">Билет <?= $model->id ?></i></a>
    </div>
</div>