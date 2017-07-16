<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\rolemanagement\models\Rolemanagement */

$this->title = 'ADD ROLE';
$this->params['breadcrumbs'][] = ['label' => 'Role Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add Role';
?>
<div class="rolemanagement-create">
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

