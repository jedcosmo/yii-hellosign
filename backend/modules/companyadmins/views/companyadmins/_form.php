<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\rolemanagement\models\Rolemanagement;
use backend\modules\city\models\City;
use backend\modules\state\models\State;
use backend\modules\country\models\Country;
use backend\modules\personcompany\models\Personcompany;
/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\Person */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-person-form';
if(!$model->isNewRecord)
	$formname = 'gmp-personupdate-form';
?>

<div class="person-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>
  
  	<input type="hidden" name="gmp_person_id_pk" id="gmp_person_id_pk" value="<?=$model->person_id_pk;?>">
    
    <?php
		$gmp_captcha_token = time(); 
		$session = Yii::$app->session;
		$session->set('gmp_captcha_tokenC', $gmp_captcha_token);
	?>
    <input type="hidden" name="gmp_captcha_token" id="gmp_captcha_token" value="<?=$gmp_captcha_token;?>">
    <input type="hidden" name="frmtoken" id="frmtoken" value="<?=$gmp_captcha_token;?>">
    
    <div id="super_company_dropdown" class="row" style="<?php if(Yii::$app->user->identity->is_super_admin!=1){ ?>display:none;  <?php } ?>">
    <label class="col-sm-3">Person Company<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'super_company_id_fk')->dropDownList(ArrayHelper::map(Personcompany::find()->where(['isDeleted' => '0'])->orderby('name ASC')->all(), 'company_id_pk', 'name'),['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'gmp_super_company_id_fk','name'=>'gmp_super_company_id_fk','onchange'=> '$.get( "'.Yii::$app->urlManager->createUrl('companyadmins/companyadmins/rolelists?id=').'"+$(this).val(), function( data ) { 
														$( "select#gmp_role_id_fk" ).html( data );	
													});'])->label(false); ?>
    </div> 
    </div>
    
    
    <?php if($model->person_id_pk!=Yii::$app->user->id){ ?>
    <div class="row">
	<label class="col-sm-3">Role<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    <?php if(!$model->isNewRecord){ ?>
    	<?= $form->field($model, 'role_id_fk')->dropDownList(ArrayHelper::map(Rolemanagement::find()->where(['isDeleted' => '0','is_administrator'=>1,'super_company_id_fk'=>$model->super_company_id_fk])->orderby('name ASC')->all(), 'role_id_pk', 'name'),['class'=>'form-control','id'=>'gmp_role_id_fk','name'=>'gmp_role_id_fk'])->label(false); ?>

    <?php } elseif(Yii::$app->user->identity->is_super_admin==1) { ?>
			<?= $form->field($model, 'role_id_fk')->dropDownList(ArrayHelper::map(Rolemanagement::find()->where(['isDeleted' => '0','is_administrator'=>1,'super_company_id_fk'=>$model->super_company_id_fk])->orderby('name ASC')->all(), 'role_id_pk', 'name'),['class'=>'form-control','id'=>'gmp_role_id_fk','name'=>'gmp_role_id_fk'])->label(false); ?>
	<?php	} ?>  
    </div>
    </div>
    <?php } ?>
   
    <div class="row">
	<label class="col-sm-3">First Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'first_name')->textInput(['id'=>'gmp_first_name','name'=>'gmp_first_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Last Name<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'last_name')->textInput(['id'=>'gmp_last_name','name'=>'gmp_last_name','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Phone<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'phone')->textInput(['id'=>'gmp_phone','name'=>'gmp_phone','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Fax</label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'fax')->textInput(['id'=>'gmp_fax','name'=>'gmp_fax','maxlength' => true])->label(false); ?>
    </div>
    </div>

	<div class="row">
	<label class="col-sm-3">Address<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?= $form->field($model, 'address')->textarea(['id'=>'gmp_address','name'=>'gmp_address','rows' => 6, 'maxlength' => 120])->label(false); ?>
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
    	<?= $form->field($model, 'zip_pincode')->textInput(['id'=>'gmp_zip_pincode','name'=>'gmp_zip_pincode','maxlength' => true])->label(false); ?>
    </div>
    </div>
    
	<div class="row">    
	<label class="col-sm-3">Email ID<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?php if($model->new_emailid!='' && $model->is_verified==1){ ?>
        <?= $form->field($model, 'emailid')->textInput(['id'=>'gmp_emailid','name'=>'gmp_emailid','maxlength' => true])->label(false)->hint('[Emailid changed to "'.$model->new_emailid.'", Verification pending.]'); ?>
        <?php } else{ ?>
    	<?= $form->field($model, 'emailid')->textInput(['id'=>'gmp_emailid','name'=>'gmp_emailid','maxlength' => true])->label(false); ?>
        <?php } ?>
    </div>
	</div>
    
    <div class="row">
	<label class="col-sm-3">Username<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?php if($model->new_username!='' && $model->is_verified==1){ ?>
    	<?= $form->field($model, 'user_name_person')->textInput(['id'=>'gmp_user_name_person','name'=>'gmp_user_name_person','maxlength' => true,'autocomplete'=>'off'])->label(false)->hint('[Username changed to "'.$model->new_username.'", Verification pending.]'); ?>
        <?php } else{ ?>
        <?= $form->field($model, 'user_name_person')->textInput(['id'=>'gmp_user_name_person','name'=>'gmp_user_name_person','maxlength' => true,'autocomplete'=>'off'])->label(false); ?>
        <?php } ?>
    </div>
	</div>
    
    <div class="row">
	<label class="col-sm-3">Password<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<?php if(!$model->isNewRecord){ 
			$model->password_person = '';
		?>
        	<?= $form->field($model, 'password_person')->passwordInput(['id'=>'gmp_password_person','name'=>'gmp_password_person','maxlength' => 16])->label(false)->hint('[Enter only if you want to change the password]'); ?>
        <?php } else{ ?>
    	<?= $form->field($model, 'password_person')->passwordInput(['id'=>'gmp_password_person','name'=>'gmp_password_person','maxlength' => 16])->label(false); ?>
        <?php } ?>
    </div>
    </div>

	<div class="row">
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'id' => 'cmpadminsubbtn']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to(['/companyadmins/companyadmins']) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/companyadmins.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
