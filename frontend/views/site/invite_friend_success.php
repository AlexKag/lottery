<?php
//use yii\helpers\Url;

$this->title                   = 'Приглашение отправлено';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<div class="site-invite-friend">
    <div class="jumbotron">
    <h1><?= $this->title ?></h1>
        <p>Приглашение вашему другу отправлено на адрес <span class="badge"><?= $email ?></span>.</p>
    </div>
    <?= $this->render('@frontend/views/site/_button_back'); ?>
</div>