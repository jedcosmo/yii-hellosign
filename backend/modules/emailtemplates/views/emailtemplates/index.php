<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\emailtemplates\models\EmailtemplatesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emailtemplates-index">

    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Email Template', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
           [
            'class' => 'yii\grid\CheckboxColumn',
			],
            [
	            'attribute'=>'id',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:70px;'],
	            'value' => 'id',
	        ],

          	[
	            'attribute'=>'subject',
				'format' => 'raw',
	            'contentOptions' =>['style'=>'width:200px;'],
	            'value' => 'subject',
	        ],
           // 'subject',
           // 'message:html',
			[
	            'attribute'=>'message',
				'format' => 'html',
	            'contentOptions' =>['style'=>'width:450px;'],
	            'value' => 'message',
	        ],
            'added_date',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

	<div class="row bg-warning">
    	<div class="col-sm-10" style="padding:10px;">
			<label class="col-sm-2">Mark Selected As:</label>
            <div class="col-sm-2">
            	<select class="form-control" name="actdrdwn" id="actdrdwn">
                	<option value="">--Select--</option>
                    <option value="Delete">Delete</option>
                </select>
            </div>
            <div class="col-sm-2">
            	<button type="button" name="" id="" class="btn btn-primary" onclick="javascript:takeAction('w0');">GO</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
function takeAction(grid_id)
{
	var keys = $('#'+grid_id).yiiGridView('getSelectedRows');
	var tAction = $('#actdrdwn').val();
	if(keys!='')
	{
		$.post('<?= Yii::$app->homeUrl; ?>emailtemplates/emailtemplates/bulkactions',{keylist: keys,tAction:tAction},function(data) {
			window.location.reload();
		   });
	}
	else
	{
		alert('Please select records to perform action');
		return false;
	}
}
</script>
