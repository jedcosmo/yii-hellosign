<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\product\models\Product;
use backend\modules\product\models\ProductSearch;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\product\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED PRODUCTS';
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted Products';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;

?>
<div class="product-index">
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
	            'attribute'=>'part',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->part);
					},
	        ],
			[
	            'attribute'=>'name',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word'],
	            'value' => function($data){
						return htmlentities($data->name);
					},
	        ],
			[
	            'attribute'=>'code',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:120px;word-wrap:break-word;'],
	            'value' => function($data){
						return htmlentities($data->code);
					},
	        ],
            [
	            'attribute'=>'company_id_fk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
	            'value' => function($data){
						$class = '';$titletxt = '';
						$cflag = checkCommonFunctions::check_If_Record_Deleted($data->company_id_fk,'bpr_company','company_id_pk');
						if($cflag=="Yes")
						{
							$class = 'error';
							$titletxt = 'This company is deleted.';
						}
						$modelTem = new Product();
						$coompnm = $modelTem->showCompanyName($data->company_id_fk);
						return "<span class='".$class."' title='".$titletxt."'>".$coompnm."</span>";
					},
	        ],
			[
	            'attribute'=>'unit_id_fk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
	            'value' => function($data){
						$class = '';$titletxt = '';
						$uflag = checkCommonFunctions::check_If_Record_Deleted($data->unit_id_fk,'bpr_unit','unit_id_pk');
						if($uflag=="Yes")
						{
							$class = 'error';
							$titletxt = 'This unit is deleted.';
						}
						$modelTem = new Product();
						$unitnm = $modelTem->showUnitName($data->unit_id_fk);
						return "<span class='".$class."' title='".$titletxt."'>".$unitnm."</span>";
					},
	        ],
			[
				'attribute'=>'document_id_fk',
				'format' => 'raw',
				'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
                'value'=>function($data){
					if($data->document_id_fk>0)
					{
						$modelTem = new Product();
						$docname = $modelTem->getProductDocument($data->document_id_fk);
						return "<a href='".Yii::$app->urlManager->baseUrl.'/uploads/documents/' .$docname."' target='_blank' title='View symbol'><span class='glyphicon glyphicon-file'></span>&nbsp; View Document</a>";
					}
					else
						return '';
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
