<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\equipmentmap\models\Equipmentmap */
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
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$_REQUEST['mprid'].'&tab=equipments';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = 'ADD EQUIPMENT';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = "Add Equipment";
?>
<div class="equipmentmap-create">
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
