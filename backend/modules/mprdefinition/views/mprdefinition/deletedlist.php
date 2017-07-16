<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mprdefinition\models\MprdefinitionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED MPR DEFINITIONS';
$this->params['breadcrumbs'][] = ['label' => 'MPR Definitions', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted MPR Definitions';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="mprdefination-index">

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
	            'attribute'=>'product_code',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_code);
					},
	        ],
            'MPR_version',
            [
	            'attribute'=>'product_part',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_part);
					},
	        ],
            [
	            'attribute'=>'product_name',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->product_name);
					},
	        ],
			[
	            'attribute'=>'reasonIsDeleted',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
	            'value' => function($data){
						return wordwrap(htmlentities($data->reasonIsDeleted),15,'<br/>',1);
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
				'template' => '{restore}',
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
		'rowOptions'=>function ($model, $key, $index, $grid){
				$class = '';
				$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
				if($rflag=="Yes")
					$class = 'mprApproved';
				return array('key'=>$key,'index'=>$index,'class'=>$class);
		},
    ]); ?>
	</div>
</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/mprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<?php echo \Yii::$app->view->renderFile('@app/views/site/_myDateFilterListing.php',['searchModel'=>$searchModel]); ?>
