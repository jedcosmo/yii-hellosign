<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\billofmaterial\models\Billofmaterial;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\billofmaterial\models\BillofmaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'BILL OF MATERIALS';
$this->params['breadcrumbs'][] = "Bill Of Materials";
?>
<div class="billofmaterial-index">

	<p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add Bill Of Material</span>', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'material_name',
            'qty_branch',
			[
	            'attribute'=>'qb_unit_id_fk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
	            'value' => function($data){
						$modelTem = new Billofmaterial();
						return $unitnm = $modelTem->showUnitName($data->qb_unit_id_fk);
					},
	        ],
            'composition',
  
          	[
	            'attribute'=>'com_unit_id_fk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
	            'value' => function($data){
						$modelTem = new Billofmaterial();
						return $unitnm = $modelTem->showUnitName($data->com_unit_id_fk);
					},
	        ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
