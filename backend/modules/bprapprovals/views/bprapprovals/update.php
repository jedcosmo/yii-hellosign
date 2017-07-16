<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
$backUrl = '/bprrecords/bprrecords/view?id='.$model->bpr_id_fk.'&tab=bprApprovals';
$listingPg = '/bprrecords/bprrecords';
$bclabel = 'BPR Records';

$this->title = 'UPDATE BPR APPROVAL';
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Executed BPR Approvals', 'url' => [$backUrl]];
//$this->params['breadcrumbs'][] = ['label' => $model->bpr_approval_id_pk, 'url' => ['view', 'id' => $model->bpr_approval_id_pk]];
$this->params['breadcrumbs'][] = 'Update BPR Approval';
?>
<div class="mprapprovals-update">
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
