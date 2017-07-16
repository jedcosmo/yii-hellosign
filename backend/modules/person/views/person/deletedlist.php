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

$this->title = 'DELETED PERSONS';
$this->params['breadcrumbs'][] = ['label' => 'Person', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Deleted Persons';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;
?>
<div class="person-index">
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
	            'attribute'=>'super_company_id_fk',
				'format'=>'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
						$companyName = getCommonFunctions::getFieldNameValue("bpr_person_company","name","company_id_pk",$data->super_company_id_fk);
						return wordwrap(htmlentities($companyName),16,'<br/>',1);
					},
				'visible' => false,
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
					'restore' => function ($url,$data) {
						if(checkCommonFunctions::check_if_role_is_administrator($data->role_id_fk)!='Yes')
						{
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
						}else{ return ""; }
					},
				],
			],
        ],
    ]); ?>
	</div>
</div>
<?php echo \Yii::$app->view->renderFile('@app/views/site/_myDateFilterListing.php',['searchModel'=>$searchModel]); ?>
