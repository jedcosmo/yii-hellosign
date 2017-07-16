<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\equipment\models\Equipment;
use backend\modules\equipment\models\EquipmentSearch;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\equipmentmap\models\Equipmentmap */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-equipment-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";


?>

<div class="equipmentmap-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'enctype' => 'multipart/form-data','autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="equipment_map_id_pk" id="equipment_map_id_pk" value="<?=$model->equipment_map_id_pk;?>">
    <input type="hidden" name="mpr_defination_id_fk" id="mpr_defination_id_fk" value="<?=$_GET['mprid'];?>">
    <input type="hidden" name="product_id_fk" id="product_id_fk" value="<?=$mprDef->product_id_fk;?>">
	<input type="hidden" name="product_code" id="product_code" value="<?=$mprDef->product_code;?>">

	<div class="row">
	<div class="form-group">
    	<div class="col-sm-12">
        	<label>Product Code</label>
            <?= $form->field($model, 'product_code')->textInput(['id'=>'product_code','name'=>'product_code','readonly'=>'true','value'=>$mprDef->product_code])->label(false); ?>
        </div>
    </div>
    </div>

	<div class="row">
	<div class="form-group">
    	<div class="col-sm-12">
        	<label>Equipment Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
            <?php if(!$model->isNewRecord){ 
            $isEqpExists = checkCommonFunctions::checkIfEquipmentExistsInDropdown($model->equipment_id_fk);
            if($isEqpExists=="No")
            {
                ?>
                <span class="text-muted">[Note: Assigned equipment not found. Please select another equipment.]</span>
                <?php
            }
            }?>
            <?= $form->field($model, 'equipment_id_fk')->dropDownList(ArrayHelper::map(Equipment::find()->where('isDeleted="0" and (caliberation_due_date >= :curdate and preventive_m_due_date >= :curdate) and super_company_id_fk="'.Yii::$app->user->identity->super_company_id_fk.'"',['curdate' => date("Y-m-d")])->orderby('name ASC')->all(), 'equipment_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'equipment_id_fk','name'=>'equipment_id_fk'])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
        	<label>Equipment #</label>
            <?= $form->field($model, 'equipment_name')->textInput(['id'=>'equipment_name','name'=>'equipment_name','readonly'=>'true'])->label(false); ?>
        </div>
        <div class="col-sm-6">
        	<label>Model #</label>
            <?= $form->field($model, 'equipment_model')->textInput(['id'=>'equipment_model','name'=>'equipment_model','readonly'=>'true'])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
        	<label>Calibration Due Date</label>
             <?php if(!$model->isNewRecord){
			 	if(isset($model->calibration_due_date) && $model->calibration_due_date!='' && $model->calibration_due_date!='0000-00-00 00:00:00')
				{
					$model->calibration_due_date = date("m/d/Y",strtotime($model->calibration_due_date));
				}
				else{
					$model->calibration_due_date = '';
				}
			 }
			 ?>
            <?= $form->field($model, 'calibration_due_date')->textInput(['id'=>'calibration_due_date','name'=>'calibration_due_date','readonly'=>'true'])->label(false); ?>
        </div>
        <div class="col-sm-6">
        	<label>Preventive Maintenance Due Date</label>
             <?php if(!$model->isNewRecord){
			 	if(isset($model->preventive_m_due_date) && $model->preventive_m_due_date!='' && $model->preventive_m_due_date!='0000-00-00 00:00:00')
				{
					$model->preventive_m_due_date = date("m/d/Y",strtotime($model->preventive_m_due_date));
				}
				else{
					$model->preventive_m_due_date = '';
				}
			 }
			 ?>
            <?= $form->field($model, 'preventive_m_due_date')->textInput(['id'=>'preventive_m_due_date','name'=>'preventive_m_due_date','readonly'=>'true'])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
      <div class="form-group">
    	<div class="col-sm-12">
        	<label>Activity</label>
            <?= $form->field($model, 'activity')->textInput(['id'=>'activity','name'=>'activity','maxlength'=>true])->label(false); ?>
        </div>
      </div>
    </div>
    
    <div class="row">
     <div class="form-group">
    	<div class="col-sm-6">
        	<label>Start Date & Time</label>
              <?php if(!$model->isNewRecord){
			 	if(isset($model->start_date_time) && $model->start_date_time!='0000-00-00 00:00:00')
				{
					$model->start_date_time = date("m/d/Y H:i:s",strtotime($model->start_date_time));
				}
				else{
					$model->start_date_time = '';
				}
			 }
			 ?>
            <?= $form->field($model, 'start_date_time')->textInput(['id'=>'start_date_time','name'=>'start_date_time','class'=>'form-control mytxt','readonly'=>'true'])->label(false); ?>
        </div>
        <div class="col-sm-6">
        	<label>End Date & Time</label>
              <?php if(!$model->isNewRecord){
			 	if(isset($model->end_date_time) && $model->end_date_time!='0000-00-00 00:00:00')
				{
					$model->end_date_time = date("m/d/Y H:i:s",strtotime($model->end_date_time));
				}
				else{
					$model->end_date_time = '';
				}
			 }
			 ?>
            <?= $form->field($model, 'end_date_time')->textInput(['id'=>'end_date_time','name'=>'end_date_time','class'=>'form-control mytxt','readonly'=>'true'])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-12">
        	<label>Dept. Assigned To</label>
            <?= $form->field($model, 'dept_assigned_to')->textInput(['id'=>'dept_assigned_to','name'=>'dept_assigned_to','maxlength'=>true])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
     <div class="form-group">
    	<div class="col-sm-12">
        	<label>Cleaning Agent</label>
            <?= $form->field($model, 'cleaning_agent')->textInput(['id'=>'cleaning_agent','name'=>'cleaning_agent','maxlength'=>true])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
     <div class="form-group">
    	<div class="col-sm-4">
        	<label>Batch #</label>
            <?= $form->field($model, 'batch')->textInput(['id'=>'batch','name'=>'batch','readonly'=>'true','value'=>'---'])->label(false); ?>
        </div>
        <div class="col-sm-4">
        	<label>Product Name</label>
            <?= $form->field($model, 'product_name')->textInput(['id'=>'product_name','name'=>'product_name','readonly'=>'true','value'=>$mprDef->product_name])->label(false); ?>
        </div>
        <div class="col-sm-4">
        	<label>Part #</label>
            <?= $form->field($model, 'product_part')->textInput(['id'=>'product_part','name'=>'product_part','readonly'=>'true','value'=>$mprDef->product_part])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-12">
        	<label>Attachments</label>
            <?= $form->field($model, 'attachment')->fileInput(['id'=>'attachment','name'=>'attachment'])->label(false)->hint('[Note: Please upload file of size less than 30MB]'); ?>
             <?php if($model->isNewRecord!=1 && $model->attachment!='') { ?>
                <a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/equipmentmap/<?=$model->attachment; ?>" target="_blank" title="View File"><span class='glyphicon glyphicon-file'></span>&nbsp; View File</a>
            <?php } ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-12">
        	<label>Comments</label>
            <?= $form->field($model, 'comments')->textArea(['id'=>'comments','name'=>'comments','maxlength'=>500])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
     <div class="form-group">
    	<div class="col-sm-12">
            <?= $form->field($model, 'operator_signature')->checkbox(['id'=>'operator_signature','name'=>'operator_signature','value' => '1','label'=>'Operator Signature'])->label(false); ?>
        </div>
	</div>
    </div>
    
    <div class="col-sm-12"><br/></div>
	<div class="row">
		<?php if($fromAuditLog!="Yes") { ?>
        <div class="col-sm-3">
        <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-block btn-primary' : 'btn btn-block btn-primary']) ?>
        </div>
        <div class="col-sm-3" style="line-height:30px;">
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to([$backUrl]) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
        </div>
        <?php } else { ?>
        <div class="col-sm-3">
        	<?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-block btn-primary' : 'btn btn-block btn-primary', 'disabled'=>'disabled']) ?>
        </div>
        <?php } ?>   
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style type="text/css">
.input-group .input-group-addon { background-color:#eeeeee!important;}
/*.ui-datepicker-header{ color:#666666!important; } */ 
.mytxt{ background-color:#FFFFFF!important;}
.ui-datepicker-header{  color:#666666!important;  }  
#ui-datepicker-div{
	opacity: 1 !important;
	position: fixed !important;
	top: 181px !important;
	z-index: 1;
}
.ui_tpicker_minute{
	padding-left:7px;
	padding-right:7px;
}
.ui_tpicker_hour{
	padding-left:7px;
	padding-right:7px;
}
.ui_tpicker_second{
	padding-left:7px;
	padding-right:7px;
}
</style>
<?php 
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.css");
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery-ui-1.11.0.custom/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery-ui-timepicker-addon.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/equipmentmap.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
