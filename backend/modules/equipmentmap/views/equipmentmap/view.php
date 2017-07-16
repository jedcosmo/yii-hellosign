<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipmentmap\models\Equipmentmap */
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=equipments';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=equipments';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "VIEW EQUIPMENT";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = "View Equipment";

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_fk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
$MPRdetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_defination_id_fk);

$BPRDetails = getCommonFunctions::get_BPR_Record_Details($bprid);

$cFlag = ''; $pFlag = ''; $dFlag = '';
$cTitle = ''; $pTitle= ''; $dTitle = '';
if(date("Y-m-d", strtotime($eqpDet->caliberation_due_date)) < date("Y-m-d"))
{
	$cFlag = 'error';
	$cTitle = 'Calibration due date expired';
}
 
if(date("Y-m-d",strtotime($eqpDet->preventive_m_due_date))<date("Y-m-d")) 
{
	$pFlag = 'error';
	$pTitle = 'Preventive maintenance due date expired';
}
	
if($eqpDet->isDeleted=='1')
{
	$dFlag = "error";
	$dTitle = 'This equipment is deleted';
}

$reasonVisible = false;
$reasonText = '';
if($model->isDeleted=='1')
{
	$reasonVisible = true;
	$reasonText = wordwrap($model->reasonIsDeleted,16,'<br/>',1);
}
else if($model->isDeleted!='1' && $isMDeleted=='1')
{
	$reasonVisible = true;
	$reasonText = wordwrap($MPRdetails['reasonIsDeleted'],16,'<br/>',1); 
}
else
{
	$reasonVisible = false;
	$reasonText =  '';
}
?>
<div class="equipmentmap-view">
	<?php if($rflag!='Yes' && $isMDeleted!='1' && $model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->equipment_map_id_pk, 'mprid' => $model->mpr_defination_id_fk,'bprid'=>$bprid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->equipment_map_id_pk, 'mprid' => $model->mpr_defination_id_fk,'bprid'=>$bprid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
   <?php } elseif($rflag=='Yes') { ?>
		<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This record is approved. So it is sealed & in readonly mode.
        </div>
	<?php }elseif($isMDeleted=='1'){ ?>
    	<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This record is deleted. So it is sealed & in readonly mode.
        </div>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
		'template' => '<tr><th width="150">{label}</th><td width="400">{value}</td></tr>',
        'attributes' => [
           // 'equipment_map_id_pk',
           // 'mpr_defination_id_fk',
           // 'product_id_fk',
          
			[                      
				'attribute'=>'product_code',
				'label'=>'Product Code',
				'format'=>'raw',
            	'value' => $mprDef->product_code,
        	],
			[                      
				'attribute'=>'equipment_id_fk',
				'label'=>'Equipment Name',
				'format'=>'raw',
            	'value' => "<span class='".$dFlag."' title='".$dTitle."'>".$model->showEquipmentName($model->equipment_id_fk)."</span>",
        	],
			[                      
				'attribute'=>'equipment_id_fk',
				'label'=>'Equipment #',
				'format'=>'raw',
            	'value' => $model->equipment_id_fk,
        	],
			[                      
				'attribute'=>'equipment_model',
				'label'=>'Equipment Model',
				'format'=>'raw',
            	'value' => $model->equipment_model,
        	],
			
			[                      
				'attribute'=>'caliberation_due_date',
				'label' => 'Calibration Due Date',
				'format'=>'raw',
            	'value' => "<span class='".$cFlag."' title='".$cTitle."'>".(($eqpDet->caliberation_due_date && $eqpDet->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($eqpDet->caliberation_due_date)).Yii::$app->params['timezoneVar']:'')."</span>",
			],
           	[                      
				'attribute'=>'preventive_m_due_date',
				'label' => 'Preventive Maintenance Due Date',
				'format'=>'raw',
            	'value' => "<span class='".$pFlag."' title='".$pTitle."'>".(($eqpDet->preventive_m_due_date && $eqpDet->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($eqpDet->preventive_m_due_date)).Yii::$app->params['timezoneVar']:'')."</span>",
			],
			[                      
				'attribute'=>'activity',
				'format'=>'raw',
            	'value' => $model->activity,
        	],
			
			[                      
				'attribute'=>'start_date_time',
				'format'=>'raw',
            	'value' => ($model->start_date_time && $model->start_date_time!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($model->start_date_time)).Yii::$app->params['timezoneVar']:'',
			],
           	[                      
				'attribute'=>'end_date_time',
				'format'=>'raw',
            	'value' => ($model->end_date_time && $model->end_date_time!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($model->end_date_time)).Yii::$app->params['timezoneVar']:'',
			],
			[                      
				'attribute'=>'dept_assigned_to',
				'format'=>'raw',
            	'value' => $model->dept_assigned_to,
        	],
			[                      
				'attribute'=>'cleaning_agent',
				'format'=>'raw',
            	'value' => $model->cleaning_agent,
        	],
			[                      
				'attribute'=>'batch',
				'format'=>'raw',
            	'value' => ($mode=='bpr')?$BPRDetails['batch']:'---',
        	],
			[                      
				'attribute'=>'product_name',
				'format'=>'raw',
            	'value' => $mprDef->product_name,
        	],
			[                      
				'attribute'=>'product_part',
				'format'=>'raw',
            	'value' => $mprDef->product_part,
        	],
			[                      
				'attribute'=>'attachment',
				'format'=>'raw',
            	'value' => $model->showAttachment($model->attachment),
        	],
			[                      
				'attribute'=>'comments',
				'format'=>'raw',
            	'value' => $model->comments,
        	],
			[                      
				'attribute'=>'operator_signature',
				'format'=>'raw',
            	'value' => ($model->operator_signature==1)?"Yes":"No",
        	],
			
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => $reasonText,
				'visible' => $reasonVisible,
        	],
        ],
    ]) ?>

</div>
