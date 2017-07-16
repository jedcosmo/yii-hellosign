<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\adminUser\models\Admin */

$this->title = 'Admin Profile';
$this->params['breadcrumbs'][] = ['label' => 'Admin', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view">

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'firstname',
            'lastname',
        
			[
				'attribute'=>'image',
                'value'=>'@web/uploads/' .$model->image,
                'format' => ['image',['width'=>'100','height'=>'100']],
			],
            'email:email',
        ],
    ]) ?>

</div>
