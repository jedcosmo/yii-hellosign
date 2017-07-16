<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\country\models\Country;
use backend\modules\state\models\State;
use backend\modules\city\models\City;
/* @var $this yii\web\View */
/* @var $model backend\modules\city\models\City */
/* @var $form yii\widgets\ActiveForm */

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
?>

<div class="city-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => 'gmp-city-form','autocomplete' => 'off','enableAjaxValidation' => true]]); ?>
	
    <input type="hidden" name="gmp_city_id_pk" id="gmp_city_id_pk" value="<?=$model->city_id_pk;?>">
    
    <div class="row">
	<label class="col-sm-3">Country<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
        <?= $form->field($model, 'country_id_fk')->dropDownList(ArrayHelper::map(Country::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'country_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_country_id_fk','name'=>'gmp_country_id_fk','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('city/city/lists?id=').'"+$(this).val(), function( data ) { 
														$( "select#gmp_state_id_fk" ).html( data );	
													});'])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
	<label class="col-sm-3">State<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?php if(!$model->isNewRecord){ ?>
        	 <?= $form->field($model, 'state_id_fk')->dropDownList(ArrayHelper::map(State::find()->where(['isDeleted' => '0','country_id_fk'=>$model->country_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'state_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_state_id_fk','name'=>'gmp_state_id_fk'])->label(false); ?>
        <?php } else{ ?>
    	 	<?= $form->field($model, 'state_id_fk')->dropDownList(ArrayHelper::map(State::find()->where(['isDeleted' => '0','country_id_fk'=>$model->country_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'state_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_state_id_fk','name'=>'gmp_state_id_fk'])->label(false); ?>
        <?php } ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">City Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'name')->textInput(['id'=>'gmp_city_name','name'=>'gmp_city_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
        		<?php if($fromAuditLog!="Yes") { ?>
				<?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
                <span class="text-muted">&nbsp; OR &nbsp;</span>
               	<a href="<?= \yii\helpers\Url::to(['/city/city']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
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
$this->registerJsFile(Yii::$app->homeUrl.'js/city.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
