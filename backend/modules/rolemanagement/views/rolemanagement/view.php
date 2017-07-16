<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\rolemanagement\models\Rolemanagement */

$this->title = "ROLE DETAILS";
$this->params['breadcrumbs'][] = ['label' => 'Role Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Role Details';

$retStr = CommonFunctions::display_all_role_modules($model->modules);

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

?>
<div class="rolemanagement-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No" && $model->is_administrator!=1){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->role_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->role_id_pk], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'role_id_pk',
			[
	            'attribute'=>'name',
				'format'=>'html',
	            'value' => htmlentities($model->name),
	        ],
			[
				'attribute' => 'modules',
				'format' => 'ntext',
				'value' => $retStr,
			],
            [
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	        ],
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => (($model->isDeleted) == 1? wordwrap(htmlentities($model->reasonIsDeleted),16,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        	],
        ],
    ]) ?>

</div>
