<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\sitesetting\models\Sitesetting */

$this->title = 'Update Site Information: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Site Information', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sitesetting-update">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
