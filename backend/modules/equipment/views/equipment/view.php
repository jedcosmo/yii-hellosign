<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipment\models\Equipment */

$this->title = 'EQUIPMENT DETAILS: '.$model->equipment_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Equipments', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = 'Equipment Details';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>
<div class="equipment-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->equipment_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->equipment_id_pk], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'equipment_id_pk',
			[                      
				'attribute'=>'name',
				'format'=>'html',
            	'value' => wordwrap(htmlentities($model->name),25,'<br/>',1),
			],
			[                      
				'attribute'=>'model',
				'format'=>'html',
            	'value' => htmlentities($model->model),
			],
			[                      
				'attribute'=>'serial',
				'format'=>'html',
            	'value' => htmlentities($model->serial),
			],
			[                      
				'attribute'=>'caliberation_due_date',
				'format'=>'raw',
            	'value' => ($model->caliberation_due_date && $model->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($model->caliberation_due_date)).Yii::$app->params['timezoneVar']:'',
			],
           	[                      
				'attribute'=>'preventive_m_due_date',
				'format'=>'raw',
            	'value' => ($model->preventive_m_due_date && $model->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($model->preventive_m_due_date)).Yii::$app->params['timezoneVar']:'',
			],
            [                      
				'attribute'=>'document_id_fk',
				'format'=>'raw',
            	'value' => $model->showDocument($model->document_id_fk),
        	],
            [
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	        ],
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => (($model->isDeleted) == 1? wordwrap($model->reasonIsDeleted,16,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        	],
        ],
    ]) ?>

</div>
