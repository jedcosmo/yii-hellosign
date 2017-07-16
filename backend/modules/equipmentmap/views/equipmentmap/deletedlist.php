<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\equipmentmap\models\EquipmentmapSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$mprid = (isset($_REQUEST['mprid']))?$_REQUEST['mprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=equipments';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$mprid.'&tab=equipments';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "DELETED EQUIPMENTS";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = "Deleted Equipments";

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="equipmentmap-index">
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
            'attribute'=>'equipment_id_fk',
            'label' => 'Equipment #',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:300px;word-wrap:break-word;'],
            'value' => 'equipment_id_fk'
        ],
        [
            'attribute'=>'eqp_name',
            'headerOptions' => ['style'=>'color:#3c8dbc;cursor:pointer;'],
            'label' => 'Equipment Name',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:300px;word-wrap:break-word;'],
            'value' => function($data){
                    $getCommonFunctions = new getCommonFunctions();
                    return $unitnm = $getCommonFunctions->getFieldNameValue("bpr_equipment","name","equipment_id_pk",$data->equipment_id_fk);
                },
        ],
		[                      
			'attribute'=>'caliberation_due_date',
			'format'=>'raw',
			'label' => 'Calibration Due Date',
			'contentOptions' =>['style'=>'width:100px;'],
			'value' => function($data){
				return ($data->equipment->caliberation_due_date && $data->equipment->caliberation_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->equipment->caliberation_due_date)).Yii::$app->params['timezoneVar']:'';
				},
		],
		[                      
			'attribute'=>'preventive_m_due_date',
			'format'=>'raw',
			'label' => 'Preventive Maintenance Due Date',
			'contentOptions' =>['style'=>'width:100px;'],
			'value' => function($data){
				return ($data->equipment->preventive_m_due_date && $data->equipment->preventive_m_due_date!='0000-00-00')?date("m/d/Y",strtotime($data->equipment->preventive_m_due_date)).Yii::$app->params['timezoneVar']:'';
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
					'view' => function ($url) {
						$minstid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-eye-open"></span>',
							Yii::$app->homeUrl.'equipmentmap/equipmentmap/view?'.$minstid.'&mprid='.$_GET['mprid'], 
							[
								'title' => 'View',
								'data-pjax' => '0',
							]
						);
					},
					'restore' => function ($url) {
						$minstid=  parse_url($url,PHP_URL_QUERY);
						return Html::a(
							'<span class="glyphicon glyphicon-share-alt"></span>',
							Yii::$app->homeUrl.'equipmentmap/equipmentmap/restore?'.$minstid.'&mprid='.$_GET['mprid'], 
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
                    if(date("Y-m-d", strtotime($model->equipment->caliberation_due_date)) < date("Y-m-d") || date("Y-m-d",strtotime($model->equipment->preventive_m_due_date))<date("Y-m-d") || $model->equipment->isDeleted=='1')
                        $class = 'error';
                    return array('key'=>$key,'index'=>$index,'class'=>$class);
                },
]); ?>
 </div> 
    
</div>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$searchClassNm = $searchModel->formName();
$gridName = 'deleteListing';

$objName1 = $searchClassNm."[caliberation_due_date]";
$objName2 = $searchClassNm."[preventive_m_due_date]";
$objName3 = $searchClassNm."[deleted_datetime]";
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
	
	var myDtObj3 = document.getElementsByName('".$objName3."');
	$(myDtObj3).daterangepicker({
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
