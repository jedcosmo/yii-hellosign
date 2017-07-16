<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\rolemanagement\models\RolemanagementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ROLE MANAGEMENT';
$this->params['breadcrumbs'][] = 'Role Management';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="rolemanagement-index">

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
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted Roles</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
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

            'name',
            [
				'attribute' => 'modules',
				'format' => 'raw',
				'value' => function($data){
					$retStr = CommonFunctions::display_all_role_modules($data->modules);
					return $retStr;
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
							if($data->is_administrator!=1)
							{
								return Html::a(
									'<span class="glyphicon glyphicon-pencil"></span>',
									$url, 
									[
										'title' => 'Update',
									]
								);
							}else{ return ""; }
						}
					},
					'delete' => function ($url,$data) {
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							return "";
						}
						else
						{
							if($data->is_administrator!=1)
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
							}else{ return ""; }
						}
					},
				],
			],
        ],
    ]); ?>
	</div>
</div>
<?php $this->registerJsFile(Yii::$app->homeUrl.'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
