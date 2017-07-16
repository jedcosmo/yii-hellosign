<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_pk,'bpr_mpr_defination','mpr_defination_id_pk');

$authorClass = ''; $authorTitle = '';
$unitClass = ''; $unitTitle = '';
$companyClass = ''; $companyTitle = '';

$isAuthorDeleted = checkCommonFunctions::check_If_Record_Deleted($model->author,'bpr_person','person_id_pk');
if($isAuthorDeleted=="Yes")
{
	$authorClass = 'error';
	$authorTitle = 'This person is deleted';
}
	
$isMprUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->MPR_unit_id,'bpr_unit','unit_id_pk');
if($isMprUnitDeleted=="Yes")
{
	$unitClass = 'error';
	$unitTitle = 'This unit is deleted';
}
	
$isCompanyDeleted = checkCommonFunctions::check_If_Record_Deleted($model->company_id_fk,'bpr_company','company_id_pk');
if($isCompanyDeleted=="Yes")
{
	$companyClass = 'error';
	$companyTitle = 'This company is deleted';
}

$commonModel = new getCommonFunctions();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="col-sm-12 col-md-10 col-xs-12" style="padding-left:0">
<?= DetailView::widget([
	'model' => $model,
	'attributes' => [
		[                      
			'attribute'=>'product_code',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->product_code),50,"<br/>",1),
		],
		[                      
			'attribute'=>'product_part',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->product_part),50,"<br/>",1),
		],
		'MPR_version',
		[                      
			'attribute'=>'author',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => "<span class='".$authorClass."' title='".$authorTitle."'>".wordwrap($commonModel->getPersonName($model->author),50,'<br/>',1)."</span>",
		],
		[                      
			'attribute'=>'product_name',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->product_name),50,"<br/>",1),
		],
		[                      
			'attribute'=>'formulation_id',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->formulation_id),50,"<br/>",1),
		],
		[                      
			'attribute'=>'product_strength',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->product_strength),50,"<br/>",1),
		],
		[                      
			'attribute'=>'batch_size',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap(htmlentities($model->batch_size),50,"<br/>",1),
		],
		[                      
			'attribute'=>'MPR_unit_id',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => "<span class='".$unitClass."' title='".$unitTitle."'>".wordwrap($commonModel->getFieldNameValue("bpr_unit","name","unit_id_pk",$model->MPR_unit_id),50,'<br/>',1)."</span>",
		],
		[                      
			'attribute'=>'theoritical_yield',
			'format'=>'ntext',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap($model->theoritical_yield,50," ",1),
		],
		[                      
			'attribute'=>'company_id_fk',
			'format'=>'html',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => "<span class='".$companyClass."' title='".$companyTitle."'>".wordwrap($commonModel->getFieldNameValue("bpr_company","name","company_id_pk",$model->company_id_fk),50,'<br/>',1)."</span>",
		],
		[                      
			'attribute'=>'purpose',
			'format'=>'ntext',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap($model->purpose,50," ",1),
		],
		[                      
			'attribute'=>'scope',
			'format'=>'ntext',
			'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
			'value' => wordwrap($model->scope,50," ",1),
		],
		[
			'attribute'=>'created_datetime',
			'format' => 'raw',
			'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	    ],
		[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
				'contentOptions'=>['style'=>'max-width:300px;word-wrap:break-word;'],
            	'value' => (($model->isDeleted) == 1? wordwrap($model->reasonIsDeleted,50,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        ],
	],
]) ?>
</div>
<div class="col-sm-12 col-md-2 col-xs-12">
	
	<?php 
		
	if($rflag!='Yes' && $isMDeleted=='No' && $fromAuditLog!="Yes"){ ?>
	<?= Html::a('Update', ['update', 'id' => $model->mpr_defination_id_pk], ['class' => 'btn btn-primary btn-block btn-lg']) ?>
	<br/>
	<?= Html::a('Delete', ['delete', 'id' => $model->mpr_defination_id_pk], [
		'class' => 'btn btn-danger btn-block btn-lg',
		'data' => [
			'confirm' => 'Are you sure you want to delete this item?',
			'method' => 'post',
		],
	]) ?>
	<?php } ?>
	
</div>
