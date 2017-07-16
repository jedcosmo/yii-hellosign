<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\country\models\Country;
/* @var $this yii\web\View */
/* @var $model backend\modules\state\models\State */
/* @var $form yii\widgets\ActiveForm */

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="state-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'gmp-state-form','autocomplete' => 'off','enableAjaxValidation' => true]]); ?>

	 <input type="hidden" name="gmp_state_id_pk" id="gmp_state_id_pk" value="<?=$model->state_id_pk;?>">
     
    <div class="row">
	<label class="col-sm-3">Country<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
        <?= $form->field($model, 'country_id_fk')->dropDownList(ArrayHelper::map(Country::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name')->all(), 'country_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_country_id_fk','name'=>'gmp_country_id_fk'])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
	<label class="col-sm-3">State Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'name')->textInput(['id'=>'gmp_state_name','name'=>'gmp_state_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8" >
        	<div>
        		<?php if($fromAuditLog!="Yes") { ?>
				<?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
                <span class="text-muted">&nbsp; OR &nbsp;</span>
               	<a href="<?= \yii\helpers\Url::to(['/state/state']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/state.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
