<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\city\models\City */

$this->title = "VIEW CITY";
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Cities', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = 'View City';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>
<div class="city-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->city_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->city_id_pk], [
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
            //'city_id_pk',
            [                      
				'attribute'=>'name',
				'format'=>'html',
            	'value' => wordwrap(htmlentities($model->name),25,'<br/>',1),
			],
			[                      
				'attribute'=>'state_id_fk',
				'format'=>'raw',
            	'value' => $model->showStateName($model->state_id_fk),
        	],
			[                      
				'attribute'=>'country_id_fk',
				'format'=>'raw',
            	'value' => $model->showCountryName($model->country_id_fk),
        	],
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => (($model->isDeleted) == 1? wordwrap(htmlentities($model->reasonIsDeleted),16,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        	],
			
        ],
    ]) ?>

</div>
