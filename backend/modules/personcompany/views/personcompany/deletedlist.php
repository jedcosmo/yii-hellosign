<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\company\models\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED PERSON COMPANIES';
$this->params['breadcrumbs'][] = ['label' => 'Person Companies', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted Person Companies';

$serDeletedDatetime = isset($_REQUEST['deleted_datetime'])?$_REQUEST['deleted_datetime']:'';
?>
<div class="company-index">
	<div class="table-responsive">
    <?= GridView::widget([
		'id' => 'deleteListing',
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

			[
	            'attribute'=>'company_id_pk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:100px;'],
	            'value' => 'company_id_pk',
	        ],
			[
	            'attribute'=>'name',
				'format'=>'html',
	            'contentOptions' =>['style'=>'max-width:350px;word-wrap:break-word;'],
				'value' => 'name',
	        ],
            [
	            'attribute'=>'address1',
				'format'=>'ntext',
	            'contentOptions' =>['style'=>'max-width:300px;word-wrap:break-word;'],
				'value' => 'address1',
	        ],
			[
	            'attribute'=>'reasonIsDeleted',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
	            'value' => function($data){
						return htmlentities($data->reasonIsDeleted);
					},
	        ],
			[
	            'attribute'=>'deleted_datetime',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
	            'value' => function($data){
					if($data->deleted_datetime!='0000-00-00 00:00:00')
						return date("m/d/Y H:i:s",strtotime($data->deleted_datetime)).Yii::$app->params['timezoneVar'];
					else
						return "";
					},
	        ],           
            [
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;&nbsp;{restore}',
				'buttons' => [
					'restore' => function ($url) {
						return Html::a(
							'<span class="glyphicon glyphicon-share-alt"></span>',
							$url, 
							[
								'title' => 'Restore',
								'data-pjax' => '0',
								'data' => [
										'confirm' => 'Are you sure you want to restore this item?',
										'method' => 'post',
									],
							]
						);
					},
				],
			],
        ],
    ]); ?>
    </div>
</div>

<?php echo \Yii::$app->view->renderFile('@app/views/site/_myDateFilterListing.php',['searchModel'=>$searchModel]); ?>

