<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\billofmaterial\models\Billofmaterial;
use backend\modules\billofmaterial\models\BillofmaterialSearch;

$rflag = "Yes";

if($rflag=="Yes")
	$gridactTpl = '{view}';
else
	$gridactTpl = '{view}&nbsp;{update}&nbsp;{delete}';

$commonModel = new getCommonFunctions();
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;

?>
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
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->material_name);
					},
        ],
        'qty_branch',
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
            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word;'],
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
            'class' => 'yii\grid\ActionColumn',
            'template' => $gridactTpl,
			'visible' => $visibleFlag,
            'buttons' => [
                'view' => function ($url) {
                    $bomid=  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/view?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr', 
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
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/update?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr', 
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
                        Yii::$app->homeUrl.'billofmaterial/billofmaterial/delete?'.$bomid.'&bprid='.$_GET['id'].'&mode=bpr', 
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
