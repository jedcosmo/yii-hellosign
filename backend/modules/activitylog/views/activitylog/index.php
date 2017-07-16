<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\activitylog\models\ActivityLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'AUDIT LOG';
$this->params['breadcrumbs'][] = 'Audit Log';

$search_by = isset($_GET['search_by'])?$_GET['search_by']:'';
$search_for = isset($_GET['search_for'])?$_GET['search_for']:'';
$from_date = isset($_GET['from_date'])?$_GET['from_date']:'';
$to_date = isset($_GET['to_date'])?$_GET['to_date']:'';
?>
<div class="activity-log-index">
    <?php  echo $this->render('_search', ['model' => $searchModel, 'availableScreens' => $availableScreens, 'availableActions' => $availableActions, 'availablePersons' => $availablePersons]); ?>
	<div class="col-sm-12">&nbsp;</div><br/>
    <div class="clearfix">&nbsp;</div>
    
    <?php if($search_by!='' || $search_for!='' || $from_date!='' || $to_date!=''){ ?>
    <div class="table-responsive">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
       // 'filterModel' => $searchModel,
		'pager' => [
				'firstPageLabel' => '&laquo;',
				'lastPageLabel' => '&raquo;',
				'nextPageLabel' => '&rsaquo;',
				'prevPageLabel' => '&lsaquo;',
				],	
        'columns' => [
            [
				'class' => 'yii\grid\SerialColumn',
				 'contentOptions' =>['style'=>'width:50px;'],
			],
          
		  	[
	            'attribute'=>'userid',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
				'value'=>'userid'
	        ],
			[
	            'attribute'=>'added_date',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => function($data){
					return date("m/d/Y H:i:s",strtotime($data->added_date)).Yii::$app->params['timezoneVar'];
				},
	        ],
			[
	            'attribute'=>'type',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:250px;'],
	            'value' => 'type',
	        ],
			[
	            'attribute'=>'action',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:150px;'],
	            'value' => 'action',
	        ],
			/*[
	            'attribute'=>'message',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:200px;'],
	            'value' => 'message',
	        ],*/
			[
	            'attribute'=>'urltext',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'max-width:200px;word-wrap:break-word;'],
	            'value' => function($data){
					if($data->urltext=='')
					{
						return "Not Applicable";
					}
					elseif($data->action == Yii::$app->params['audit_log_action']['DELETE'] || $data->action == Yii::$app->params['audit_log_action']['RESTORED'])
					{
						if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
							$data->urltext = str_replace("http://","https://",$data->urltext);
						} 
						if(strstr($data->urltext,'al=1')==false)
						{
							if(strstr($data->urltext,"?"))
								$data->urltext = $data->urltext."&al=1";
							else
								$data->urltext = $data->urltext."?al=1";
						}
						return "<a href='".$data->urltext."'>".$data->urltext."</a><br/>Reason : ".$data->message;
					}
					else
					{
						if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
							$data->urltext = str_replace("http://","https://",$data->urltext);
						} 
						if(strstr($data->urltext,'al=1')==false)
						{
							if(strstr($data->urltext,"?"))
								$data->urltext = $data->urltext."&al=1";
							else
								$data->urltext = $data->urltext."?al=1";
						}
						return "<a href='".$data->urltext."'>".$data->urltext."</a>";
					}
				},
	        ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    </div>
	<?php } ?>
</div>
