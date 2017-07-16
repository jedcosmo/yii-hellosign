<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\mprapprovals\models\Mprapprovals;

$gridactTpl = '{view}';

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$mprid = (isset($_REQUEST['mprid']))?$_REQUEST['mprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=mprApprovals';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$mprid.'&tab=mprApprovals';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "DELETED MPR APPROVALS";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'MPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Deleted MPR Approvals';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>

<div class="table-responsive">
<?= GridView::widget([
'id' => 'deleteListing',
'dataProvider' => $dataProvider,
'filterModel' => $searchModel,
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
				if($data->approval_status!='Approved' && $isMDeleted!='1')
				{
					$chk1 = '';
				}
				else
				{
					$chk1 = '';
				}
				$unitnm = $commonModel->getPersonName($data->approval_person_id_fk);
				if($unitnm)
					return $chk1."&nbsp;&nbsp;".ucfirst($unitnm);
				else
					return '';
			},
	],
	[
		'attribute'=>'approval_job_function',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		'value' => function($data){
						return htmlentities($data->approval_job_function);
					},
	],
	[
		'attribute'=>'approval_datetime',
		'format' => 'raw',
		'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
		'value' => function($data){
			if(isset($data->approval_datetime) && $data->approval_datetime!='0000-00-00 00:00:00')
				return date("m/d/Y H:i:s",strtotime($data->approval_datetime)).Yii::$app->params['timezoneVar'];
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
				if($data->verified_status!='Verified' && $isMDeleted!='1')
				{
					$chk2 = '';
				}
				else
				{
					$chk2 = '';
				}
				$unitnm = $commonModel->getPersonName($data->verifier_person_id_fk);
				if($unitnm)
					return $chk2."&nbsp;&nbsp;".ucfirst($unitnm);
				else
					return '';
			},
	],
	[
		'attribute'=>'verifier_job_function',
		'format' => 'raw',
		'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
		'value' => function($data){
						return htmlentities($data->verifier_job_function);
					},
	],
	[
		'attribute'=>'verified_datetime',
		'format' => 'raw',
		'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
		'value' => function($data){
			if(isset($data->verified_datetime) && $data->verified_datetime!='0000-00-00 00:00:00')
				return date("m/d/Y H:i:s",strtotime($data->verified_datetime)).Yii::$app->params['timezoneVar'];
			else
				return '';
		},
	],
	[
			'attribute'=>'reasonIsDeleted',
			'format' => 'raw',
			'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
			'value' => function($data){
						return wordwrap(htmlentities($data->reasonIsDeleted),15,'<br/>',1);
					},
	    ],
		[
	            'attribute'=>'deleted_datetime',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
	            'value' => function($data){
					if($data->deleted_datetime!='0000-00-00 00:00:00')
						return date("m/d/Y H:i:s",strtotime($data->deleted_datetime)).Yii::$app->params['timezoneVar'];
					else
						return "";
					},
	        ],
        [				
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}&nbsp;&nbsp;{restore}',
			'visible' => $visibleFlag,
				'buttons' => [
					'view' => function ($url) {
						$minstid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-eye-open"></span>',
							Yii::$app->homeUrl.'mprapprovals/mprapprovals/view?'.$minstid.'&mprid='.$_GET['mprid'], 
							[
								'title' => 'View',
								'data-pjax' => '0',
							]
						);
					},
					'restore' => function ($url) {
						$minstid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-share-alt"></span>',
							Yii::$app->homeUrl.'mprapprovals/mprapprovals/restore?'.$minstid.'&mprid='.$_GET['mprid'], 
							[
								'title' => 'Restore',
								'data-pjax' => '0',
								'data' => [
										'confirm' => 'Are you sure you want to restore this item?',
										'method' => 'post',
									],
							]
						);
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

$searchClassNm = $searchModel->formName();
$objName1 = $searchClassNm."[approval_datetime]";
$objName2 = $searchClassNm."[verified_datetime]";
$objName3 = $searchClassNm."[deleted_datetime]";
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
	
	var myDtObj3 = document.getElementsByName('".$objName3."');
	$(myDtObj3).daterangepicker({
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
