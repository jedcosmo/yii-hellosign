<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */

$this->title = 'UPDATE COMPANY ADMIN';
$this->params['breadcrumbs'][] = ['label' => 'Company Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update Company Admin';
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