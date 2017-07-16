<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this yii\web\View */
/* @var $model backend\modules\rolemanagement\models\Rolemanagement */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-role-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";	
?>

<div class="rolemanagement-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="gmp_role_id_pk" id="gmp_role_id_pk" value="<?=$model->role_id_pk;?>">
    
    <div class="row">
    <label class="col-sm-3">Role Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'name')->textInput(['maxlength' => true,'name'=>'gmp_role', 'id'=>'gmp_role'])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
	<label class="col-sm-3">Role Modules<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
   	<?php 
		$roleModules = getCommonFunctions::get_all_role_modules();
		$savedModules = array();
		$savedModules = explode(",",$model->modules);
		if(is_array($roleModules) && count($roleModules)>0)
		{
	?>
    	<div class="checkbox" style="padding-left:15px;">
             <label><input type="checkbox" name="all_chk" id="all_chk"  value="__ALL__" onclick="checkAllModules();" <?php if($model->modules=='__ALL__'){ ?> checked="checked" <?php } ?>>All</label>
        </div>
    	<div class="list-group" style="margin-bottom:2px;">            
	<?php	foreach($roleModules as $k=>$v){ 
				$checked = '';
			if(in_array($v['name_pk'],$savedModules) || $model->modules=='__ALL__')
				$checked = 'checked';
	?>
    		<div class="list-group-item">
    		<div class="checkbox" style="margin-top:0px; margin-bottom:0px;">
              <label><input type="checkbox" name="roleModules[]" id="roleModules[]" custAttr="roleModuleChk"  value="<?=$v['name_pk']?>" <?=$checked;?> ><?=$v['display_name']?></label>
            </div>
            </div>
	<?php } ?>
        </div>
        <div class="eqpExpired" id="chkModulesErr" style="display:none;"></div>
    <?php } ?>
    </div>
    </div>

	<div class="col-sm-12"><br/></div>
	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
            <?php if($fromAuditLog!="Yes") { ?>
			<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/rolemanagement/rolemanagement']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/rolemanagement.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
