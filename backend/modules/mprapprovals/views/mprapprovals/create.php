<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
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
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$_REQUEST['mprid'].'&tab=mprApprovals';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = 'ADD MPR APPROVAL';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'MPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Add MPR Approval';
?>
<div class="mprapprovals-create">
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
