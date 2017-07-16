<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\mprdefinition\models\Mprdefination */

$this->title = 'ADD MPR DEFINITION';
$this->params['breadcrumbs'][] = ['label' => 'MPR Definitions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New';
?>
<div class="mprdefination-create">
	<div class="row">
    	<div class="col-md-2">&nbsp;</div>
        <div class="col-md-8">
			<?= $this->render('_form', [
                'model' => $model,
				'mytoken' => $mytoken,
            ]) ?>
        </div>
        <div class="col-md-2">&nbsp;</div>
	</div>
</div>
