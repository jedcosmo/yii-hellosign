<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\Billofmaterial */
$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
$bprid = (isset($_REQUEST['bprid']))?$_REQUEST['bprid']:0;
$bclabel = '';
if($mode=='bpr')
{
	$backUrl = '/bprrecords/bprrecords/view?id='.$bprid.'&tab=billofmaterials';
	$listingPg = '/bprrecords/bprrecords';
	$bclabel = 'BPR Records';
}
else
{
	$backUrl = '/mprdefinition/mprdefinition/view?id='.$model->mpr_defination_id_fk.'&tab=billofmaterials';
	$listingPg = '/mprdefinition/mprdefinition';
	$bclabel = 'MPR Definitions';
}

$this->title = "BILL OF MATERIAL DETAILS";
$this->params['breadcrumbs'][] = ['label' => $bclabel, 'url' => [$listingPg]];
$this->params['breadcrumbs'][] = ['label' => 'Bill Of Materials', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = "Bill Of Material Details";


$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_fk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_fk,'bpr_mpr_defination','mpr_defination_id_pk');
$MPRdetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_defination_id_fk);


$QBunitClass = ''; $QBunitTitle = '';
$isQBUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->qb_unit_id_fk,'bpr_unit','unit_id_pk');
if($isQBUnitDeleted=="Yes")
{
	$QBunitClass = 'error';
	$QBunitTitle = 'This unit is deleted';
}

$ComunitClass = ''; $ComunitTitle = '';
$isComUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->com_unit_id_fk,'bpr_unit','unit_id_pk');
if($isComUnitDeleted=="Yes")
{
	$ComunitClass = 'error';
	$ComunitTitle = 'This unit is deleted';
}

$reasonVisible = false;
$reasonText = '';
if($model->isDeleted=='1')
{
	$reasonVisible = true;
	$reasonText = wordwrap($model->reasonIsDeleted,16,'<br/>',1);
}
else if($model->isDeleted!='1' && $isMDeleted=='1')
{
	$reasonVisible = true;
	$reasonText = wordwrap($MPRdetails['reasonIsDeleted'],16,'<br/>',1); 
}
else
{
	$reasonVisible = false;
	$reasonText =  '';
}

?>
<div class="billofmaterial-view">

	<?php if($rflag!='Yes' && $isMDeleted!='1' && $model->isDeleted=='0' && $fromAuditLog=="No"){ ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->bom_id_pk, 'mprid' => $model->mpr_defination_id_fk, 'bprid' => $bprid], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->bom_id_pk, 'mprid' => $model->mpr_defination_id_fk, 'bprid' => $bprid], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php } elseif($rflag=='Yes') { ?>
		<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This record is approved. So it is sealed & in readonly mode.
        </div>
	<?php }elseif($isMDeleted=='1'){ ?>
    	<div class="alert alert-warning">
        	<span class="glyphicon glyphicon-info-sign"></span>&nbsp;This record is deleted. So it is sealed & in readonly mode.
        </div>
    <?php } ?>

    <?= DetailView::widget([
        'model' => $model,
		'template' => '<tr><th width="150">{label}</th><td width="400">{value}</td></tr>',
        'attributes' => [
           // 'bom_id_pk',
            'material_name',
			'material_id',
			[
				'attribute'=>'material_type_id_fk',
				'format'=>'raw',
				'value'=> $model->showMaterialType($model->material_type_id_fk),
			],
			
			[
				'attribute'=>'product_part',
				'format'=>'raw',
				'value'=> $MPRdetails['product_part'],
			],
			'vendor_id',
			'vendor_name',
			'vendor_lot',
			
			'qty_branch',
			[
				'attribute'=>'price_per_unit',
				'format'=>'raw',
				'value'=> ($model->price_per_unit==0)?"":$model->price_per_unit,
			],
			[                      
				'attribute'=>'qb_unit_id_fk',
				'format'=>'raw',
            	'value' => "<span class='".$QBunitClass."' title='".$QBunitTitle."'>".wordwrap($model->showUnitName($model->qb_unit_id_fk),25,'<br/>',1)."</span>",
        	],
			[
				'attribute'=>'maximum_qty',
				'format'=>'raw',
				'value'=> ($model->maximum_qty==0)?"":$model->maximum_qty,
			],
			
			'composition',
			[                      
				'attribute'=>'com_unit_id_fk',
				'format'=>'raw',
            	'value' => "<span class='".$ComunitClass."' title='".$ComunitTitle."'>".wordwrap($model->showUnitName($model->com_unit_id_fk),25,'<br/>',1)."</span>",
        	],
			
			'storage_condition' ,
			'temperature_condition',
			[
				'attribute'=>'country_id_fk',
				'format'=>'raw',
				'value'=> $model->showCountryName($model->country_id_fk),
			],
			
			'total_shelf_life',
			'CAS_Number',
			'Control_Number',
			'material_test_status',
			
			[
				'attribute'=>'material_safety_data_sheet',
				'format'=>'raw',
				'value'=> $model->showDataSheet($model->material_safety_data_sheet),
			],
			[
				'attribute'=>'environmental_protection_agency',
				'format'=>'raw',
				'value'=> ($model->environmental_protection_agency==1)?"Yes":"No",
			],
			[
				'attribute'=>'select_a_file',
				'format'=>'raw',
				'value'=> $model->showDataSheet($model->select_a_file),
			],
           
            [
	            'attribute'=>'created_datetime',
				'format' => 'raw',
	            'value' => ($model->created_datetime!='0000-00-00 00:00:00')? date("m/d/Y H:i:s",strtotime($model->created_datetime)).Yii::$app->params['timezoneVar']: "",
	        ],
			[                      
				'attribute'=>'reasonIsDeleted',
				'format'=>'raw',
            	'value' => $reasonText,
				'visible' => $reasonVisible,
        	],
        ],
    ]) ?>

</div>
