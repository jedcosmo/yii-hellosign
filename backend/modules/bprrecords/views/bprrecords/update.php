<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\bprrecords\models\Bprrecords */

$this->title = 'UPDATE BATCH PRODUCTION RECORD: ' . ' ' . $model->bpr_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'BPR Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bpr_id_pk, 'url' => ['view', 'id' => $model->bpr_id_pk]];
$this->params['breadcrumbs'][] = 'Update Batch Production Record';
?>
<div class="bprrecords-update">
	<div class="row">
    	<div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
			<?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-md-2">&nbsp;</div>
	</div>
</div>
