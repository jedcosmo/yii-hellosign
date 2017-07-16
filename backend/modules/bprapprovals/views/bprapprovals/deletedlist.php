<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mprapprovals\models\MprapprovalsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;

$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=bprApprovals';
$listingPg = '/bprrecords/bprrecords';
$bclabel = 'BPR Records';

$this->title = 'DELETED BPR APPROVALS';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Excecuted BPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Deleted BPR Approvals';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="mprapprovals-index">
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
				if($data->approval_status!='Approved')
				{
					$chk1 = '<input type="checkbox" value="Yes" name="performerChk_'.$data->bpr_approval_id_pk.'" id="performerChk_'.$data->bpr_approval_id_pk.'" onclick="javascript:approveBPR('.$data->bpr_approval_id_pk.','.$data->bpr_id_fk.');">';
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
				if($data->verified_status!='Verified')
				{
					$chk2 = '<input type="checkbox" value="Yes" name="verifierChk_'.$data->bpr_approval_id_pk.'" id="verifierChk_'.$data->bpr_approval_id_pk.'" onclick="javascript:verifyBPR('.$data->bpr_approval_id_pk.','.$data->bpr_id_fk.');">';
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
						return htmlentities($data->reasonIsDeleted);
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
							Yii::$app->homeUrl.'bprapprovals/bprapprovals/view?'.$minstid.'&bprid='.$_GET['bprid'], 
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
							Yii::$app->homeUrl.'bprapprovals/bprapprovals/restore?'.$minstid.'&bprid='.$_GET['bprid'], 
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
