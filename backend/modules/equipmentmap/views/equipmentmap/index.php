<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\equipmentmap\models\EquipmentmapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Equipmentmaps';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipmentmap-index">

    <p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add Equipment</span>', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'equipment_map_id_pk',
            // 'mpr_defination_id_fk',
            // 'product_id_fk',
            'product_code',
            'equipment_id_fk',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
