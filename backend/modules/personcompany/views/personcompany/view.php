<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\company\models\Company */

$this->title = "PERSON COMPANY DETAILS: ". $model->company_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Person Companies', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Person Companies', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = "Person Company Details";
?>
<div class="company-view">
	<?php if($model->isDeleted=='0'){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->company_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->company_id_pk], [
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
            'company_id_pk',
			[                      
				'attribute'=>'name',
				'format'=>'html',
            	'value' => wordwrap(htmlentities($model->name),25,'<br/>',1),
			],
			[                      
				'attribute'=>'address1',
				'format'=>'ntext',
            	'value' => wordwrap($model->address1,25,' ',1),
			],
			[                      
				'attribute'=>'address2',
				'format'=>'ntext',
            	'value' => wordwrap($model->address2,25,' ',1),
			],
			[                      
				'attribute'=>'country_id_fk',
				'format'=>'html',
            	'value' => wordwrap(nl2br($model->showCountryName($model->country_id_fk)),25,'<br/>',1),
        	],
			[                      
				'attribute'=>'state_id_fk',
				'format'=>'html',
            	'value' => wordwrap(nl2br($model->showStateName($model->state_id_fk)),25,'<br/>',1),
        	],
			[                      
				'attribute'=>'city_id_fk',
				'format'=>'html',
            	'value' => wordwrap(nl2br($model->showCityName($model->city_id_fk)),25,'<br/>',1),
        	],
         	'pobox',
            'zip_postalcode',
            [
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	        ],
        ],
    ]) ?>

</div>
