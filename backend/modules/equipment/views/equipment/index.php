<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\equipment\models\Equipment;
use backend\modules\equipment\models\EquipmentSearch;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\equipment\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'EQUIPMENTS';
$this->params['breadcrumbs'][] = 'Equipments';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<style type="text/css">
.myTab{ padding:10px;}
#custMyTab{  border-bottom:2px solid #367fa9; }
#custMyTab li a { color:#FFFFFF; border-color: #367fa9; background-color:#868686; }
#custMyTab li.active a {color:#FFFFFF; border-bottom-color: transparent;background-color:#0D69A3; }
.cust-my-tab .tab-pane{ border:0;/*border:solid 1px #367fa9;  border-top: 0;*/}
</style>

<div class="equipment-index">
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
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted Equipments</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
    </p>
    <?php } ?>
    <ul id="custMyTab" class="nav nav-tabs">
      <li <?php if($activeTab=='Active'){ ?> class="active" <?php } ?>><a data-toggle="tab" href="#activeEqp" onclick="javascript:changeTabs('Active');">Active</a></li>
      <li <?php if($activeTab=='Expired'){ ?> class="active" <?php } ?>><a data-toggle="tab" href="#expiredEqp" onclick="javascript:changeTabs('Expired');">Expired</a></li>
    </ul>
  
  	<div class="tab-content cust-my-tab">
      <div id="activeEqp" class="tab-pane fade <?php if($activeTab=='Active'){ ?> in active <?php } ?>">
      	<div class="myTab">
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
						'attribute'=>'equipment_id_pk',
						'format' => 'raw',
						'contentOptions' =>['style'=>'width:70px;'],
						'value' => 'equipment_id_pk',
					],
					[
						'attribute'=>'name',
						'format' => 'html',
						'contentOptions' =>['style'=>'width:200px;'],
						'value' => function($data){
								return wordwrap(htmlentities($data->name),15,'<br/>',1);
							},
					],
					[
						'attribute'=>'model',
						'format' => 'html',
						'contentOptions' =>['style'=>'width:70px;'],
						'value' => function($data){
								return wordwrap(htmlentities($data->model),10,'<br/>',1);
							},
					],
					[
						'attribute'=>'serial',
						'format' => 'html',
						'contentOptions' =>['style'=>'width:70px;'],
						'value' => function($data){
								return wordwrap(htmlentities($data->serial),10,'<br/>',1);
							},
					],
					[                      
						'attribute'=>'caliberation_due_date',
						'format'=>'raw',
						'contentOptions' =>['style'=>'width:100px;'],
						'value' => function($data){
							return ($data->caliberation_due_date && $data->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->caliberation_due_date)).Yii::$app->params['timezoneVar']:'';
							},
					],
					[                      
						'attribute'=>'preventive_m_due_date',
						'format'=>'raw',
						'contentOptions' =>['style'=>'width:100px;'],
						'value' => function($data){
							return ($data->preventive_m_due_date && $data->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->preventive_m_due_date)).Yii::$app->params['timezoneVar']:'';
							},
					],
					[
						'attribute'=>'document_id_fk',
						'format' => 'raw',
						'contentOptions' =>['style'=>'width:100px;'],
						'value'=>function($data){
							if($data->document_id_fk>0)
							{
								$modelTem = new Equipment();
								$docname = $modelTem->getEquipmentDocument($data->document_id_fk);
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
				'rowOptions'=>function ($model, $key, $index, $grid){
							$class = '';
							if(date("Y-m-d", strtotime($model->caliberation_due_date)) < date("Y-m-d") || date("Y-m-d",strtotime($model->preventive_m_due_date))<date("Y-m-d"))
								$class = 'error';
							return array('key'=>$key,'index'=>$index,'class'=>$class);
						},
			]); ?>
            </div>
          </div>
      </div>
      <div id="expiredEqp" class="tab-pane fade <?php if($activeTab=='Expired'){ ?> in active <?php } ?>">
        	<div class="myTab">
            	<div class="table-responsive">
            	<?= GridView::widget([
					'id' => 'deleteListingExp',
					'dataProvider' => $dataProviderExp,
					'filterModel' => $searchModelExp,
					'pager' => [
					'firstPageLabel' => '&laquo;',
					'lastPageLabel' => '&raquo;',
					'nextPageLabel' => '&rsaquo;',
					'prevPageLabel' => '&lsaquo;',
					],	
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],
			
						[
							'attribute'=>'equipment_id_pk',
							'format' => 'raw',
							'contentOptions' =>['style'=>'width:70px;'],
							'value' => 'equipment_id_pk',
						],
						[
							'attribute'=>'name',
							'format' => 'html',
							'contentOptions' =>['style'=>'width:200px;'],
							'value' => function($data){
									return wordwrap($data->name,15,'<br/>',1);
								},
						],
						[
							'attribute'=>'model',
							'format' => 'html',
							'contentOptions' =>['style'=>'width:70px;'],
							'value' => function($data){
									return wordwrap($data->model,10,'<br/>',1);
								},
						],
						[
							'attribute'=>'serial',
							'format' => 'html',
							'contentOptions' =>['style'=>'width:70px;'],
							'value' => function($data){
									return wordwrap($data->serial,10,'<br/>',1);
								},
						],
						[                      
							'attribute'=>'caliberation_due_date',
							'format'=>'raw',
							'contentOptions' =>['style'=>'width:100px;'],
							'value' => function($data){
								return ($data->caliberation_due_date && $data->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->caliberation_due_date)).Yii::$app->params['timezoneVar']:'';
								},
						],
						[                      
							'attribute'=>'preventive_m_due_date',
							'format'=>'raw',
							'contentOptions' =>['style'=>'width:100px;'],
							'value' => function($data){
								return ($data->preventive_m_due_date && $data->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->preventive_m_due_date)).Yii::$app->params['timezoneVar']:'';
								},
						],
						[
							'attribute'=>'document_id_fk',
							'format' => 'raw',
							'contentOptions' =>['style'=>'width:100px;'],
							'value'=>function($data){
								if($data->document_id_fk>0)
								{
									$modelTem = new Equipment();
									$docname = $modelTem->getEquipmentDocument($data->document_id_fk);
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
					'rowOptions'=>function ($model, $key, $index, $grid){
								$class = '';
								if(date("Y-m-d", strtotime($model->caliberation_due_date)) < date("Y-m-d") || date("Y-m-d",strtotime($model->preventive_m_due_date))<date("Y-m-d"))
									$class = 'error';
								return array('key'=>$key,'index'=>$index,'class'=>$class);
							},
				]); ?>
                </div>
            </div>
      </div>
    </div>

</div>
<?php $this->registerJsFile(Yii::$app->homeUrl.'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
<script type="text/javascript">
function changeTabs(tabname)
{
	var al = '<?=@$_GET['al'];?>';
	var str1 = '';
	if(al==1)
		str1 = "&al=1";
	window.location.href = '<?=Yii::$app->homeUrl;?>equipment/equipment/index?tab='+tabname+str1;
}
</script>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

if($activeTab=='Active'){
	$searchClassNm = $searchModel->formName();
	$gridName = 'deleteListing';
}
else{
	$searchClassNm = $searchModelExp->formName();
	$gridName = 'deleteListingExp';
}
$objName1 = $searchClassNm."[caliberation_due_date]";
$objName2 = $searchClassNm."[preventive_m_due_date]";
$this->registerJs("$(function() {
	var myDtObj1 = document.getElementsByName('".$objName1."');
	$(myDtObj1).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#".$gridName."').yiiGridView('applyFilter');
		},
	});
	
	var myDtObj2 = document.getElementsByName('".$objName2."');
	$(myDtObj2).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#".$gridName."').yiiGridView('applyFilter');
		},
	});

});");
?>
<style>
.ui-daterangepicker-arrows input.ui-rangepicker-input{ height:1.8em!important; }
.ui-daterangepicker-arrows .ui-daterangepicker-prev{ top:8px; }
.ui-daterangepicker-arrows .ui-daterangepicker-next{ top:8px; }
</style>
