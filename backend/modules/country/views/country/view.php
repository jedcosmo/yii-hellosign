<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\country\models\Country */

$this->title = 'VIEW COUNTRY';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Countries', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = $model->name;

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>
<div class="country-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->country_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->country_id_pk], [
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
           // 'country_id_pk',
            [                      
				'attribute'=>'name',
				'format'=>'html',
            	'value' => wordwrap(htmlentities($model->name),25,'<br/>',1),
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
