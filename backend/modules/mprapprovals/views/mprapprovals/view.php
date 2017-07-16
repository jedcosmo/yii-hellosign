<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=mprApprovals';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=mprApprovals';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = 'MPR APPROVAL DETAILS';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'MPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'MPR Approval Details';

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_fk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
$MPRdetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_defination_id_fk);

$apprClass = ''; $apprTitle = '';
$veriClass = ''; $veriTitle = '';
$isApproverDeleted = checkCommonFunctions::check_If_Record_Deleted($model->approval_person_id_fk,'bpr_person','person_id_pk');
if($isApproverDeleted=="Yes")
{
	$apprClass = 'error'; 
	$apprTitle = 'This person is deleted';
}
$isVerifierDeleted = checkCommonFunctions::check_If_Record_Deleted($model->verifier_person_id_fk,'bpr_person','person_id_pk');
if($isVerifierDeleted=="Yes")
{
	$veriClass = 'error'; 
	$veriTitle = 'This person is deleted';
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
<div class="mprapprovals-view">
	<?php if($rflag!='Yes' && $isMDeleted!='1' && $model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->mpr_approval_id_pk,'mprid' => $model->mpr_defination_id_fk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->mpr_approval_id_pk,'mprid' => $model->mpr_defination_id_fk], [
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
        'attributes' => [
           // 'mpr_approval_id_pk',
           
			[                      
				'attribute'=>'approval_person_id_fk',
				'format'=>'raw',
            	'value' => "<span class='".$apprClass."' title='".$apprTitle."'>".$model->showPersonName($model->approval_person_id_fk)."</span>",
        	],
            'approval_job_function',
            'approval_status',
			[                      
				'attribute'=>'approval_datetime',
				'format'=>'raw',
            	'value' => ($model->approval_datetime!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($model->approval_datetime)).Yii::$app->params['timezoneVar']:'',
        	],
			[                      
				'attribute'=>'verifier_person_id_fk',
				'format'=>'raw',
            	'value' => "<span class='".$veriClass."' title='".$veriTitle."'>".$model->showPersonName($model->verifier_person_id_fk)."</span>",
        	],
            'verifier_job_function',
            'verified_status',
            [                      
				'attribute'=>'verified_datetime',
				'format'=>'raw',
            	'value' => ($model->verified_datetime!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($model->verified_datetime)).Yii::$app->params['timezoneVar']:'',
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
