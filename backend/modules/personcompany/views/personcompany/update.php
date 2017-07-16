<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\company\models\Company */

$this->title = 'UPDATE PERSON COMPANY: ' . ' ' . $model->company_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Person Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->company_id_pk, 'url' => ['view', 'id' => $model->company_id_pk]];
$this->params['breadcrumbs'][] = 'Update Person Company';
?>
<div class="company-update">
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
