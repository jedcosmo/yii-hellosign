<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\country\models\Country */

$this->title = 'UPDATE COUNTRY';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->country_id_pk]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="country-update">
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
