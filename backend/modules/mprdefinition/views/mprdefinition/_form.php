<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\unit\models\Unit;
use backend\modules\company\models\Company;
use backend\modules\product\models\Product;
use backend\modules\person\models\Person;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprdefinition\models\Mprdefination */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-mprdefinition-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$personsArr = getCommonFunctions::getAllPersonsOfCompany();
$persondpdw = array();
if(is_array($personsArr) && count($personsArr)>0)
{
	foreach($personsArr as $pk=>$pv)
	{
		if(CommonFunctions::isPersonHavingAccessToModule($pv['person_id_pk'], 'RM_MPR'))
		{
			$persondpdw[$pv['person_id_pk']] = $pv['first_name']." ".$pv['last_name'];
		}
	}
}

$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($model->mpr_defination_id_pk);	
?>

<div class="mprdefination-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="mpr_defination_id_pk" id="mpr_defination_id_pk" value="<?=$model->mpr_defination_id_pk;?>">
    <input type="hidden" name="mytoken" id="mytoken" value="<?=$mytoken;?>">

	<div class="row">
	<label class="col-sm-3">Product Code<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord && $model->isCopied=='1'){  ?>
    	<?= $form->field($model, 'product_id_fk')->dropDownList(ArrayHelper::map(Product::find()->where(['super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('code ASC')->all(), 'product_id_pk', 'code'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'product_code','name'=>'product_code','disabled'=>'disabled'])->label(false); ?>
        <div class="col-sm-12 text-muted" style="padding-left:0px;">[Note: You can't change product of this MPR as it is copied version]<br/>&nbsp;</div>
    <?php } elseif(!$model->isNewRecord && $pflag=='Yes'){ ?>
    	<?= $form->field($model, 'product_id_fk')->dropDownList(ArrayHelper::map(Product::find()->where(['super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('code ASC')->all(), 'product_id_pk', 'code'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'product_code','name'=>'product_code','disabled'=>'disabled'])->label(false); ?>
        <div class="col-sm-12 text-muted" style="padding-left:0px;">[Note: This product is deleted.]<br/>&nbsp;</div>
    <?php } else{  ?>
    <?= $form->field($model, 'product_id_fk')->dropDownList(ArrayHelper::map(Product::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('code ASC')->all(), 'product_id_pk', 'code'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'product_code','name'=>'product_code','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('mprdefinition/mprdefinition/productdetails?id=').'"+$(this).val(), function( data ) { 
													$("#product_part").val(data.product_part);
													$("#product_name").val(data.product_name);
													$("#product_code_hid").val(data.product_code);
													},"json");'])->label(false); ?>
    <?php } ?>
    <?php if(!$model->isNewRecord){ ?>
    <input type="hidden" name="product_code_hid" id="product_code_hid" value="<?=$model->product_code;?>">
    <?php } else{ ?>
    <input type="hidden" name="product_code_hid" id="product_code_hid" value="">
    <?php } ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Part #<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'product_part')->textInput(['id'=>'product_part','name'=>'product_part','readonly'=>'true'])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
    <label class="col-sm-3">Product Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'product_name')->textInput(['id'=>'product_name','name'=>'product_name','readonly'=>'true'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Author<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){
    	$isAuthorDeleted = checkCommonFunctions::check_If_Record_Deleted($model->author,'bpr_person','person_id_pk');
		if($isAuthorDeleted=="Yes")
		{
		?>
        	<span class="text-muted">[Note: Assigned author is deleted. Please select another author.]</span>
        <?php 
		}
	}
	?>
    <?= $form->field($model, 'author')->dropDownList($persondpdw,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'author','name'=>'author'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Formulation ID<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'formulation_id')->textInput(['id'=>'formulation_id','name'=>'formulation_id','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Product Strength<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'product_strength')->textInput(['id'=>'product_strength','name'=>'product_strength','maxlength'=>'50'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Batch Size<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'batch_size')->textInput(['id'=>'batch_size','name'=>'batch_size','maxlength'=>'10'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">MPR Unit<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){
    	$isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->MPR_unit_id,'bpr_unit','unit_id_pk');
		if($isUnitDeleted=="Yes")
		{
		?>
        	<span class="text-muted">[Note: Assigned unit is deleted. Please select another unit.]</span>
        <?php 
		}
	}
	?>
      <?= $form->field($model, 'MPR_unit_id')->dropDownList(ArrayHelper::map(Unit::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'unit_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'MPR_unit_id','name'=>'MPR_unit_id'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Theoritical Yield<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'theoritical_yield')->textarea(['id'=>'theoritical_yield','name'=>'theoritical_yield','rows' => 6,'maxlength' => '255'])->label(false); ?>
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
	}
	?>
    <?= $form->field($model, 'company_id_fk')->dropDownList(ArrayHelper::map(Company::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'company_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_company','name'=>'gmp_company'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Purpose<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'purpose')->textarea(['id'=>'purpose','name'=>'purpose','rows' => 6,'maxlength' => '255'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Scope<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'scope')->textarea(['id'=>'scope','name'=>'scope','rows' => 6,'maxlength' => '255'])->label(false); ?>
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
            <a href="<?= \yii\helpers\Url::to(['/mprdefinition/mprdefinition']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            <?php } else { ?>
			<?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'disabled'=>'disabled']) ?>
            <?php } ?>
            </div>
        </div>
    </div>
   
    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/mprdefinition.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
