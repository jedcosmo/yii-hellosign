<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\sitesetting\models\SitesettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Site Information';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sitesetting-index">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
			[
	            'attribute'=>'id',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:50px;'],
	            'value' => 'id',
	        ],
            'type',
            'value',

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {update}'],
        ],
    ]); ?>

</div>
