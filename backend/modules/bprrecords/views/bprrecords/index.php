<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\bprrecords\models\BprrecordsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'BATCH PRODUCTION RECORDS';
$this->params['breadcrumbs'][] = 'Batch Production Records';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="bprrecords-index">
   <?php if($fromAuditLog!="Yes"){ ?>
   <p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add BPR</span>', ['create'], ['class' => 'btn btn-primary']) ?>
   </p>
    <?php } ?>

	<?php   
    $session = Yii::$app->session;
    $bprStatusSuccess = $session->get('bprStatusSuccess');
	$lockMsg = $session->get('LockError');
    $session->remove('bprStatusSuccess');
	$session->remove('LockError');
    if(isset($bprStatusSuccess) && $bprStatusSuccess!=''){ ?>
        <div id="msgE" class="alert alert-success">
          <strong>Success!</strong>&nbsp; <?=$bprStatusSuccess;?>
        </div>
    <?php } 
	 if(isset($lockMsg) && $lockMsg!=''){ ?>
		<div id="msgS" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$lockMsg;?>
		</div>
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
            [
				'attribute'=>'status',
				'label' => '',
				'format' => 'raw',
				'contentOptions' =>['style'=>'width:20px;'],
				'value' => function($data){
						$rflag = checkCommonFunctions::check_If_BPR_Approved($data->bpr_id_pk);
						$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
						$str1= '';
						if($fromAuditLog=="Yes") $str1='&al=1';
						if($rflag=="Yes")
							return '<a href="'.Yii::$app->homeUrl.'bprrecords/bprrecords/pdf?id='.$data->bpr_id_pk.$str1.'" title="Print to PDF" target="_blank"><span class="glyphicon glyphicon-print"></span></a>';
						elseif($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
							return '<span class="text-danger" title="This record is locked, because another user is accessing same"><i class="fa fa-lock"></i></span>';
						else
							return '<a href="'.Yii::$app->homeUrl.'bprrecords/bprrecords/view?id='.$data->bpr_id_pk.$str1.'" title="Edit"><span class="glyphicon glyphicon-upload"></span></a>';
					},
			],
			[	
				'class' => 'yii\grid\SerialColumn',
				'contentOptions' =>['style'=>'width:50px;'],
			],
			[
				'attribute'=>'product_part',
				'label' => 'Product Part',
				'contentOptions' =>['style'=>'width:70px;word-wrap:break-word;'],
				'value' => function($data){
						return htmlentities($data->mprdefination->product_part);
					},
			],
			[
				'attribute'=>'batch',
				'label' => 'Batch',
				'format' => 'raw',
				'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
				'value' => function($data){
						$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
						$str1= '';
						if($fromAuditLog=="Yes") $str1='&al=1';
						return '<a href="'.Yii::$app->homeUrl.'bprrecords/bprrecords/view?id='.$data->bpr_id_pk.$str1.'">'.$data->batch.'</a>';
					},
			],
			[
				'attribute'=>'product_name',
				'label' => 'Product Name',
				'format' => 'html',
				'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
				'value' => function($data){
						return htmlentities($data->mprdefination->product_name);
					},
			],
			[
				'attribute'=>'product_code',
				'label' => 'Product Code',
				'format' => 'html',
				'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
				'value' => function($data){
						return htmlentities($data->product_code);
					},
			],
			[
				'attribute'=>'MPR_version',
				'label' => 'MPR Version',
				'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
				'value' => 'mprdefination.MPR_version',
			],
			
			[
				'attribute'=>'status',
				'label' => 'Status',
				'format' => 'raw',
				'headerOptions' => ['style'=>'color:#3c8dbc;cursor:pointer;'],
				'contentOptions' =>['style'=>'width:150px;word-wrap:break-word;'],
				'value' => function($data){
						$rflag = checkCommonFunctions::check_If_BPR_Approved($data->bpr_id_pk);
						$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
						if($rflag=="Yes" && $fromAuditLog!="Yes")
						{
							$curStatus = getCommonFunctions::get_BPR_current_status($data->bpr_id_pk);
							$QuarantineStr = ''; $ApprovedStr = ''; $RejectedStr = '';
							if($curStatus=='Quarantine')
								$QuarantineStr = 'selected';
							elseif($curStatus=='Approved')
								$ApprovedStr = 'selected';
							elseif($curStatus=='Rejected')
								$RejectedStr = 'selected';
								
							$cmbbox = '<select name="" id="" class="form-control" onchange="javascript:changeBPRstaus(this.value,\''.$data->bpr_id_pk.'\')">
										<option value="Quarantine" '.$QuarantineStr.' >Quarantine</option>
										<option value="Approved" '.$ApprovedStr.' >Approved</option>
										<option value="Rejected" '.$RejectedStr.' >Rejected</option>
									</select>';
							return $cmbbox;
						}
						else
							return ''; 
					},
			],
			
			[
				'attribute'=>'status_approver',
				'label' => 'Status Approver',
				'format' => 'raw',
				'contentOptions' =>['style'=>'max-width:400px;word-wrap:break-word;'],
				'headerOptions' => ['style'=>'color:#3c8dbc;cursor:pointer;'],
				'value' => function($data){
							$output = getCommonFunctions::get_BPR_last_status_approver($data->bpr_id_pk);
							if($output['personname'])
							{
								$retStr = $output['personname'];
								if($output['signatureDoc']!='')
									$retStr .= "<br/>".$output['signatureDoc'];
								return $retStr;
							}
							else
								return ''; 
					},
			],

			[
				'attribute'=>'time_stamp',
				'label' => 'Time Stamp',
				'format' => 'raw',
				'headerOptions' => ['style'=>'color:#3c8dbc;cursor:pointer;'],
				'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word;'],
				'value' => function($data){
							$output = getCommonFunctions::get_BPR_last_status_approver($data->bpr_id_pk);
							if($output['dttm'])
								return $output['dttm'].Yii::$app->params['timezoneVar'];
							else
								return ''; 
					},
			],
            //['class' => 'yii\grid\ActionColumn'],
        ],
		'rowOptions'=>function ($model, $key, $index, $grid){
				$class = ''; $titletxt = '';
				$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_pk);
				$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($model->mpr_definition_id_fk);
				$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($model->mpr_definition_id_fk);
				if($rflag=="Yes")
					$class .= ' mprApproved';
				if($eflag=="Yes")
				{
					$class .= ' eqpExpired';
					$titletxt = 'Equipment related to this BPR is either expired or deleted.';
				}
				if($pflag=="Yes")
				{
					$class .= ' eqpExpired';
					$titletxt = 'Product related to this MPR is deleted.';
				}
				return array('key'=>$key,'index'=>$index,'class'=>$class,'title'=>$titletxt);
		},
    ]); ?>
	</div>
</div>
<!-- Modal Starts here-->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal Ends here-->
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/bprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
