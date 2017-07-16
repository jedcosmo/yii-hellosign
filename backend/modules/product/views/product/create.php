<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\product\models\Product */

$this->title = 'ADD NEW PRODUCT';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Add New Product';
?>
<div class="product-create">
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

