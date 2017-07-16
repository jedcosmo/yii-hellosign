<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\minstructions\models\Minstructions */
$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=manufacturingInst';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=manufacturingInst';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = 'UPDATE MANUFACTURING INSTRUCTION';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Manufacturing Instructions', 'url' => [$backUrl]];
//$this->params['breadcrumbs'][] = ['label' => $model->mi_id_pk, 'url' => ['view', 'id' => $model->mi_id_pk]];
$this->params['breadcrumbs'][] = 'Update Manufacturing Instruction';
?>
<div class="minstructions-update">
	<?php if($rflag=='Yes'){ ?>
    	<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This MPR is approved. So it is sealed & in readonly mode.
        </div>
    <?php } ?>
	<div class="row">
    	<div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
			<?= $this->render('_form', [
                'model' => $model,
				'backUrl' => $backUrl,
            ]) ?>
        </div>
        <div class="col-md-2">&nbsp;</div>
	</div>
</div>
