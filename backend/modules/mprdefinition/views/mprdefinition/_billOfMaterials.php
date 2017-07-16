<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\billofmaterial\models\Billofmaterial;
use backend\modules\billofmaterial\models\BillofmaterialSearch;

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
    <a href="<?=Yii::$app->homeUrl.'billofmaterial/billofmaterial/create?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-primary"><i class="fa fa-plus"></i>&nbsp;<span>Add Bill Of Material</span></a>
    &nbsp;&nbsp;
    <a href="<?=Yii::$app->homeUrl.'billofmaterial/billofmaterial/deletedlist?mprid='.$model->mpr_defination_id_pk; ?>" class="btn btn-warning"><i class="fa fa-trash"></i>&nbsp;<span>Deleted Materials</span></a>
</p>
<?php } ?>
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
            'attribute'=>'material_name',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->material_name);
					},
        ],
		[
            'attribute'=>'qty_branch',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
            'value' => 'qty_branch',
        ],
        [
            'attribute'=>'qb_unit_id_fk',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => function($data){
				   $unitClass = ''; $unitTitle = '';
				   $isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($data->qb_unit_id_fk,'bpr_unit','unit_id_pk');
				   if($isUnitDeleted=="Yes")
				   {
						$unitClass = 'error';
						$unitTitle = 'This unit is deleted';
				   }
                   $commonModel = new getCommonFunctions();
                   $unitnm = $commonModel->getFieldNameValue("bpr_unit","name","unit_id_pk",$data->qb_unit_id_fk);
				   return "<span class='".$unitClass."' title='".$unitTitle."'>".$unitnm."</span>";
                },
        ],
        [
            'attribute'=>'composition',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->composition);
					},
        ],
        [
            'attribute'=>'com_unit_id_fk',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => function($data){
				   $unitClass = ''; $unitTitle = '';
				   $isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($data->com_unit_id_fk,'bpr_unit','unit_id_pk');
				   if($isUnitDeleted=="Yes")
				   {
						$unitClass = 'error';
						$unitTitle = 'This unit is deleted';
				   }
                   $commonModel = new getCommonFunctions();
                   $unitnm = $commonModel->getFieldNameValue("bpr_unit","name","unit_id_pk",$data->com_unit_id_fk);
				   return "<span class='".$unitClass."' title='".$unitTitle."'>".$unitnm."</span>";
                },
        ],
		 [
            'attribute'=>'material_test_status',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
            'value' => 'material_test_status'
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
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/view?'.$bomid.'&mprid='.$_GET['id'], 
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
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/update?'.$bomid.'&mprid='.$_GET['id'], 
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
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/delete?'.$bomid.'&mprid='.$_GET['id'], 
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
