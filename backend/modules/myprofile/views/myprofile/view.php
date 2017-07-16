<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\person\models\Person;

/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */

$this->title = 'MY PROFILE:'.$model->person_id_pk;

$this->params['breadcrumbs'][] = 'My Profile';

?>
<div class="person-view">
	<?php if($model->isDeleted=='0'){ ?>
    <p>
    	<?php echo Html::a('Update', ['update', 'id' => $model->person_id_pk], ['class' => 'btn btn-primary'])."&nbsp;&nbsp;"; ?>
    </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'person_id_pk',
			[
	            'attribute'=>'first_name',
				'format'=>'html',
	            'value' => wordwrap(htmlentities($model->first_name),15,'<br/>',1),
	        ],
			[
	            'attribute'=>'last_name',
				'format'=>'html',
	            'value' => wordwrap(htmlentities($model->last_name),15,'<br/>',1),
	        ],
            'phone',
            'fax',
			[
	            'attribute'=>'address',
				'format'=>'ntext',
	            'value' => wordwrap($model->address,30,' ',1),
	        ],
			[                      
				'attribute'=>'country_id_fk',
				'format'=>'raw',
            	'value' => $model->showCountryName($model->country_id_fk),
        	],
			[                      
				'attribute'=>'state_id_fk',
				'format'=>'raw',
            	'value' => $model->showStateName($model->state_id_fk),
        	],
			[                      
				'attribute'=>'city_id_fk',
				'format'=>'raw',
            	'value' => $model->showCityName($model->city_id_fk),
        	],
            'pobox',
            'zip_pincode',
			[
	            'attribute'=>'emailid',
				'format'=>'email',
	            'value' => wordwrap($model->emailid,60),
	        ],
			[
	            'attribute'=>'user_name_person',
				'format'=>'html',
	            'value' => wordwrap(htmlentities($model->user_name_person),16,'<br/>',1),
	        ],
			[
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)): "",
	        ],
        ],
    ]) ?>

</div>
