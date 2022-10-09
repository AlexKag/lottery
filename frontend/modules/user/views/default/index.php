<?php

use trntv\filekit\widget\Upload;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\widgets\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;

/* @var $this yii\web\View */
/* @var $model common\base\MultiModel */
/* @var $form yii\widgets\ActiveForm */

$this->title = Yii::t('frontend', 'Личный кабинет');
$this->params['breadcrumbs'][] = [
    'label'    => $this->title,
//    'template' => "<li><span>{link}</span></li>\n"
];


$user        = Yii::$app->user->identity;
$isGuest     = !isset(Yii::$app->user->identity);
?>

<div class="user-profile-summary">
    <div class="page-header">
        <h1>Profile summary</h1>
    </div>
    <div class="row">
        <div class="col-xs-6 col-md-3">
            <img src="<?= Yii::$app->user->identity->userProfile->getAvatar() ?>" class="user-image">
        </div>
    </div>
    <p>Username:  <span class="badge"><?= Yii::$app->user->identity->username ?></span></p>
    <p>Зарегистрирован:  <span class="badge"><?= Yii::$app->formatter->asDate(Yii::$app->user->identity->created_at) ?></span></p>
    <p>E-mail:  <span class="badge"><?= Yii::$app->user->identity->email ?></span></p>
    <p>Счёт:  <span class="badge"><?= Yii::$app->user->identity->userProfile->account?></span></p>

</div>
