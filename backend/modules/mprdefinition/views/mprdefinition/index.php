<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mprdefinition\models\MprdefinitionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MPR DEFINITIONS';
$this->params['breadcrumbs'][] = 'MPR Definitions';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="mprdefination-index">
	<!-- show success message on screen -->
	<?php $session = Yii::$app->session;
	$successDeleteRestore = $session->get('successDeleteRestore');
	$lockMsg = $session->get('LockError');
	$session->remove('successDeleteRestore');
	$session->remove('LockError');
	
	if(isset($successDeleteRestore) && $successDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-success">
		  <strong>Success!</strong>&nbsp; <?=$successDeleteRestore;?>
		</div>
	<?php }     
    if(isset($lockMsg) && $lockMsg!=''){ ?>
		<div id="msgS" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$lockMsg;?>
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
	
	<?
	$successMPRCopy = $session->get('successMPRCopy');
	$mprversionError = $session->get('mprversionError');
	$session->remove('successMPRCopy');
	$session->remove('mprversionError');
	if(isset($successMPRCopy) && $successMPRCopy!=''){ ?>
		<div id="msgS" class="alert alert-success">
		  <strong>Success!</strong>&nbsp; <?=$successMPRCopy;?>
		</div>
	<?php } 
	   if(isset($mprversionError) && $mprversionError!=''){ ?>
		<div id="msgE" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$mprversionError;?>
		</div>
	<?php } ?>

	<?php if($fromAuditLog!="Yes"){ ?>
    <p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add MPR Definition</span>', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-download"></i>&nbsp;&nbsp;<span>Export</span>', ['excel?'.$_SERVER['QUERY_STRING']], ['class' => 'btn btn-primary', 'method'=>'get']) ?>
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted MPRs</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?php } ?>
    <div class="table-responsive">
    <?= GridView::widget([
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
	            'attribute'=>'product_code',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_code);
					},
	        ],
			[
	            'attribute'=>'MPR_version',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'word-wrap:break-word;'],
	            'value' => 'MPR_version',
	        ],
			[
	            'attribute'=>'product_part',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_part);
					},
	        ],
            [
	            'attribute'=>'product_name',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:420px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_name);
					},
	        ],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;{addcopy}',
				'visible' => $visibleFlag,
				'buttons' => [
					'view' => function ($url,$data) {
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							$iconStr = '<span class="text-danger" title="This record is locked, because another user is accessing same"><i class="fa fa-lock"></i></span>';
							$url = 'javascript:void(0);';
						}
						else
							$iconStr = '<span class="glyphicon glyphicon-eye-open"></span>';
						return Html::a(
							$iconStr,
							$url, 
							[
								'title' => 'View',
								'data-pjax' => '0',
							]
						);
					},
					'addcopy' => function ($url,$data) {
						$rflag = checkCommonFunctions::check_If_MPR_Approved($data->mpr_defination_id_pk);
						if($rflag=="Yes"){
						return Html::a(
							'<span class="glyphicon glyphicon-plus-sign"></span>',
							$url, 
							[
								'title' => 'Copy',
								'data-pjax' => '0',
							]
						);
					}else{ return false; }
					},
				],
			],
        ],
		'rowOptions'=>function ($model, $key, $index, $grid){
				$class = '';$titletxt = '';
				$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
				$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($model->mpr_defination_id_pk);
				$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($model->mpr_defination_id_pk);
				if($rflag=="Yes")
					$class .= ' mprApproved';
				if($eflag=="Yes")
				{
					$class .= ' eqpExpired';
					$titletxt = 'Equipment related to this MPR is either expired or deleted.';
				}
				if($pflag=="Yes")
				{
					$class .= ' eqpExpired';
					$titletxt = 'Product related to this MPR is deleted.';
				}
				return array('key'=>$key,'index'=>$index,'class'=>$class, 'title'=>$titletxt);
		},
    ]); ?>
	</div>
</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/mprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
