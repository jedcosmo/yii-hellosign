<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\bprrecords\models\Bprrecords */

$this->title = 'ADD BATCH PRODUCTION RECORD';
$this->params['breadcrumbs'][] = ['label' => 'BPR Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add Batch Production Record';
?>
<div class="bprrecords-create">
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
