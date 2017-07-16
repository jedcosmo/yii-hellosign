<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\activitylog\models\ActivityLog */

$this->title = "AUDIT LOG DETAILS";
$this->params['breadcrumbs'][] = ['label' => 'Audit Log', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Audit Log Details';
?>
<div class="activity-log-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'userid',
			[
	            'attribute'=>'added_date',
				'format' => 'raw',
	            'value' => date("m/d/Y H:i:s",strtotime($model->added_date)).Yii::$app->params['timezoneVar'],
	        ],
            'type',
            'action',
          /*  'message:ntext',*/
			[
	            'attribute'=>'urltext',
				'format' => 'raw',
	            'value' => $model->showloglink($model),
	        ],
        ],
    ]) ?>

</div>
