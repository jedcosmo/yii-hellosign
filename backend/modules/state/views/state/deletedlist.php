<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\state\models\State;
use backend\modules\state\models\StateSearch;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\state\models\StateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED STATES';
$this->params['breadcrumbs'][] = ['label' => 'States', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted States';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="state-index">
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
	            'attribute'=>'state_id_pk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:100px;'],
	            'value' => 'state_id_pk',
	        ],*/
			[
	            'attribute'=>'name',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:400px;'],
	            'value' => function($data){
                        return htmlentities($data->name);
                    },
	        ],
			[
				'attribute'=>'country_id_fk',
                'format' => 'raw',
	            'contentOptions' =>['style'=>'width:400px;'],
            	'value' => function($data){
						$modelTem = new State();
						return $modelTem->showCountryName($data->country_id_fk);
					}
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
