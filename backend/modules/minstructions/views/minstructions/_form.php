<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\unit\models\Unit;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\minstructions\models\Minstructions */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-minstructions-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="minstructions-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="mi_id_pk" id="mi_id_pk" value="<?=$model->mi_id_pk;?>">
    <input type="hidden" name="mpr_defination_id_fk" id="mpr_defination_id_fk" value="<?=$_GET['mprid'];?>">
    
    <div class="row">
    <label class="col-sm-3">Step<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'mi_step')->textInput(['id'=>'mi_step','name'=>'mi_step','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Action</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'mi_action')->textarea(['id'=>'mi_action','name'=>'mi_action','maxlength'=>'500'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Unit</label>
    <div class="col-sm-8">
    	<?php if(!$model->isNewRecord && $model->unit_id_fk>0){ 
	 	$isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($model->unit_id_fk,'bpr_unit','unit_id_pk');
		if($isUnitDeleted=="Yes")
		{
			?>
            <span class="text-muted">[Note: Assigned unit is deleted. Please select another unit.]</span>
            <?php
		}
	 	}?>
    	<?= $form->field($model, 'unit_id_fk')->dropDownList(ArrayHelper::map(Unit::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'unit_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'unit_id_fk','name'=>'unit_id_fk'])->label(false); ?>
    </div>
	</div>
    
    <div class="row">
	<label class="col-sm-3">Range</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'mi_range')->textInput(['id'=>'mi_range','name'=>'mi_range','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Target</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'target')->textInput(['id'=>'target','name'=>'target','maxlength' => true])->label(false); ?>
    </div>
	</div>
    
    <div class="row">
	<label class="col-sm-3">Performer</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'perfomer')->checkbox(['id'=>'perfomer','name'=>'perfomer','label' => '','value' => 'Yes'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Verifier</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'verifier')->checkbox(['id'=>'verifier','name'=>'verifier','label' => '','value' => 'Yes'])->label(false); ?>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/minstructions.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
