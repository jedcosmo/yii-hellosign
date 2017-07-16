<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
$backUrl = '/bprrecords/bprrecords/view?id='.$_REQUEST['bprid'].'&tab=bprApprovals';
$listingPg = '/bprrecords/bprrecords';
$bclabel = 'BPR Records';

$this->title = 'ADD EXECUTED BPR APPROVAL';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Executed BPR Approvals', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Add BPR Approval';
?>
<div class="mprapprovals-create">
	<?php if($rflag=='Yes'){ ?>
    	<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This BPR is approved. So it is sealed & in readonly mode.
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
