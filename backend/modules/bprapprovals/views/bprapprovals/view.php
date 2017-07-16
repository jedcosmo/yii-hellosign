<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
$backUrl = '/bprrecords/bprrecords/view?id='.$model->bpr_id_fk.'&tab=bprApprovals';
$listingPg = '/bprrecords/bprrecords';
$bclabel = 'BPR Records';

$this->title = 'BPR APPROVAL DETAILS';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Excecuted BPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'BPR Approval Details';

$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_fk);
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";


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
?>
<div class="mprapprovals-view">
	<?php if($rflag!='Yes' && $model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->bpr_approval_id_pk,'bprid' => $model->bpr_id_fk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->bpr_approval_id_pk,'bprid' => $model->bpr_id_fk], [
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
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bpr_approval_id_pk',
           
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
            	'value' => (($model->isDeleted) == 1? wordwrap($model->reasonIsDeleted,16,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        	],
        ],
    ]) ?>

</div>
