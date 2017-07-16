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

$visibleFlag = false;
if(Yii::$app->user->identity->is_super_admin==1)
	$visibleFlag = true;
else
	$visibleFlag = false;
	
?>
<div class="person-index">
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
    
	<p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add New</span>', ['create'], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fa fa-download"></i>&nbsp;&nbsp;<span>Export</span>', ['excel?'.$_SERVER['QUERY_STRING']], ['class' => 'btn btn-primary', 'method'=>'get']) ?>
        <?= Html::a('<i class="fa fa-trash"></i>&nbsp;<span>Deleted Persons</span>', ['deletedlist'], ['class' => 'btn btn-warning']) ?>
    </p>
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
						return "<a href='mailto:".$data->emailid."' title='".$data->emailid."'>".wordwrap($data->emailid,25,'<br/>',1)."</a>";
					},
	        ],
			[
	            'attribute'=>'user_name_person',
				'format'=>'html',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						return wordwrap(htmlentities($data->user_name_person),16,'<br/>',1);
					},
	        ],
			[                      
				'attribute'=>'role_id_fk',
				'format'=>'raw',
				'contentOptions' =>['style'=>'width:150px;'],
            	'value' => function($data){
					$model = new Person();
					return $model->showRoleName($data->role_id_fk);
				},
        	],
			[
	            'attribute'=>'super_company_id_fk',
				'format'=>'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						$companyName = getCommonFunctions::getFieldNameValue("bpr_person_company","name","company_id_pk",$data->super_company_id_fk);
						return wordwrap($companyName,16,'<br/>',1);
					},
				'visible' => false,
	        ],

            //['class' => 'yii\grid\ActionColumn'],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{view}&nbsp;{update}&nbsp;{delete}',
				'buttons' => [
					'view' => function ($url,$data) {
						return Html::a(
							'<span class="glyphicon glyphicon-eye-open"></span>',
							$url, 
							[
								'title' => 'View',
							]
						);
					},
					'update' => function ($url,$data) {
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
					},
					'delete' => function ($url,$data) {
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
					},
				],
			],
		
        ],
    ]); ?>

</div>
<?php $this->registerJsFile(Yii::$app->homeUrl.'js/common.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
