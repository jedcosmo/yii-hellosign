<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\formulation\models\Formulation;
use backend\modules\formulation\models\FormulationSearch;

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
    <a href="<?=Yii::$app->homeUrl.'formulation/formulation/create?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<span>Add Formulation</span></a>
    &nbsp;&nbsp;
    <a href="<?=Yii::$app->homeUrl.'formulation/formulation/deletedlist?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-warning"><i class="fa fa-trash"></i>&nbsp;<span>Deleted Formulations</span></a>
</p>
<?php } ?>

<?= GridView::widget([
    'dataProvider' => $formuladataProvider,
    'filterModel' => $formulasearchModel,
    'pager' => [
        'firstPageLabel' => '&laquo;',
        'lastPageLabel' => '&raquo;',
        'nextPageLabel' => '&rsaquo;',
        'prevPageLabel' => '&lsaquo;',
        ],	
	'showFooter'=>TRUE,
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
			'footer'=>Formulation::fPercentageTotal($formuladataProvider->models,'formulation_percentage'),
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
						if(floatval($data->formulation_percentage)<=0.01)
						{
							return $gmWeight.'  G';
						}
						else
						{
							return $kgWeight.'  KG';
						}
					},
			 'footer'=>Formulation::kgWeightTotal($formuladataProvider->models,'weight_by_weight'),
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
                        Yii::$app->homeUrl.'formulation/formulation/view?'.$bomid.'&mprid='.$_GET['id'], 
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
                        Yii::$app->homeUrl.'formulation/formulation/update?'.$bomid.'&mprid='.$_GET['id'], 
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
                        Yii::$app->homeUrl.'formulation/formulation/delete?'.$bomid.'&mprid='.$_GET['id'], 
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
