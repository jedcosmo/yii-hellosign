<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\company\models\Company */

$this->title = 'ADD NEW COMPANY';
$this->params['breadcrumbs'][] = ['label' => 'Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = "Add New Company";
?>
<div class="company-create">
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
