<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\country\models\Country */

$this->title = 'ADD COUNTRY';
$this->params['breadcrumbs'][] = ['label' => 'Countries', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add Country';
?>
<div class="country-create">
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
