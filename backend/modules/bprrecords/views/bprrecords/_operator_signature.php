<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];

?>
<div class="modal-header text-primary">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h5 class="modal-title">BPR Equipment Operator Signature</h5>
</div>
<div class="modal-body">
	<input type="hidden" name="module_primary_id" id="module_primary_id" value="<?=$equipment_map_id_pk;?>">
    <input type="hidden" name="module_name" id="module_name" value="BPR_Equipment_Operator">
    <input type="hidden" name="masterBPR" id="masterBPR" value="<?=$bpr_id;?>">
    <input type="hidden" name="mpr_def_id" id="mpr_def_id" value="<?=$mpr_def_id;?>">
    <input type="hidden" name="equipment_map_id_pk" id="equipment_map_id_pk" value="<?=$equipment_map_id_pk;?>">
    <input type="hidden" name="signType" id="signType" value="<?=$signType;?>">
    <div class="login-box-body">
    	
        <div id="loginFormDiv">  
        <?php $form = ActiveForm::begin(['options' => ['id' => 'login_form','name'=>'login_form', 'enableClientValidation' => false, 'autocomplete'=> 'off']]); ?>
		<input type="hidden" name="bpr_id_hid" id="bpr_id_hid" value="<?=$bpr_id;?>">
        <input type="hidden" name="bpr_status_signature_id" id="bpr_status_signature_id" value="">
        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => 'Username/Email', 'name'=>'username', 'id'=>'username', 'autofocus'=>'autofocus']) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password'),'name'=>'password', 'id'=>'password']) ?>
		<div class="form-group error" id="logErrorMsg" style="display:none;"></div>
		<div id="login_error_div" class="error" style="display:none;"></div>
        <div class="row">
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
              <div class="col-xs-4">
                <?= Html::Button('Cancel', ['class' => 'btn btn-default btn-block btn-flat', 'name' => 'cancel-button', 'onclick'=>'closethisPopup();']) ?>
            </div>
            <!-- /.col -->
        </div>

        <?php ActiveForm::end(); ?>
         </div>
        
        <div id="HelloSignBtnDiv" class="text-center" style="display:none;padding:15px;">
        	<button type="button" class="btn btn-success" name="HelloSignBtn" id="HelloSignBtn" onclick="javascript:doHelloSign('<?=$equipment_map_id_pk;?>', 'BPR_Equipment_Operator','Approver');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;E-Signature</button>
            <button type="button" class="btn btn-primary" name="HelloSignConfirmBtn" id="HelloSignConfirmBtn" style="display:none;" onclick="confirmHelloSign();"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Confirm</button>
        </div>
		
    </div>
    <!-- /.login-box-body -->

</div>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/jquery.validate.min.js?ver=<?=time();?>"></script>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/equipment_operator_signature.js?ver=<?=time();?>"></script>
