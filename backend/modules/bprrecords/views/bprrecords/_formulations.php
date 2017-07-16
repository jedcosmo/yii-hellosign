<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\formulation\models\Formulation;
use backend\modules\formulation\models\FormulationSearch;
use yii\data\Pagination;

$rflag = "Yes";

if($rflag=="Yes")
	$gridactTpl = '{view}';
else
	$gridactTpl = '{view}&nbsp;{update}&nbsp;{delete}';

$commonModel = new getCommonFunctions();
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;

/******Code to hide/show formulation % total******************************************/
$pgModel = new Pagination();
$page = isset($_REQUEST['page'])?$_REQUEST['page']:'';
$pgModel->setPage($page, false);
$currentPage = $pgModel->getPage();

$pageSize = $formuladataProvider->pagination->getPageSize();
$totalCount = $formuladataProvider->totalCount;
$totalPages = 0;

if ($pageSize < 1) {
	$totalPages = $totalCount > 0 ? 1 : 0;
} else {
	$totalCount = $totalCount < 0 ? 0 : (int) $totalCount;
	$totalPages = (int) (($totalCount + $pageSize - 1) / $pageSize);
}

$showFooter = FALSE;
if($totalPages==$currentPage && $currentPage)
	$showFooter = TRUE;
elseif($totalPages==1)
	$showFooter = TRUE;
else
	$showFooter = FALSE;
/*********************************************************************************/
?>
	
<div class="table-responsive">
<?= GridView::widget([
    'dataProvider' => $formuladataProvider,
    'filterModel' => $formulasearchModel,
    'pager' => [
        'firstPageLabel' => '&laquo;',
        'lastPageLabel' => '&raquo;',
        'nextPageLabel' => '&rsaquo;',
        'prevPageLabel' => '&lsaquo;',
        ],
	'showFooter'=>$showFooter,
	'footerRowOptions'=>['style'=>'font-weight:bold;'],	
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
		[
            'attribute'=>'material_part',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
            'value' => 'material_part',
        ],
        [
            'attribute'=>'material_name',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->material_name);
					},
			'footerOptions' =>['style'=>'text-align:right;'],
			'footer'=>' Total w/w:',
        ],
        [
            'attribute'=>'formulation_percentage',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
			'value' => function($data){
						return htmlentities($data->formulation_percentage).'  %';
					},
			'footer'=>Formulation::fPercentageTotal($formuladataProvider->models,'formulation_percentage',$model->mpr_definition_id_fk),
        ],
     
	 	[
            'attribute'=>'weight_by_weight',
            'format' => 'raw',
			'headerOptions' => ['style'=>'color:#3c8dbc;'],
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
            'value' => function($data){
						$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($data->mpr_defination_id_fk);
						if(is_array($MPRDetails) && count($MPRDetails)>0)
						{
							$unitnm = getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']);
							$batchSizeInKG = CommonFunctions::unitConversionToKG($unitnm,	$MPRDetails['batch_size']);
						}
						$kgWeight = (($batchSizeInKG*floatval($data->formulation_percentage))/100);
						$kgWeight = round($kgWeight,2);
						
						$gmWeight = ((($batchSizeInKG*floatval($data->formulation_percentage))/100)*1000);
						$gmWeight = round($gmWeight,2);
						
						if(floatval($data->formulation_percentage)==0)
						{
							return '--';
						}
						elseif(floatval($data->formulation_percentage)<=0.01)
						{
							return $gmWeight.'  G';
						}
						else
						{
							return $kgWeight.'  KG';
						}
					},
			 'footer'=>Formulation::kgWeightTotal($formuladataProvider->models,'weight_by_weight',$model->mpr_definition_id_fk),
        ],
		
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => $gridactTpl,
			'visible' => $visibleFlag,
            'buttons' => [
                'view' => function ($url) {
                    $bomid=  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        Yii::$app->homeUrl.'formulation/formulation/view?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr', 
                        [
                            'title' => 'View',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'update' => function ($url) {
                    $bomid=  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-pencil"></span>',
                        Yii::$app->homeUrl.'formulation/formulation/update?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr', 
                        [
                            'title' => 'Update',
                            'data-pjax' => '0',
                        ]
                    );
                },
                'delete' => function ($url) {
                    $bomid=  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-trash"></span>',
                        Yii::$app->homeUrl.'formulation/formulation/delete?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr',
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
]); ?>
</div>
