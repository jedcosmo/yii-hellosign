<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\product\models\Product */

$this->title = 'UPDATE PRODUCT: ' . ' ' . $model->product_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->product_id_pk]];
$this->params['breadcrumbs'][] = 'Update Product';
?>
<div class="product-update">
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
