<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\equipmentmap\models\Equipmentmap;

$rflag = "Yes";

if($rflag=="Yes")
	$gridactTpl = '{view}';
else
	$gridactTpl = '{view}&nbsp;{update}&nbsp;{delete}';

$commonModel = new getCommonFunctions();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;

$session = Yii::$app->session;
$bprEqpError = $session->get('bprEqpError');
$session->remove('bprEqpError');

?>
<?php if(isset($bprEqpError) && $bprEqpError!=''){ ?>
	<div id="msgE" class="alert alert-danger">
	   <?=$bprEqpError;?>
	</div>
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
            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word;'],
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
			'attribute'=>'operator_signature',
			'format'=>'raw',
			'label' => 'Operator Signature',
			'contentOptions' =>['style'=>'width:200px;'],
			'value' => function($data){
					$bprID = isset($_GET['id'])?$_GET['id']:0;
					$bpr_approved_flag = '';
					if($bprID)
						$bpr_approved_flag = checkCommonFunctions::check_If_BPR_Approved($bprID);

					if($data->operator_signature==0)
					{
						return 'Not required';
					}
					elseif($data->operator_signature==1)
					{
						$bprID = $_GET['id'];
						$getCommonFunctions = new getCommonFunctions();
						$operatorSignatureStatus = $getCommonFunctions->get_BPREQP_operator_signature_status($data->equipment_map_id_pk,$data->mpr_defination_id_fk,$bprID);
						if($operatorSignatureStatus['approved_status']!='Approved' &&$operatorSignatureStatus['HS_signature_id']=='' && $bpr_approved_flag!='Yes')
						{
							return '<button type="button" class="btn btn-sm btn-success" onclick="javascript:equipmentOperatorSignature(\''.$_GET['id'].'\',\''.$data->mpr_defination_id_fk.'\',\''.$data->equipment_map_id_pk.'\');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Sign</button>';
						}
						elseif($operatorSignatureStatus['approved_status']=='Approved' && $operatorSignatureStatus['HS_signature_id']!='')
						{
							$personDetails = getCommonFunctions::getPersonDetails($operatorSignatureStatus['approved_by_person_id_fk']);
							$retStr = '<small><span class="text-muted">'.htmlentities($personDetails['first_name']).' '.htmlentities($personDetails['last_name']).' ( '.htmlentities($personDetails['user_name_person']).' )<br/><span>'.date('m/d/Y H:i:s',strtotime($operatorSignatureStatus['approved_datetime'])).Yii::$app->params['timezoneVar'].'</span></span></small><br/><span>'.getCommonFunctions::getSignedDocFromSignatureId($operatorSignatureStatus['HS_signature_id']).'</span>';
							
							return $retStr;
						}
						else
						{
							return "";
						}
					}
					else
					{
						return "";
					}
				},
		],
       [
            'class' => 'yii\grid\ActionColumn',
            'template' => $gridactTpl,
			'visible' => $visibleFlag,
            'buttons' => [
                'view' => function ($url,$data) {
                    $eqpid =  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/view?'.$eqpid.'&bprid='.$_GET['id'].'&mode=bpr&mprid='.$data->mpr_defination_id_fk, 
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
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/update?'.$eqpid.'&bprid='.$_GET['id'].'&mode=bpr&mprid='.$data->mpr_defination_id_fk, 
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
                        Yii::$app->homeUrl.'equipmentmap/equipmentmap/delete?'.$eqpid.'&bprid='.$_GET['id'].'&mode=bpr&mprid='.$data->mpr_defination_id_fk, 
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
