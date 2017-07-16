<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */

$this->title = 'UPDATE PERSON: ' . ' ' . $model->person_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Person', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->person_id_pk, 'url' => ['view', 'id' => $model->person_id_pk]];
$this->params['breadcrumbs'][] = 'UPDATE PERSON';
?>
<div class="person-update">
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
