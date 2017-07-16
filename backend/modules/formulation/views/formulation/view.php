<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\Billofmaterial */
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=formulation';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=formulation';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "FORMULATION DETAILS";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Formulations', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = "Formulation Details";

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_fk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
$MPRdetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_defination_id_fk);

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
<div class="billofmaterial-view">

	<?php if($rflag!='Yes' && $isMDeleted!='1' && $model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->f_id_pk, 'mprid' => $model->mpr_defination_id_fk, 'bprid' => $bprid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->f_id_pk, 'mprid' => $model->mpr_defination_id_fk, 'bprid' => $bprid], [
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
			'material_part',
            'material_name',
			'formulation_percentage',
			[
				'attribute' => 'weight_by_weight',
				'format' => 'raw',
				'value' =>$model->showWbyW($model),
			],
			[
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
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
