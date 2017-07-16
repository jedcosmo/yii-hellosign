<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipment\models\Equipment */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-equipment-form';
if(!$model->isNewRecord)
	$formname = 'gmp-equipmentupdate-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="equipment-form">

	<?php $form = ActiveForm::begin(['options' => ['id' => $formname, 'enctype' => 'multipart/form-data','autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>
    
    <input type="hidden" name="gmp_equipment_id_pk" id="gmp_equipment_id_pk" value="<?=$model->equipment_id_pk;?>">

	<div class="row">
	<label class="col-sm-4">Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-7">
    <?= $form->field($model, 'name')->textInput(['id'=>'gmp_equipment_name','name'=>'gmp_equipment_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-4">Model<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-7">
    <?= $form->field($model, 'model')->textInput(['id'=>'gmp_equipment_model','name'=>'gmp_equipment_model','maxlength' => true])->label(false); ?>
   	</div>
    </div>

	<div class="row">
	<label class="col-sm-4">Serial<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-7">
    <?= $form->field($model, 'serial')->textInput(['id'=>'gmp_equipment_serial','name'=>'gmp_equipment_serial','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-4">Calibration Due Date<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-7">
    <?php if(!$model->isNewRecord){ $model->caliberation_due_date = date('m/d/Y',strtotime($model->caliberation_due_date)); } ?>
    <?= $form->field($model, 'caliberation_due_date')->textInput(['id'=>'caliberation_due_date','name'=>'caliberation_due_date', 'readonly'=>'true','style'=>'background-color:#FFFFFF!important;'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-4">Preventive Maintenance Due Date<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-7">
  	<?php if(!$model->isNewRecord){ $model->preventive_m_due_date = date('m/d/Y',strtotime($model->preventive_m_due_date)); } ?>
    <?= $form->field($model, 'preventive_m_due_date')->textInput(['id'=>'preventive_m_due_date','name'=>'preventive_m_due_date','readonly'=>'true','readonly'=>'true','style'=>'background-color:#FFFFFF!important;'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-4">Document</label>
    <div class="col-sm-7">
     <input type="file" name="gmp_equipment_document" id="gmp_equipment_document">
     <span class="text-muted small">[Note: Please upload file of size less than 30MB]</span>
    <?php if($model->isNewRecord!=1 && $model->document_id_fk!='') { 
		$docname = $model->getEquipmentDocument($model->document_id_fk);
	?>
    	<a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/documents/<?=$docname; ?>" target="_blank" title="View Document"><span class='glyphicon glyphicon-file'></span>&nbsp; View Document</a>
    <?php } ?>
    </div> 
    </div>   

	<div class="col-sm-12"><br/></div>
	<div class="row">
    	<label class="col-sm-4">&nbsp;</label>
        <div class="form-group col-sm-7">
        	<div>
            <?php if($fromAuditLog!="Yes") { ?>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
             <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/equipment/equipment']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            <?php } else { ?>
			<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'disabled'=>'disabled']) ?>
            <?php } ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<style type="text/css">
    .ui-datepicker-header{  
        color:#666666!important;  
    }  
</style>
<?php
$this->registerCssFile(Yii::$app->homeUrl."js/jquery-ui-1.11.0.custom/jquery-ui.css");
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery-ui-1.11.0.custom/jquery-ui.js', ['depends' => [\yii\web\JqueryAsset::className()]]); 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/equipment.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
