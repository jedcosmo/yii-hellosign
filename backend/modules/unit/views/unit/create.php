<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\unit\models\Unit */

$this->title = 'ADD UNIT';
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New' ;
?>
<div class="unit-create">
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
