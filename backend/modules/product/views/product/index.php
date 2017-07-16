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

$this->title = 'PRODUCTS';
$this->params['breadcrumbs'][] = 'Products';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="product-index">
	<!-- show success message on screen -->
	<?php $session = Yii::$app->session;
	$successDeleteRestore = $session->get('successDeleteRestore');
	$lockMsg = $session->get('LockError');
	$session->remove('successDeleteRestore');
	$session->remove('LockError');

	if(isset($successDeleteRestore) && $successDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-success">
		  <strong>Success!</strong>&nbsp; <?=$successDeleteRestore;?>
		</div>
	<?php } 
	 if(isset($lockMsg) && $lockMsg!=''){ ?>
		<div id="msgS" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$lockMsg;?>
		</div>
	<?php } ?>
    <!-- show error message on screen -->
    <?php
	$errorDeleteRestore = $session->get('errorDeleteRestore');
	$session->remove('errorDeleteRestore');

	if(isset($errorDeleteRestore) && $errorDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-danger">
		  <strong>Error!</strong>&nbsp; <?=$errorDeleteRestore;?>
		</div>
	<?php } ?>
    
    <?php if($fromAuditLog!="Yes"){ ?>
	<p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add New</span>', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-download"></i>&nbsp;&nbsp;<span>Export</span>', ['excel?'.$_SERVER['QUERY_STRING']], ['class' => 'btn btn-primary', 'method'=>'get']) ?>
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted Products</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
    </p>
    <? } ?>
 	<div class="table-responsive">
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
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;{update}&nbsp;{delete}',
				'visible' => $visibleFlag,
				'buttons' => [
					'view' => function ($url,$data) {
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							$iconStr = '<span class="text-danger" title="This record is locked, because another user is accessing same"><i class="fa fa-lock"></i></span>';
							$url = 'javascript:void(0);';
						}
						else
							$iconStr = '<span class="glyphicon glyphicon-eye-open"></span>';
						return Html::a(
							$iconStr,
							$url, 
							[
								'title' => 'View',
							]
						);
					},
					'update' => function ($url,$data) {
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							return "";
						}
						else
						{
							return Html::a(
								'<span class="glyphicon glyphicon-pencil"></span>',
								$url, 
								[
									'title' => 'Update',
								]
							);
						}
					},
					'delete' => function ($url,$data) {
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							return "";
						}
						else
						{
							return Html::a(
								'<span class="glyphicon glyphicon-trash"></span>',
								$url, 
								[
									'title' => 'Delete',
									'data-pjax' => '0',
									'data' => [
											'confirm' => 'Are you sure you want to delete this item?',
											'method' => 'post',
										],
								]
							);
						}
					},
				],
			],
        ],
    ]); ?>
	</div>
</div>
<?php $this->registerJsFile(Yii::$app->homeUrl.'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
