<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */

$this->title = 'ADD NEW PERSON';
$this->params['breadcrumbs'][] = ['label' => 'Person', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New Person';
?>
<div class="person-create">
	<div class="row">
    	<div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-8">
			<?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
        <div class="col-sm-2">&nbsp;</div>
	</div>
</div>
