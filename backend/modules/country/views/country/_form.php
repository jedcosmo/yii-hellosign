<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\country\models\Country */
/* @var $form yii\widgets\ActiveForm */

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="country-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'gmp-country-form','autocomplete' => 'off','enableAjaxValidation' => true]]); ?>

	 <input type="hidden" name="gmp_country_id_pk" id="gmp_country_id_pk" value="<?=$model->country_id_pk;?>">
    <div class="row">
	<label class="col-sm-3">Country Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?= $form->field($model, 'name')->textInput(['id'=>'gmp_country_name','name'=>'gmp_country_name','maxlength' => true,])->label(false); ?>
    </div>
    </div>
    
	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
        		<?php if($fromAuditLog!="Yes") { ?>
				<?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
                <span class="text-muted">&nbsp; OR &nbsp;</span>
               	<a href="<?= \yii\helpers\Url::to(['/country/country']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/country.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
