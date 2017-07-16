<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\bprrecords\models\Bprrecords */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-bpr-form';

$productsArr = array();
$productsArr = getCommonFunctions::getApprovedProductCodes();
$mprVerArr = array();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>
<style type="text/css">
.input-group .input-group-addon { background-color:#eeeeee!important;}
.mytxt{ background-color:#FFFFFF!important;}
</style>
<div class="bprrecords-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="bpr_id_pk" id="bpr_id_pk" value="<?=$model->bpr_id_pk;?>">
	<input type="hidden" name="mpr_version_hid" id="mpr_version_hid" value="">
    
    <div class="row">
	<label class="col-sm-3">Product Code<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'product_code')->dropDownList($productsArr,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'product_code','name'=>'product_code', 'onchange'=> 'changeMPRonProductChange();'])->label(false); ?>
        <div class="error" id="product_code_err_div" style="display:none;margin-top:-10px!important;">Please select product code</div>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">MPR Version # <span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<div class="input-group">
        	<input type="hidden" name="mpr_def_id_fk" id="mpr_def_id_fk"/>
            <input type="text" class="form-control mytxt" name="mpr_version_ro" id="mpr_version_ro" readonly="readonly"/>
            <span class="input-group-addon">
               <a href="javascript:void(0);" onclick="javascript:searchMPRVersion('<?=$fromAuditLog?>');" > <i class="fa fa-search"></i> </a>
            </span>
        </div>
    </div>
    </div>
   
    <div class="col-sm-12"><br/></div>
    
    <div class="row">
    <label class="col-sm-3">Lot #</label>
    <div class="col-sm-8">
    	<input type="text" class="form-control" name="lot_hash" id="lot_hash" value="<?=$model->lot_hash?>" maxlength="50"/>
    </div>
    </div>
    
	<div class="col-sm-12"><br/></div>
    
	<div id="mpr_details_div" class="row"></div>
    
    <div class="col-sm-12"><br/></div>
	<div class="row" id="submitBtnDiv" style="display:none;">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
             <?php if($fromAuditLog!="Yes") { ?>
            <?= Html::submitButton($model->isNewRecord ? 'Confirm' : 'Confirm', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
             <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/bprrecords/bprrecords']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            <?php } else { ?>
            <?= Html::submitButton($model->isNewRecord ? 'Confirm' : 'Confirm', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'disabled'=>'disabled']) ?>
            <?php } ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- Modal Starts here-->
<div class="modal fade" id="myModalBig" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>
<!-- Modal Ends here-->
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/bprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs("$(function() {

	
	jQuery.validator.addMethod('gmpproductcode', function (param, element) {
			if(param.length > 0)
			  return param.match(/^[a-zA-Z0-9]+$/);
			else
				return true;
	}, 'You can use alphanumeric characters only');
		 		 
	// validate add person form on keyup and submit
	jQuery('#gmp-bpr-form').validate({
		errorElement:'div',
		rules: {
			product_code : { required: true },
			mpr_version_ro : { required: true },
			lot_hash:{ gmpproductcode: true },
		},
		messages:{
			product_code : { required: 'Please select product code.' },
			mpr_version_ro : { required: 'Please select MPR version.' },			
		},
		success: function(label){
			label.hide();
		}
	});
	
});");
?>
