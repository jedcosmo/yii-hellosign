<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\activitylog\models\ActivityLog */

$this->title = 'Update Activity Log: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Activity Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="activity-log-update">

  
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
