<?php
$this->title                   = 'Пригласить друга';
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<div class="site-invite-friend">
    <h1><?= $this->title ?></h1>
    <div class="page-text"><?= $inviteText ?></div>
    <?php
    if (!$isGuest) {
        echo $this->render('@app/modules/user/views/default/_referral_link');
    }
    echo '<hr>';
    ?>
    <?= $this->render('_invite_friend_form', ['model' => $model, 'isGuest' => $isGuest]); ?>
    <?= $this->render('@frontend/views/site/_button_back'); ?>
</div>
