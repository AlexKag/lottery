<?php
/**
 * @var $this \yii\web\View
 * @var $model \common\models\Page
 */
$this->title                   = $model->title;
$this->params['breadcrumbs'][] = [
    'label' => $this->title,
];
?>
<!--<div class="content">-->
    <h1><?php echo $model->title ?></h1>
    <?php echo $model->body ?>
    <?= $this->render('@frontend/views/site/_button_back'); ?>
<!--</div>-->