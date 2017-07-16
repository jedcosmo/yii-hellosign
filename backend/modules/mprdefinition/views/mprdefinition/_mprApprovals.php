<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\mprapprovals\models\Mprapprovals;

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_pk,'bpr_mpr_defination','mpr_defination_id_pk');

if($rflag=="Yes" || $isMDeleted=='1')
	$gridactTpl = '{view}';
else
	$gridactTpl = '{view}&nbsp;{update}&nbsp;{delete}';

$commonModel = new getCommonFunctions();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
	<!-- show success message on screen -->
	<?php $session = Yii::$app->session;
	$successDeleteRestore = $session->get('successDeleteRestore');
	$session->remove('successDeleteRestore');

	if(isset($successDeleteRestore) && $successDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-success">
		  <strong>Success!</strong>&nbsp; <?=$successDeleteRestore;?>
		</div>
	<?php } ?>
	<!-- show error message on screen -->
    <?php
	$errorDeleteRestore = $session->get('errorDeleteRestore');
	$session->remove('errorDeleteRestore');

	if(isset($errorDeleteRestore) && $errorDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$errorDeleteRestore;?>
		</div>
	<?php } ?>
    
<?php $session = Yii::$app->session;
$errorLogin = $session->get('errorLogin');
$successLogin = $session->get('successLogin');
$session->remove('successLogin');
$session->remove('errorLogin');
if(isset($errorLogin) && $errorLogin!=''){ ?>
	<div id="msgS" class="alert alert-danger">
	  <span class="glyphicon glyphicon-warning-sign"></span>&nbsp; <?=$errorLogin;?>
	</div>
<?php } if(isset($successLogin) && $successLogin!=''){ ?>
	<div id="msgE" class="alert alert-success">
	  <strong>Success!</strong>&nbsp; <?=$successLogin;?>
	</div>
<?php } ?>
<?php if($rflag!='Yes' && $isMDeleted!='1' && $fromAuditLog!="Yes"){ ?>
<p class="text-right">
<a href="<?=Yii::$app->homeUrl.'mprapprovals/mprapprovals/create?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<span>Add MPR Approval</span></a>
&nbsp;&nbsp;
    <a href="<?=Yii::$app->homeUrl.'mprapprovals/mprapprovals/deletedlist?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-warning"><i class="fa fa-trash"></i>&nbsp;<span>Deleted MPR Approvals</span></a>
</p>
<?php } ?>
<div class="table-responsive">
<?= GridView::widget([
'id' => 'deleteListing',
'dataProvider' => $mapprdataProvider,
'filterModel' => $mapprsearchModel,
'pager' => [
			'firstPageLabel' => '&laquo;',
			'lastPageLabel' => '&raquo;',
			'nextPageLabel' => '&rsaquo;',
			'prevPageLabel' => '&lsaquo;',
			],	
'columns' => [
	['class' => 'yii\grid\SerialColumn'],

	[
		'attribute'=>'approval_person_id_fk',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		'value' => function($data){
				$commonModel = new getCommonFunctions();
				$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($data->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
				$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
				if($data->approval_status!='Approved' && $isMDeleted!='1' && $fromAuditLog!="Yes")
				{
					$chk1 = '<input type="checkbox" value="Yes" name="performerChk_'.$data->mpr_approval_id_pk.'" id="performerChk_'.$data->mpr_approval_id_pk.'" onclick="javascript:approveMPR('.$data->mpr_approval_id_pk.','.$data->mpr_defination_id_fk.');" >';
				}
				else
				{
					$chk1 = '';
				}
				$unitnm = $commonModel->getPersonName($data->approval_person_id_fk);
				//if approval person is exists or not
				$approvalExitsArray = checkCommonFunctions::check_if_person_exists($data->approval_person_id_fk);
				$expClass = "";
				if($approvalExitsArray['exists'] == 'No'){
					$expClass = "eqpExpired";
				}
				if($unitnm)
					return $chk1."<span class='".$expClass."'>&nbsp;&nbsp;".ucfirst($unitnm)."</span>";
				else
					return '';
			},
	],
	[
		'attribute'=>'approval_job_function',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		'value' => function($data){
						//if approval person is exists or not
						$approvalExitsArray = checkCommonFunctions::check_if_person_exists($data->approval_person_id_fk);
						$expClass = "";
						if($approvalExitsArray['exists'] == 'No'){
							$expClass = "eqpExpired";
						}
						return "<span class='".$expClass."'>".htmlentities($data->approval_job_function)."</span>";
					},
	],
	[
		'attribute'=>'approval_datetime',
		'format' => 'raw',
		'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
		'value' => function($data){
			if(isset($data->approval_datetime) && $data->approval_datetime!='0000-00-00 00:00:00')
			{
				$dateTimeStrAppr =  date("m/d/Y H:i:s",strtotime($data->approval_datetime)).Yii::$app->params['timezoneVar'];
				if($data->HS_approver_signature_id!='')
				{
					$dateTimeStrAppr .= "<br/>".getCommonFunctions::getSignedDocFromSignatureId($data->HS_approver_signature_id);
				}
				return $dateTimeStrAppr;
			}
			else
				return '';
		},
	],
	[
		'attribute'=>'verifier_person_id_fk',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		 'value' => function($data){
				$commonModel = new getCommonFunctions();
				$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($data->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
				$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
				if($data->verified_status!='Verified' && $isMDeleted!='1' && $fromAuditLog!="Yes")
				{
					$chk2 = '<input type="checkbox" value="Yes" name="verifierChk_'.$data->mpr_approval_id_pk.'" id="verifierChk_'.$data->mpr_approval_id_pk.'" onclick="javascript:verifyMPR('.$data->mpr_approval_id_pk.','.$data->mpr_defination_id_fk.');" >';
				}
				else
				{
					$chk2 = '';
				}
				$unitnm = $commonModel->getPersonName($data->verifier_person_id_fk);
				//if verifier person is exists or not
				$veriferExitsArray = checkCommonFunctions::check_if_person_exists($data->verifier_person_id_fk);
				$expClass = "";
				if($veriferExitsArray['exists'] == 'No'){
					$expClass = "eqpExpired";
				}
				if($unitnm)
					return $chk2."<span class='".$expClass."'>&nbsp;&nbsp;".ucfirst($unitnm)."</span>";
				else
					return '';
			},
	],
	[
		'attribute'=>'verifier_job_function',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		'value' => function($data){
						//if verifier person is exists or not
						$veriferExitsArray = checkCommonFunctions::check_if_person_exists($data->verifier_person_id_fk);
						$expClass = "";
						if($veriferExitsArray['exists'] == 'No'){
							$expClass = "eqpExpired";
						}
						return "<span class='".$expClass."'>".htmlentities($data->verifier_job_function)."</span>";
					},
	],
	[
		'attribute'=>'verified_datetime',
		'format' => 'raw',
		'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
		'value' => function($data){
			if(isset($data->verified_datetime) && $data->verified_datetime!='0000-00-00 00:00:00')
			{
				$dateTimeStrVerify = date("m/d/Y H:i:s",strtotime($data->verified_datetime)).Yii::$app->params['timezoneVar'];
				if($data->HS_verifier_signature_id!='')
				{
					$dateTimeStrVerify .= "<br/>".getCommonFunctions::getSignedDocFromSignatureId($data->HS_verifier_signature_id);
				}
				return $dateTimeStrVerify;
			}
			else
				return '';
		},
	],
	[			
		'class' => 'yii\grid\ActionColumn',
		'template' => $gridactTpl,
		'visible' => $visibleFlag,
		'buttons' => [
			'view' => function ($url) {
				$mprid=  parse_url($url,PHP_URL_QUERY);
				return Html::a(
					'<span class="glyphicon glyphicon-eye-open"></span>',
					Yii::$app->homeUrl.'mprapprovals/mprapprovals/view?'.$mprid.'&mprid='.$_GET['id'], 
					[
						'title' => 'View',
						'data-pjax' => '0',
					]
				);
			},
			'update' => function ($url,$data) {
				$mprid=  parse_url($url,PHP_URL_QUERY);
				$commonModel = new getCommonFunctions();
				$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($data->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
				$approvalVeriferIsDeletedBeforeApprove = checkCommonFunctions::checkIfMPRApprovalVeriferIsDeletedBeforeApprove($data->mpr_approval_id_pk);
				if($data->approval_status != 'Approved' || ($data->verified_status != 'Verified' && $isMDeleted != '1') || $approvalVeriferIsDeletedBeforeApprove == 'Yes')
				{
					return Html::a(
						'<span class="glyphicon glyphicon-pencil"></span>',
						Yii::$app->homeUrl.'mprapprovals/mprapprovals/update?'.$mprid.'&mprid='.$_GET['id'], 
						[
							'title' => 'Update',
							'data-pjax' => '0',
						]
					);
				}
				else
				{
					return "";
				}
			},
			'delete' => function ($url, $data) {
				$mprid=  parse_url($url,PHP_URL_QUERY);
				
				$commonModel = new getCommonFunctions();
				$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($data->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
				if($data->approval_status!='Approved' && $isMDeleted!='1' && $data->verified_status!='Verified')
				{
					return Html::a(
						'<span class="glyphicon glyphicon-trash"></span>',
						Yii::$app->homeUrl.'mprapprovals/mprapprovals/delete?'.$mprid.'&mprid='.$_GET['id'], 
						[
							'title' => 'Delete',
							'data-pjax' => '0',
							'data-method' => 'post',
							'data-confirm' => 'Are you sure you want to delete this item?',
							'aria-label' => 'Delete',
						]
					);
				}
				else
				{
					return "";
				}
			},
		],
	],
],
]); ?>
</div>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

$searchClassNm = $mapprsearchModel->formName();
$objName1 = $searchClassNm."[approval_datetime]";
$objName2 = $searchClassNm."[verified_datetime]";
$this->registerJs("$(function() {
	var myDtObj1 = document.getElementsByName('".$objName1."');
	$(myDtObj1).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#deleteListing').yiiGridView('applyFilter');
		},
	});
	
	var myDtObj2 = document.getElementsByName('".$objName2."');
	$(myDtObj2).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#deleteListing').yiiGridView('applyFilter');
		},
	});

});");
?>
<style>
.ui-daterangepicker-arrows input.ui-rangepicker-input{ height:1.8em!important; }
.ui-daterangepicker-arrows .ui-daterangepicker-prev{ top:8px; }
.ui-daterangepicker-arrows .ui-daterangepicker-next{ top:8px; }
</style>