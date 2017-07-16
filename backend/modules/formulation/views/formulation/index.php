<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\formulation\models\Formulation;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\billofmaterial\models\BillofmaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'FORMULATION';
$this->params['breadcrumbs'][] = "Formulation";
?>
<div class="billofmaterial-index">

	<p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add Formulation</span>', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
			'material_part',
            'material_name',
			'formulation_percentage',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
