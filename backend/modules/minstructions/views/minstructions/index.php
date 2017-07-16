<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\minstructions\models\MinstructionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MANUFACTURING INSTRUCTIONS';
$this->params['breadcrumbs'][] = 'Manufacturing Instructions';
?>
<div class="minstructions-index">

    <p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add Instruction</span>', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'pager' => [
						'firstPageLabel' => '&laquo;',
						'lastPageLabel' => '&raquo;',
						'nextPageLabel' => '&rsaquo;',
						'prevPageLabel' => '&lsaquo;',
						],	
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'mi_id_pk',
            'mi_step',
            'mi_action',
            'unit_id_fk',
            'mi_range',
            'target',
            'perfomer',
            'verifier',
            // 'document_id_fk',


            [				
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;{update}&nbsp;{delete}',
				'buttons' => [
					'view' => function ($url) {
						$bomid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-eye-open"></span>',
							Yii::$app->homeUrl.'minstructions/minstructions/view?'.$bomid.'&mprid='.$_GET['id'], 
							[
								'title' => 'View',
								'data-pjax' => '0',
							]
						);
					},
					'update' => function ($url) {
						$bomid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-pencil"></span>',
							Yii::$app->homeUrl.'minstructions/minstructions/update?'.$bomid.'&mprid='.$_GET['id'], 
							[
								'title' => 'Update',
								'data-pjax' => '0',
							]
						);
					},
					'delete' => function ($url) {
						$bomid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-trash"></span>',
							Yii::$app->homeUrl.'minstructions/minstructions/delete?'.$bomid.'&mprid='.$_GET['id'], 
							[
								'title' => 'Delete',
								'data-pjax' => '0',
								'data-method' => 'post',
								'data-confirm' => 'Are you sure you want to delete this item?',
								'aria-label' => 'Delete',
							]
						);
					},
				],
			],
        ],
    ]); ?>

</div>
