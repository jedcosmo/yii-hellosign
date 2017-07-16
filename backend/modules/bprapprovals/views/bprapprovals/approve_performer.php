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

$bpr_appr_id = ($_REQUEST['bpr_appr_id']?$_REQUEST['bpr_appr_id']:0);
?>

<div class="modal-header text-primary">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h5 class="modal-title">Executed BPR Approvals</h5>
</div>
<div class="modal-body">
	<input type="hidden" name="module_primary_id" id="module_primary_id" value="<?=$bpr_appr_id;?>">
    <input type="hidden" name="module_name" id="module_name" value="BPR_Approvals">
    <input type="hidden" name="masterBPR" id="masterBPR" value="<?=$masterBPR;?>">
    <input type="hidden" name="signType" id="signType" value="<?=$signType;?>">
    <div class="login-box-body">
    	<?php  if(isset($outstandingSteps) && is_array($outstandingSteps) && ($outstandingSteps['performer']!='' || $outstandingSteps['verifier']!='' || $outstandingSteps['QAOutstanding']!='' || $eqpExpiredflag=="Yes" || $prdDeletedflag=="Yes" || count($outstandingEqps)>0)) { ?>
        <div class="alert alert-danger">
        	<span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;&nbsp;Approval process can't proceed due to following: <br/>
            <?php if($prdDeletedflag=="Yes") { ?>
            	&bull; Product related to this BPR is deleted. <br/>
            <?php } ?>
            <?php if($eqpExpiredflag=="Yes"){ ?>
            	&bull; Equipment related to this BPR is either expired or deleted. <br/>
            <?php } ?> 
            <?php if($outstandingSteps['performer']!=''){ ?>
            	&bull; Performer outstanding Step(s) # : <?=wordwrap($outstandingSteps['performer'],30,"<br/>",1);?> <br/>
            <?php } ?>
            <?php if($outstandingSteps['verifier']!=''){ ?>
            	&bull; Verifier outstanding Step(s) # : <?=wordwrap($outstandingSteps['verifier'],30,"<br/>",1);?> <br/>
            <?php } ?>
            <?php if($outstandingSteps['QAOutstanding']!=''){ ?>
            	&bull; QA Review outstanding Step(s) # : <?=wordwrap($outstandingSteps['QAOutstanding'],30,"<br/>",1);?> <br/>
            <?php } ?>
            
             <?php if(isset($outstandingEqps['equipment_ids']) && $outstandingEqps['equipment_ids']!=''){ ?>
            	&bull; Operator signature pending for Equipment# : <?=wordwrap($outstandingEqps['equipment_ids'],30,"<br/>",1);?> <br/>
            <?php } ?>
            
        </div>
        <?php }else{ ?>
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
        	<button type="button" class="btn btn-success" name="HelloSignBtn" id="HelloSignBtn" onclick="javascript:doHelloSign('<?=$bpr_appr_id;?>', 'BPR_Approvals','<?=$signType;?>');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;&nbsp;E-Signature</button>
            <button type="button" class="btn btn-primary" name="HelloSignConfirmBtn" id="HelloSignConfirmBtn" style="display:none;" onclick="confirmHelloSign();"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;Confirm</button>
        </div>
		<?php } ?>
    </div>
    <!-- /.login-box-body -->

</div>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/jquery.validate.min.js?ver=<?=time();?>"></script>
<script type="text/javascript" src="<?=Yii::$app->homeUrl?>js/bprperformerlogin.js?ver=<?=time();?>"></script>
<script>
$('#myModal').on('hidden.bs.modal', function () {
    $("#performerChk_" + <?=$bpr_appr_id?>).prop('checked', false); // Unchecks it
	 $("#verifierChk_" + <?=$bpr_appr_id?>).prop('checked', false); // Unchecks it
})
</script>
