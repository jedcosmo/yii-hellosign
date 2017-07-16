<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\modules\equipment\models\Equipment;
use backend\modules\equipment\models\EquipmentSearch;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\equipment\models\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'DELETED EQUIPMENTS';
$this->params['breadcrumbs'][] = ['label' => 'Equipments', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted Equipments';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>

<div class="equipment-index"> 
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
                'contentOptions' =>['style'=>'width:20px;'],
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
	            'attribute'=>'reasonIsDeleted',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word'],
	            'value' => function($data){
						return wordwrap(htmlentities($data->reasonIsDeleted),15,'<br/>',1);
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
        'rowOptions'=>function ($model, $key, $index, $grid){
                    $class = '';
                    if(date("Y-m-d", strtotime($model->caliberation_due_date)) < date("Y-m-d") || date("Y-m-d",strtotime($model->preventive_m_due_date))<date("Y-m-d"))
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
$objName1 = $searchClassNm."[caliberation_due_date]";
$objName2 = $searchClassNm."[preventive_m_due_date]";
$this->registerJs("$(function() {
	var myDtObj1 = document.getElementsByName('".$objName1."');
	$(myDtObj1).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#deleteListing').yiiGridView('applyFilter');
		},
	});
	
	var myDtObj2 = document.getElementsByName('".$objName2."');
	$(myDtObj2).daterangepicker({
		arrows:true,
		dateFormat: 'yy-mm-dd',
		rangeSplitter: '|',
		onClose:function(){
			$('#deleteListing').yiiGridView('applyFilter');
		},
	});

});");
?>
<style>
.ui-daterangepicker-arrows input.ui-rangepicker-input{ height:1.8em!important; }
.ui-daterangepicker-arrows .ui-daterangepicker-prev{ top:8px; }
.ui-daterangepicker-arrows .ui-daterangepicker-next{ top:8px; }
</style>
