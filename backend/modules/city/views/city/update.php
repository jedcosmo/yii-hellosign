<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\city\models\City */

$this->title = 'UPDATE CITY';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->city_id_pk]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="city-update">
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
