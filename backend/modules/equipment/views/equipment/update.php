<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipment\models\Equipment */

$this->title = 'UPDATE EQUIPMENT: ' . ' ' . $model->equipment_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->equipment_id_pk, 'url' => ['view', 'id' => $model->equipment_id_pk]];
$this->params['breadcrumbs'][] = 'Update Equipment';
?>
<div class="equipment-update">
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
