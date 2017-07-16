<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
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

$mpr_appr_id = ($_REQUEST['mpr_appr_id']?$_REQUEST['mpr_appr_id']:0);

$errorTextMessage = '';
if($eqpExpiredflag=="Yes") 
	$errorTextMessage .= "<br/>&bull; Equipment related to this MPR is either expired or deleted.";
	
if($prdDeletedflag=="Yes") 
	$errorTextMessage .= "<br/>&bull; Product related to this MPR is deleted.";
	
if($BOMFlag!="Yes")
	$errorTextMessage .= "<br/>&bull; Bill of Materials are not yet added for this MPR.";
	
if($EqpsFlag!="Yes")
	$errorTextMessage .= "<br/>&bull; Equipments are not yet added for this MPR.";
	
if($MiStepsFlag!="Yes")
	$errorTextMessage .= "<br/>&bull; Manufacturing Steps are not yet added for this MPR.";
	
if($bomsRejectedFlag=="Yes")
	$errorTextMessage .= "<br/>&bull; Bill of Materials are pending for status Approval.";
	
	
$errorTextMessage = trim($errorTextMessage,"<br/>");
?>
<div class="modal-header text-primary">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h5 class="modal-title">MPR Approvals</h5>
</div>
<div class="modal-body">
	<input type="hidden" name="module_primary_id" id="module_primary_id" value="<?=$mpr_appr_id;?>">
    <input type="hidden" name="module_name" id="module_name" value="MPR_Approvals">
    <input type="hidden" name="masterMPR" id="masterMPR" value="<?=$masterMPR;?>">
    <input type="hidden" name="signType" id="signType" value="<?=$signType;?>">
    <div class="login-box-body">
    	<?php if($errorTextMessage!=''){ ?>
    		<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Approval process can't proceed due to following:<br/><?=$errorTextMessage;?></div>
        <?php } else{ ?>
     	<div id="loginFormDiv">        
        <?php $form = ActiveForm::begin(['options' => ['id' => 'login_form','name'=>'login_form', 'enableClientValidation' => false, 'autocomplete'=> 'off']]); ?>

        <?= $form
            ->field($model, 'username', $fieldOptions1)
            ->label(false)
            ->textInput(['placeholder' => 'Username/Email', 'name'=>'username', 'id'=>'username', 'autofocus'=>'autofocus']) ?>

        <?= $form
            ->field($model, 'password', $fieldOptions2)
            ->label(false)
            ->passwordInput(['placeholder' => $model->getAttributeLabel('password'),'name'=>'password', 'id'=>'password']) ?>
		<div class="form-group error" id="logErrorMsg" style="display:none;"></div>
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
        	<button type="button" class="btn btn-success" name="HelloSignBtn" id="HelloSignBtn" onclick="javascript:doHelloSign('<?=$mpr_appr_id;?>', 'MPR_Approvals','<?=$signType;?>');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;E-Signature</button>
            <button type="button" class="btn btn-primary" name="HelloSignConfirmBtn" id="HelloSignConfirmBtn" style="display:none;" onclick="confirmHelloSign();"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Confirm</button>
        </div>
		<?php } ?>
    </div>
    <!-- /.login-box-body -->

</div>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/jquery.validate.min.js?ver=<?=time();?>"></script>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/mprperformerlogin.js?ver=<?=time();?>"></script>
<script>
$('#myModal').on('hidden.bs.modal', function () {
    $("#performerChk_" + <?=$mpr_appr_id?>).prop('checked', false); // Unchecks it
	 $("#verifierChk_" + <?=$mpr_appr_id?>).prop('checked', false); // Unchecks it
})
</script>
