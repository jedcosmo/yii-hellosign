<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\person\models\Person;

/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */

$this->title = 'PERSON DETAILS:'.$model->person_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Persons', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Persons', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = 'Person Details';

$visibleFlag = false;
if(Yii::$app->user->identity->is_super_admin==1)	$visibleFlag = true;

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";


$isRoleDeleted = checkCommonFunctions::check_If_Record_Deleted($model->role_id_fk,'bpr_role','role_id_pk');
$roleClass = '';
$roleTitle = '';
if($isRoleDeleted=="Yes")
{
	$roleClass = 'error';
	$roleTitle = 'This role is deleted';
}
?>
<div class="person-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No" && checkCommonFunctions::check_if_role_is_administrator($model->role_id_fk)!='Yes'){ ?>
    <p>
    	<?php 
 
        echo Html::a('Update', ['update', 'id' => $model->person_id_pk], ['class' => 'btn btn-primary'])."&nbsp;&nbsp;";

		if($model->role_id_fk != '-1' && $model->person_id_pk!=Yii::$app->user->id)
		{  
			echo Html::a('Delete', ['delete', 'id' => $model->person_id_pk], [
				'class' => 'btn btn-danger',
				'data' => [
					'confirm' => 'Are you sure you want to delete this item?',
					'method' => 'post',
				],
			]);
        } ?>
    </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
			'person_id_pk',
			/*[
	            'attribute'=>'super_company_id_fk',
				'format'=>'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => wordwrap(htmlentities(getCommonFunctions::getFieldNameValue("bpr_person_company","name","company_id_pk",$model->super_company_id_fk)),50,'<br/>',1),
				'visible' => $visibleFlag,
	        ],*/
			[                      
				'attribute'=>'role_id_fk',
				'format'=>'raw',
            	'value' => "<span class='".$roleClass."' title='".$roleTitle."'>".$model->showRoleName($model->role_id_fk)."</span>",
        	],
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
