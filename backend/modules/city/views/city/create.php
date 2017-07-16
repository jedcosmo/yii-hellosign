<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\city\models\City */

$this->title = 'ADD CITY';
$this->params['breadcrumbs'][] = ['label' => 'Cities', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add City';
?>
<div class="city-create">
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
