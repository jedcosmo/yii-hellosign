<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\city\models\City;
use backend\modules\state\models\State;
use backend\modules\country\models\Country;

/* @var $this yii\web\View */
/* @var $model backend\modules\company\models\Company */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-company-form';
?>

<div class="company-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>

	<input type="hidden" name="gmp_company_id_pk" id="gmp_company_id_pk" value="<?=$model->company_id_pk;?>">
    
    <div class="row">
    <label class="col-sm-3">Company Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'name')->textInput(['id'=>'gmp_company_name','name'=>'gmp_company_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Address1<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'address1')->textarea(['id'=>'gmp_address1','name'=>'gmp_address1','rows' => 6, 'maxlength' => 120])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Address2</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'address2')->textarea(['id'=>'gmp_address2','name'=>'gmp_address2','rows' => 6, 'maxlength' => 120])->label(false); ?>
    </div>
    </div>
	
    <div class="row">
    <label class="col-sm-3">Country<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'country_id_fk')->dropDownList(ArrayHelper::map(Country::find()->where(['isDeleted' => '0','super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'country_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_country_id_fk','name'=>'gmp_country_id_fk','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('person/person/lists?id=').'"+$(this).val(), function( data ) { 
														$( "select#gmp_state_id_fk" ).html( data );	
														$( "select#gmp_city_id_fk" ).html("<option>--- Select One ---</option>");
													});'])->label(false); ?>
    </div>
    </div>
    
    <div class="row">
    <label class="col-sm-3">State<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?php if(!$model->isNewRecord){ ?>
        	 <?= $form->field($model, 'state_id_fk')->dropDownList(ArrayHelper::map(State::find()->where(['isDeleted' => '0','country_id_fk'=>$model->country_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'state_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_state_id_fk','name'=>'gmp_state_id_fk','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('person/person/citylists?id=').'"+$(this).val(), function( data ) { 
														$( "select#gmp_city_id_fk" ).html( data );	
													});'])->label(false); ?>
        <?php } else{ ?>
        	<?= $form->field($model, 'state_id_fk')->dropDownList(ArrayHelper::map(State::find()->where(['isDeleted' => '0','country_id_fk'=>$model->country_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'state_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_state_id_fk','name'=>'gmp_state_id_fk','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('person/person/citylists?id=').'"+$(this).val(), function( data ) { 
														$( "select#gmp_city_id_fk" ).html( data );	
													});'])->label(false); ?>
        <?php } ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">City<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    		<?php if(!$model->isNewRecord){ ?>
        	 <?= $form->field($model, 'city_id_fk')->dropDownList(ArrayHelper::map(City::find()->where(['isDeleted' => '0','state_id_fk'=>$model->state_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'city_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_city_id_fk','name'=>'gmp_city_id_fk'])->label(false); ?>
        <?php } else{ ?>
    		<?= $form->field($model, 'city_id_fk')->dropDownList(ArrayHelper::map(City::find()->where(['isDeleted' => '0','state_id_fk'=>$model->state_id_fk,'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->orderby('name ASC')->all(), 'city_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_city_id_fk','name'=>'gmp_city_id_fk'])->label(false); ?>
        <?php } ?>
    </div>
    </div>
    
    <div class="row">
	<label class="col-sm-3">PO Box</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'pobox')->textInput(['id'=>'gmp_pobox','name'=>'gmp_pobox','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Zip Code</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'zip_postalcode')->textInput(['id'=>'gmp_zip_pincode','name'=>'gmp_zip_pincode','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/personcompany/personcompany']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/personcompany.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
