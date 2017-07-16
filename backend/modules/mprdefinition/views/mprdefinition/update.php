<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprdefinition\models\Mprdefination */

$this->title = 'UPDATE MPR DEFINITION: ' . ' ' . $model->mpr_defination_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'MPR Definitions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mpr_defination_id_pk, 'url' => ['view', 'id' => $model->mpr_defination_id_pk]];
$this->params['breadcrumbs'][] = 'Update MPR Definition';
?>
<div class="mprdefination-update">
    <div class="row">
    	<div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
			<?= $this->render('_form', [
                'model' => $model,
				'mytoken' => $mytoken,
            ]) ?>
        </div>
        <div class="col-md-2">&nbsp;</div>
	</div>
</div>

