<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\product\models\Product */

$this->title = 'PRODUCT DETAILS:'.$model->product_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
if($model->isDeleted=='1'){ 
$this->params['breadcrumbs'][] = ['label' => 'Deleted Products', 'url' => ['deletedlist']];
}
$this->params['breadcrumbs'][] = 'Product Details';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$unitClass = ''; $unitTitle ='';
$companyClass = ''; $companyTitle = '';
$isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->unit_id_fk,'bpr_unit','unit_id_pk');
if($isUnitDeleted=="Yes")
{
	$unitClass = 'error';
	$unitTitle = 'This unit is deleted';
}
	
$isCompanyDeleted = checkCommonFunctions::check_If_Record_Deleted($model->company_id_fk,'bpr_company','company_id_pk');
if($isCompanyDeleted=="Yes")
{
	$companyClass = 'error';
	$companyTitle = 'This company is deleted';
}
?>
<div class="product-view">
	<?php if($model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->product_id_pk], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->product_id_pk], [
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
            'product_id_pk',
			'part',
			[                      
				'attribute'=>'name',
				'format'=>'html',
            	'value' => wordwrap(htmlentities($model->name),25,'<br/>',1),
			],
			'code',
			[                      
				'attribute'=>'company_id_fk',
				'format'=>'html',
            	'value' => "<span class='".$companyClass."' title='".$companyTitle."'>".wordwrap($model->showCompanyName($model->company_id_fk),25,'<br/>',1)."</span>",
        	],
			[                      
				'attribute'=>'unit_id_fk',
				'format'=>'html',
            	'value' => "<span class='".$unitClass."' title='".$unitTitle."'>".wordwrap($model->showUnitName($model->unit_id_fk),25,'<br/>',1)."</span>",
        	],
			[                      
				'attribute'=>'document_id_fk',
				'format'=>'raw',
            	'value' => $model->showDocument($model->document_id_fk),
        	],
            [
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	        ],
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => (($model->isDeleted) == 1? wordwrap($model->reasonIsDeleted,16,'<br/>',1) : ''),
				'visible' => (($model->isDeleted) == 1? true : false),
        	],
        ],
    ]) ?>

</div>
