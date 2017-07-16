<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\Billofmaterial */

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=billofmaterials';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=billofmaterials';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = 'UPDATE BILL OF MATERIAL';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Bill Of Materials', 'url' => [$backUrl]];
//$this->params['breadcrumbs'][] = ['label' => $model->bom_id_pk, 'url' => ['view', 'id' => $model->bom_id_pk,'mprid'=>$model->mpr_defination_id_fk,'bprid'=>$bprid]];
$this->params['breadcrumbs'][] = 'Update Bill Of Material';
?>
<div class="billofmaterial-update">
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
				'mprDef' => $mprDef,
            ]) ?>
        </div>
        <div class="col-md-2">&nbsp;</div>
	</div>
</div>
