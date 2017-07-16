<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\equipment\models\Equipment */

$this->title = 'ADD EQUIPMENT';
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New Equipment';
?>
<div class="equipment-create">
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
