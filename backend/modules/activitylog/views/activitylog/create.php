<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\activitylog\models\ActivityLog */

$this->title = 'Create Activity Log';
$this->params['breadcrumbs'][] = ['label' => 'Activity Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activity-log-create">

  

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
