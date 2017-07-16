<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\unit\models\Unit */

$this->title = "View Unit";
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Units', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = 'View Unit';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

?>
<div class="unit-view">

	<?php if($model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->unit_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->unit_id_pk], [
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
            //'unit_id_pk',
			[
	            'attribute'=>'name',
				'format'=>'html',
	            'value' => htmlentities($model->name),
	        ],
			[
	            'attribute'=>'description',
				'format'=>'ntext',
	            'value' => wordwrap($model->description,30,' ',1),
	        ],
			[
				'attribute'=>'symbols',
				'format'=>'raw',
				'value'=> $model->showSymbol($model->symbols),
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
