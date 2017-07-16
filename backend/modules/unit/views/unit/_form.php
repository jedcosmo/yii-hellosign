<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\unit\models\Unit */
/* @var $form yii\widgets\ActiveForm */

$formname = 'gmp-unit-form';
if(!$model->isNewRecord)
	$formname = 'gmp-unitupdate-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

?>

<div class="unit-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname, 'enctype' => 'multipart/form-data','autocomplete' => 'off','enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="gmp_unit_id_pk" id="gmp_unit_id_pk" value="<?=$model->unit_id_pk;?>" >
    
    <div class="row">
	<label class="col-sm-3">Unit Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'name')->textInput(['id'=>'gmp_unit_name','name'=>'gmp_unit_name','maxlength' => true, 'onblur' => 'javascript:blurDescription();'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Description<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'description')->textarea(['id'=>'gmp_unit_description','name'=>'gmp_unit_description','rows' => 6, 'maxlength' => 255, 'onblur' => 'javascript:blurName();'])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Symbol</label>
    <div class="col-sm-8">
    <input type="file" name="gmp_unit_symbols" id="gmp_unit_symbols">
    <span class="text-muted small">[Note: Please upload file of size less than 30MB]</span>
    <?php if($model->isNewRecord!=1 && $model->symbols!='') { ?>
    	<a href="<?=Yii::$app->urlManager->baseUrl;?>/uploads/symbols/<?=$model->symbols; ?>" target="_blank" title="View symbol"><span class='glyphicon glyphicon-file'></span>&nbsp; View Symbol</a>
    <?php } ?>
    </div>
    </div>
 
    <div class="col-sm-12"><br/></div>
  
	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8" >
        	<div>
            	<?php if($fromAuditLog!="Yes") { ?>
        		<?= Html::submitButton($model->isNewRecord ? 'Add' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
                <span class="text-muted">&nbsp; OR &nbsp;</span>
               	<a href="<?= \yii\helpers\Url::to(['/unit/unit']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/unit.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
