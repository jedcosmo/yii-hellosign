<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\unit\models\Unit;
use backend\modules\country\models\Country;
use backend\modules\billofmaterial\models\Materialtype;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\Billofmaterial */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-billofmaterials-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$materialTestStatus = array("Approved"=>"Approved", "Rejected"=>"Rejected", "Quarantine"=>"Quarantine");
?>

<div class="billofmaterial-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'enctype' => 'multipart/form-data','autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="bom_id_pk" id="bom_id_pk" value="<?=$model->bom_id_pk;?>">
    <input type="hidden" name="mpr_defination_id_fk" id="mpr_defination_id_fk" value="<?=$_GET['mprid'];?>">
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-12">
    		<label>Material Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    		<?= $form->field($model, 'material_name')->textInput(['id'=>'material_name','name'=>'material_name','maxlength' => true])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
        <div class="col-sm-6">
            <label>Material ID</label>
            <?= $form->field($model, 'material_id')->textInput(['id'=>'material_id','name'=>'material_id','maxlength' => true])->label(false); ?>
        </div>
        <div class="col-sm-6">
            <label>Material Type</label>
            <?= $form->field($model, 'material_type_id_fk')->dropDownList(ArrayHelper::map(Materialtype::find()->orderby('type_name ASC')->all(), 'material_type_id_pk', 'type_name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'material_type_id_fk','name'=>'material_type_id_fk'])->label(false); ?>
        </div>
         <div class="col-sm-12"><br/></div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-3">    
            <label>Part#</label>
            <?= $form->field($model, 'product_part')->textInput(['id'=>'product_part','name'=>'product_part','maxlength' => true,'readonly'=>'true','value'=>$mprDef->product_part])->label(false); ?>
        </div>
    
        <div class="col-sm-3">
            <label>Vendor ID</label>
            <?= $form->field($model, 'vendor_id')->textInput(['id'=>'vendor_id','name'=>'vendor_id','maxlength' => true])->label(false); ?>
        </div>
    
        <div class="col-sm-3">
            <label>Vendor Name</label>
            <?= $form->field($model, 'vendor_name')->textInput(['id'=>'vendor_name','name'=>'vendor_name','maxlength' => true])->label(false); ?>
        </div>
    
        <div class="col-sm-3">
            <label>Vendor Lot#</label>
            <?= $form->field($model, 'vendor_lot')->textInput(['id'=>'vendor_lot','name'=>'vendor_lot','maxlength' => true])->label(false); ?>
        </div>
        <div class="col-sm-12"><br/></div>
    </div>
    </div>
    
    <div class="row">
	<div class="form-group">
    	<div class="col-sm-3">
            <label>Unit Size<span class="text-danger">&nbsp;*&nbsp;</span></label>
            <?= $form->field($model, 'qty_branch')->textInput(['id'=>'qty_branch','name'=>'qty_branch','maxlength'=>'10'])->label(false); ?>
        </div>
    
        <div class="col-sm-3">
        	<label>Price Per Unit</label>
             <?php if(!$model->isNewRecord && $model->price_per_unit==0){
			 	$model->price_per_unit = '';
			 }?> 
        	<?= $form->field($model, 'price_per_unit')->textInput(['id'=>'price_per_unit','name'=>'price_per_unit','maxlength'=>'10'])->label(false); ?>
        </div>

		<div class="col-sm-3">
			<label>Unit<span class="text-danger">&nbsp;*&nbsp;</span></label>
			 <?= $form->field($model, 'qb_unit_id_fk')->dropDownList(ArrayHelper::map(Unit::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'unit_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'qb_unit_id_fk','name'=>'qb_unit_id_fk'])->label(false); ?>
              <?php if(!$model->isNewRecord){ 
                $isQBUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->qb_unit_id_fk,'bpr_unit','unit_id_pk');
                if($isQBUnitDeleted=="Yes")
                {
                    ?>
                    <span class="text-muted">[Note: Assigned unit is deleted. Please select another unit.]</span>
                    <?php
                }
             }?>
    	</div>
    	
        <div class="col-sm-3">
        	<label>Maximum Qty</label>
             <?php if(!$model->isNewRecord && $model->maximum_qty==0){
			 	$model->maximum_qty = '';
			 }?> 
    		<?= $form->field($model, 'maximum_qty')->textInput(['id'=>'maximum_qty','name'=>'maximum_qty','maxlength'=>'10'])->label(false); ?>
    	</div>
        
        <div class="col-sm-12"><br/></div>
	</div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
            <label>Composition<span class="text-danger">&nbsp;*&nbsp;</span></label>
            <?= $form->field($model, 'composition')->textInput(['id'=>'composition','name'=>'composition','maxlength' => true])->label(false); ?>
        </div>
        
        <div class="col-sm-6">
            <label>Composition Unit<span class="text-danger">&nbsp;*&nbsp;</span></label>
            <?= $form->field($model, 'com_unit_id_fk')->dropDownList(ArrayHelper::map(Unit::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'unit_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'com_unit_id_fk','name'=>'com_unit_id_fk'])->label(false); ?>
             <?php if(!$model->isNewRecord){ 
                $isCOMUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->com_unit_id_fk,'bpr_unit','unit_id_pk');
                if($isCOMUnitDeleted=="Yes")
                {
                    ?>
                    <span class="text-muted">[Note: Assigned unit is deleted. Please select another unit.]</span>
                    <?php
                }
             }?>
        </div>
         <div class="col-sm-12"><br/></div>
	</div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
            <label>Storage Condition</label>
            <?= $form->field($model, 'storage_condition')->textInput(['id'=>'storage_condition','name'=>'storage_condition','maxlength' => true])->label(false); ?>
        </div>
        
        <div class="col-sm-6">
            <label>Temperature Condition</label>
            <?= $form->field($model, 'temperature_condition')->textInput(['id'=>'temperature_condition','name'=>'temperature_condition','maxlength' => true])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
            <label>Country of Origin</label>
            <?= $form->field($model, 'country_id_fk')->dropDownList(ArrayHelper::map(Country::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'country_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_country_id_fk','name'=>'gmp_country_id_fk'])->label(false); ?>
        </div>
        
        <div class="col-sm-6">
        	<label>Total Shelf Life</label>
        	<?= $form->field($model, 'total_shelf_life')->textInput(['id'=>'total_shelf_life','name'=>'total_shelf_life','maxlength' => true])->label(false); ?>
        </div>
    </div>
    </div>

	<div class="row">
	 <div class="form-group">
    	<div class="col-sm-6">
            <label>Chemical Abstracts Service(CAS) number #</label>
           <?= $form->field($model, 'CAS_Number')->textInput(['id'=>'CAS_Number','name'=>'CAS_Number','maxlength' => true])->label(false); ?>
        </div>
        
        <div class="col-sm-6">
        	<label>Control Number #</label>
        	<?= $form->field($model, 'Control_Number')->textInput(['id'=>'Control_Number','name'=>'Control_Number','maxlength' => true])->label(false); ?>
        </div>
    </div>
    </div>
      
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-6">
            <label>Material Test Status</label>
            <?= $form->field($model, 'material_test_status')->dropDownList($materialTestStatus,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'material_test_status','name'=>'material_test_status','maxlength' => true])->label(false); ?>
        </div>
    </div>
    </div>
    
    <div class="col-sm-12"><br/></div>
    <div class="row">
    <div class="form-group">
    	<div class="col-sm-12">
            <label>Material Safety Data Sheet</label>
            <?= $form->field($model, 'material_safety_data_sheet')->fileInput(['id'=>'material_safety_data_sheet','name'=>'material_safety_data_sheet'])->label(false)->hint('[Note: Please upload file of size less than 30MB]'); ?>
            <?php if($model->isNewRecord!=1 && $model->material_safety_data_sheet!='') { ?>
                <a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/billofmaterials/<?=$model->material_safety_data_sheet; ?>" target="_blank" title="View File"><span class='glyphicon glyphicon-file'></span>&nbsp; View File</a>
            <?php } ?>
        </div>
    </div>
    </div>
    
    <div class="row">
    <div class="col-sm-12"><br/></div>
    <div class="form-group">
    	<div class="col-sm-12">
            <label>Environmental Protection Agency</label>
            <?= $form->field($model, 'environmental_protection_agency')->checkbox(['id'=>'environmental_protection_agency','name'=>'environmental_protection_agency','value' => '1','label'=>'Yes'])->label(false); ?>
        </div>
	</div>
    </div>

	<div class="row">
	<div class="form-group">
    	<div class="col-sm-12">
            <label>Select A File</label>
            <?= $form->field($model, 'select_a_file')->fileInput(['id'=>'select_a_file','name'=>'select_a_file'])->label(false)->hint('[Note: Please upload file of size less than 30MB]'); ?>
            <?php if($model->isNewRecord!=1 && $model->select_a_file!='') { ?>
                <a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/billofmaterials/<?=$model->select_a_file; ?>" target="_blank" title="View File"><span class='glyphicon glyphicon-file'></span>&nbsp; View File</a>
            <?php } ?>
        </div>
	</div>
    </div>
    
	<div class="col-sm-12"><br/></div>
	<div class="row">
            <?php if($fromAuditLog!="Yes") { ?>
            <div class="col-sm-3">
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-block btn-primary' : 'btn btn-block btn-primary']) ?>
            </div>
            <div class="col-sm-6" style="line-height:30px;">
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
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/billofmaterials.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
