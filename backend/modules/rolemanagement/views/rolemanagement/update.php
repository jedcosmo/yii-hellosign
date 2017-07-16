<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\rolemanagement\models\Rolemanagement */

$this->title = 'UPDATE ROLE';
$this->params['breadcrumbs'][] = ['label' => 'Role Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Update Role';
?>
<div class="rolemanagement-update">
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
