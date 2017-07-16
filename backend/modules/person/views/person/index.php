<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\person\models\Person;
/* @var $this yii\web\View */
/* @var $searchModel backend\modules\person\models\PersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PERSON';
$this->params['breadcrumbs'][] = 'Person';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="person-index">
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
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted Persons</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
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
	            'attribute'=>'person_id_pk',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:100px;'],
	            'value' => 'person_id_pk',
	        ],
			[
	            'attribute'=>'first_name',
				'format'=>'html',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						return wordwrap(htmlentities($data->first_name),15,'<br/>',1);
					},
	        ],
			[
	            'attribute'=>'last_name',
				'format'=>'html',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						return wordwrap(htmlentities($data->last_name),15,'<br/>',1);
					},
	        ],
			[
	            'attribute'=>'emailid',
				'format'=>'html',
	            'contentOptions' =>['style'=>'width:200px;'],
	            'value' => function($data){
						$eStr = "<a href='mailto:".$data->emailid."' title='".$data->emailid."'>".wordwrap($data->emailid,25,'<br/>',1)."</a>";
						if($data->new_emailid!='' && $data->is_verified==1)
						{
							$eStr .= "<br/><span class='text-muted small'>[Emailid changed to '".wordwrap($data->new_emailid,25,'<br/>',1)."', Verification pending.]</span>";
						}
						return $eStr;
					},
	        ],
			[
	            'attribute'=>'user_name_person',
				'format'=>'html',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						$uStr = wordwrap(htmlentities($data->user_name_person),16,'<br/>',1);
						if($data->new_username!='' && $data->is_verified==1)
						{
							$uStr .= "<br/><span class='text-muted small'>[Username changed to '".wordwrap($data->new_username,16,'<br/>',1)."', Verification pending.]</span>";
						}
						return $uStr; 
					},
	        ],
			[                      
				'attribute'=>'role_id_fk',
				'format'=>'raw',
				'contentOptions' =>['style'=>'width:150px;'],
            	'value' => function($data){
					$isRoleDeleted = checkCommonFunctions::check_If_Record_Deleted($data->role_id_fk,'bpr_role','role_id_pk');
					$roleClass = '';
					$roleTitle = '';
					if($isRoleDeleted=="Yes")
					{
						$roleClass = 'error';
						$roleTitle = 'This role is deleted';
					}
					$model = new Person();
					$rolenm = $model->showRoleName($data->role_id_fk);
					return "<span class='".$roleClass."' title='".$roleTitle."'>".$rolenm."</span>";
				},
        	],
			[
	            'attribute'=>'super_company_id_fk',
				'format'=>'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						$companyName = getCommonFunctions::getFieldNameValue("bpr_person_company","name","company_id_pk",$data->super_company_id_fk);
						return wordwrap(htmlentities($companyName),16,'<br/>',1);
					},
				'visible' => false,
	        ],

            //['class' => 'yii\grid\ActionColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;{update}&nbsp;{delete}&nbsp;&nbsp;{sendverificationemail}',
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
							if(checkCommonFunctions::check_if_role_is_administrator($data->role_id_fk)!='Yes')
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
							if(checkCommonFunctions::check_if_role_is_administrator($data->role_id_fk)!='Yes')
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
					'sendverificationemail' => function ($url,$data){
						if($data->lock_flag==1 && $data->locked_by!=Yii::$app->user->identity->person_id_pk)
						{
							return "";
						}
						else
						{
							if($data->is_verified==0)
							{
								return Html::a(
									'<span class="glyphicon glyphicon-envelope"></span>',
									'javascript:void(0);',  
									[
										'title' => 'Resend Verification Email',
										'onclick' => 'javascript:$(this).remove();window.location.href=\''.$url.'\';',
									]
								);
							}
							else
							{
								return "";
							}
						}
					},
				],
			],
		
        ],
    ]); ?>
	</div>
</div>
<?php $this->registerJsFile(Yii::$app->homeUrl.'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
