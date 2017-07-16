<?php

use yii\helpers\Html;
use yii\grid\GridView;

use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\minstructions\models\Minstructions;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\minstructions\models\MinstructionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$gridactTpl = '{view}';

$commonModel = new getCommonFunctions();

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$mprid = (isset($_REQUEST['mprid']))?$_REQUEST['mprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=manufacturingInst';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$mprid.'&tab=manufacturingInst';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "DELETED MANUFACTURING INSTRUCTIONS";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Manufacturing Instructions', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Deleted Manufacturing Instructions';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="minstructions-index">
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
            'attribute'=>'mi_step',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->mi_step);
					},
        ],
        [
            'attribute'=>'mi_action',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
            'value' => function($data){
						return htmlentities($data->mi_action);
					},
        ],
        [
            'attribute'=>'unit_id_fk',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
            'value' => function($data){
                   $unitClass = ''; $unitTitle = '';
				   $isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($data->unit_id_fk,'bpr_unit','unit_id_pk');
				   if($isUnitDeleted=="Yes")
				   {
						$unitClass = 'error';
						$unitTitle = 'This unit is deleted';
				   }
				   $commonModel = new getCommonFunctions();
				   $unitnm = $commonModel->getFieldNameValue("bpr_unit","name","unit_id_pk",$data->unit_id_fk);
				   return "<span class='".$unitClass."' title='".$unitTitle."'>".$unitnm."</span>";
                },
        ],
        [
            'attribute'=>'mi_range',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:70px;word-wrap:break-word;'],
            'value' => 'mi_range'
        ],
        [
            'attribute'=>'target',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:70px;word-wrap:break-word;'],
            'value' => 'target'
        ],
        [
            'attribute'=>'perfomer',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:50px;word-wrap:break-word;'],
            'value' => 'perfomer'
        ],
        [
            'attribute'=>'verifier',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:50px;word-wrap:break-word;'],
            'value' => 'verifier'
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
							Yii::$app->homeUrl.'minstructions/minstructions/view?'.$minstid.'&mprid='.$_GET['mprid'], 
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
							Yii::$app->homeUrl.'minstructions/minstructions/restore?'.$minstid.'&mprid='.$_GET['mprid'], 
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
]); ?>
</div>
</div>
<?php echo \Yii::$app->view->renderFile('@app/views/site/_myDateFilterListing.php',['searchModel'=>$searchModel]); ?>
