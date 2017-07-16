<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\unit\models\Unit;
use backend\modules\country\models\Country;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\Billofmaterial */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-formulation-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

?>

<div class="billofmaterial-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'enctype' => 'multipart/form-data','autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="f_id_pk" id="f_id_pk" value="<?=$model->f_id_pk;?>">
    <input type="hidden" name="mpr_defination_id_fk" id="mpr_defination_id_fk" value="<?=$_GET['mprid'];?>">
    
    <div class="row">
    <label class="col-sm-3">Part #</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'material_part')->textInput(['id'=>'material_part','name'=>'material_part','maxlength' => true])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
    <label class="col-sm-3">Ingredient Name</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'material_name')->textInput(['id'=>'material_name','name'=>'material_name','maxlength' => true])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
    <label class="col-sm-3">Formulation %</label>
    <div class="col-sm-4">
        <div class="input-group">
        	<input type="text" class="form-control" name="formulation_percentage" id="formulation_percentage" maxlength="15" value="<?=$model->formulation_percentage;?>"/>
            <span class="input-group-addon" style="background-color:#eeeeee;">%</span>
        </div>
        <div class="error" for="formulation_percentage" generated="true"></div>
    </div>
	</div>
	
    <div class="row" id="formulation_error" name="formulation_error" style="display:none;">
    	<div class="col-sm-3"></div>
        <div class="col-sm-8" style="color:#CC3300;padding-left:8px;" id="formulation_sub_error"></div>
    </div>
    
    <div class="col-sm-12"><br/></div>
	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
            <?php if($fromAuditLog!="Yes") { ?>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to([$backUrl]) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/formulation.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
