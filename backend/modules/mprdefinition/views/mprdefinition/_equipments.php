<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\equipmentmap\models\Equipmentmap;

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_pk,'bpr_mpr_defination','mpr_defination_id_pk');
if($rflag=="Yes" || $isMDeleted=='1')
	$gridactTpl = '{view}';
else
	$gridactTpl = '{view}&nbsp;{update}&nbsp;{delete}';

$commonModel = new getCommonFunctions();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
	<!-- show success message on screen -->
	<?php $session = Yii::$app->session;
	$successDeleteRestore = $session->get('successDeleteRestore');
	$session->remove('successDeleteRestore');

	if(isset($successDeleteRestore) && $successDeleteRestore!=''){ ?>
		<div id="msgS" class="alert alert-success">
		  <strong>Success!</strong>&nbsp; <?=$successDeleteRestore;?>
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
    
<?php if($rflag!='Yes' && $isMDeleted!='1' && $fromAuditLog!="Yes"){ ?>
<p class="text-right">
    <a href="<?=Yii::$app->homeUrl.'equipmentmap/equipmentmap/create?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<span>Add Equipment</span></a>
     &nbsp;&nbsp;
    <a href="<?=Yii::$app->homeUrl.'equipmentmap/equipmentmap/deletedlist?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-warning"><i class="fa fa-trash"></i>&nbsp;<span>Deleted Equipments</span></a>
</p>
<?php } ?>
<div class="table-responsive">
<?= GridView::widget([
	'id' => 'deleteListing',
    'dataProvider' => $eqpdataProvider,
    'filterModel' => $eqpsearchModel,
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
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
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
            'class' => 'yii\grid\ActionColumn',
            'template' => $gridactTpl,
			'visible' => $visibleFlag,
            'buttons' => [
                'view' => function ($url) {
                    $eqpid =  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/view?'.$eqpid.'&mprid='.$_GET['id'], 
                        [
                            'title' => 'View',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'update' => function ($url) {
                    $eqpid =  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/update?'.$eqpid.'&mprid='.$_GET['id'], 
                        [
                            'title' => 'Update',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'delete' => function ($url) {
                    $eqpid =  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/delete?'.$eqpid.'&mprid='.$_GET['id'], 
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
	'rowOptions'=>function ($model, $key, $index, $grid){
                    $class = '';
                    if(date("Y-m-d", strtotime($model->equipment->caliberation_due_date)) < date("Y-m-d") || date("Y-m-d",strtotime($model->equipment->preventive_m_due_date))<date("Y-m-d") || $model->equipment->isDeleted=='1')
                        $class = 'error';
                    return array('key'=>$key,'index'=>$index,'class'=>$class);
                },
]); ?>
</div>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.css");
$this->registerCssFile(Yii::$app->homeUrl."js/FilamentGroup_daterangepicker/css/ui.daterangepicker.css");

$this->registerJsFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.min.js",['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/date.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/FilamentGroup_daterangepicker/js/daterangepicker.jQuery.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


$searchClassNm = $eqpsearchModel->formName();
$gridName = 'deleteListing';

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
