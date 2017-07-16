<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\adminUser\models\Admin */

$this->title = 'Update Admin';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="admin-update">

    <!--<h1><?php // Html::encode($this->title) ?></h1>--->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
