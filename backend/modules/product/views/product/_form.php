<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\unit\models\Unit;
use backend\modules\company\models\Company;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\product\models\Product */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-product-form';
if(!$model->isNewRecord)
	$formname = 'gmp-productupdate-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="product-form">

	<?php $form = ActiveForm::begin(['options' => ['id' => $formname, 'enctype' => 'multipart/form-data','autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>
    
    <input type="hidden" name="gmp_product_id_pk" id="gmp_product_id_pk" value="<?=$model->product_id_pk;?>">
    
    <div class="row">
    <label class="col-sm-3">Product Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){ ?>
    <?= $form->field($model, 'name')->textInput(['id'=>'gmp_product_name','name'=>'gmp_product_name','maxlength' => true, 'readonly'=>'readonly'])->label(false); ?>
	<?php }else{ ?>
    <?= $form->field($model, 'name')->textInput(['id'=>'gmp_product_name','name'=>'gmp_product_name','maxlength' => true])->label(false); ?>
    <?php } ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Company<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){ 
	 	$isCompanyDeleted = checkCommonFunctions::check_If_Record_Deleted($model->company_id_fk,'bpr_company','company_id_pk');
		if($isCompanyDeleted=="Yes")
		{
			?>
            <span class="text-muted">[Note: Assigned company is deleted. Please select another company.]</span>
            <?php
		}
	 	}?>
     <?= $form->field($model, 'company_id_fk')->dropDownList(ArrayHelper::map(Company::find()->where(['isDeleted' => '0', 'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'company_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_company','name'=>'gmp_company'])->label(false); ?>
   	</div>
    </div>

	<div class="row">
	<label class="col-sm-3">Product Code<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){ ?>
    <?= $form->field($model, 'code')->textInput(['id'=>'gmp_product_code','name'=>'gmp_product_code','maxlength' => true, 'readonly'=>'readonly'])->label(false); ?>
    <?php } else { ?>
    <?= $form->field($model, 'code')->textInput(['id'=>'gmp_product_code','name'=>'gmp_product_code','maxlength' => true])->label(false); ?>
    <?php } ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Unit<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){ 
	 	$isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->unit_id_fk,'bpr_unit','unit_id_pk');
		if($isUnitDeleted=="Yes")
		{
			?>
            <span class="text-muted">[Note: Assigned unit is deleted. Please select another unit.]</span>
            <?php
		}
	 	}?>
    
     <?= $form->field($model, 'unit_id_fk')->dropDownList(ArrayHelper::map(Unit::find()->where(['isDeleted' => '0', 'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'unit_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_product_unit','name'=>'gmp_product_unit'])->label(false); ?>
    </div>
	</div>
	
	<div class="row">
	<label class="col-sm-3">Document</label>
    <div class="col-sm-8">
     <input type="file" name="gmp_product_document" id="gmp_product_document">
     <span class="text-muted small">[Note: Please upload file of size less than 30MB]</span>
    <?php if($model->isNewRecord!=1 && $model->document_id_fk!='') { 
		$docname = $model->getProductDocument($model->document_id_fk);
	?>
    	<a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/documents/<?=$docname; ?>" target="_blank" title="View Document"><span class='glyphicon glyphicon-file'></span>&nbsp; View Document</a>
    <?php } ?>
    </div>  
    </div>  
    
    <div class="col-sm-12"><br/></div>
	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
            <?php if($fromAuditLog!="Yes") { ?>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/product/product']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            <?php } else { ?>
			<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'disabled'=>'disabled']) ?>
            <?php } ?>
            </div>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/product.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
