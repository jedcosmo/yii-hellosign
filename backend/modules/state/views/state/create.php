<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\state\models\State */

$this->title = 'ADD STATE';
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New';
?>
<div class="state-create">
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
