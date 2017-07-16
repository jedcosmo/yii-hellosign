<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\unit\models\UnitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED UNITS';
$this->params['breadcrumbs'][] = ['label' => 'Units', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted Units';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="unit-index">
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
			/*[
	            'attribute'=>'unit_id_pk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:60px;'],
	            'value' => 'unit_id_pk',
	        ],*/
          	[
	            'attribute'=>'name',
				'format'=>'html',
	            'contentOptions' =>['style'=>''],
	            'value' => function($data){
						return "<div style='width:100px;'>".htmlentities($data->name)."</div>";
					},
	        ],
			[
	            'attribute'=>'description',
				'format'=>'html',
	            'contentOptions' =>['style'=>''],
	            'value' => function($data){
						return "<div style='width:250px;'>".htmlentities($data->description)."</div>";
					},
	        ],
			[
				'attribute'=>'symbols',
				'format' => 'raw',
                'value'=>function($data){
					if($data->symbols!='')
						return "<a href='".Yii::$app->urlManager->baseUrl.'/uploads/symbols/' .$data->symbols."' target='_blank' title='View symbol'><span class='glyphicon glyphicon-file'></span>&nbsp; View Symbol</a>";
					else
						return '';
				}, 
			],
			[
	            'attribute'=>'reasonIsDeleted',
				'format' => 'raw',
	            'contentOptions' =>['style'=>''],	//max-width:200px;word-wrap:break-word
	            'value' => function($data){
						return "<div style='width:50px;'>".htmlentities($data->reasonIsDeleted)."</div>";
					},
	        ],
			[
	            'attribute'=>'deleted_datetime',
				'format' => 'raw',
	            'contentOptions' =>['style'=>''],	//max-width:200px;width:350px;
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
				'visible' => $visibleFlag,
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
