<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\modules\person\models\Person;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\Mprapprovals */
/* @var $form yii\widgets\ActiveForm */
$formname = 'gmp-mprapprovals-form';

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";

$performerChk = ''; $verifierChk = '';

if(!$model->isNewRecord && $model->approval_person_id_fk>0)
	$performerChk = 'checked="checked"'; 
	
if(!$model->isNewRecord && $model->verifier_person_id_fk>0)
	$verifierChk = 'checked="checked"'; 
	
//	check approval or verifer person is exists
$approvalExitsArray = checkCommonFunctions::check_if_person_exists($model->approval_person_id_fk);
$veriferExitsArray = checkCommonFunctions::check_if_person_exists($model->verifier_person_id_fk);

$personsArr = getCommonFunctions::getAllPersonsOfCompany();
$persondpdw = array();
if(is_array($personsArr) && count($personsArr)>0)
{
	foreach($personsArr as $pk=>$pv)
	{
		if(CommonFunctions::isPersonHavingAccessToModule($pv['person_id_pk'], 'RM_BPR'))
		{
			$persondpdw[$pv['person_id_pk']] = $pv['first_name']." ".$pv['last_name'];
		}
	}
}	

//	make readonly approval because his approval status is approved and now nobody can edit his record.
$disabledApprover = "";
$alreadyApproved = '';
$alreadyVerified = '';
if($model->approval_status == 'Approved'){
	$disabledApprover = 'disabled="disabled"';
	$alreadyApproved = "Yes";
}
$disabledVerifier = "";
if($model->verified_status == 'Verified'){
	$disabledVerifier = 'disabled="disabled"';
	$alreadyVerified = "Yes";
}
?>

<div class="mprapprovals-form">

    <?php $form = ActiveForm::begin(['options' => ['id' => $formname,'autocomplete' => 'off', 'enableAjaxValidation' => true]]); ?>
	
    <input type="hidden" name="bpr_approval_id_pk" id="bpr_approval_id_pk" value="<?=$model->bpr_approval_id_pk;?>">
    <input type="hidden" name="bpr_id_fk" id="bpr_id_fk" value="<?=$_GET['bprid'];?>">
    <input type="hidden" name="bpr_alreadyApproved" id="bpr_alreadyApproved" value="<?=$alreadyApproved;?>">
    <input type="hidden" name="bpr_alreadyVerified" id="bpr_alreadyVerified" value="<?=$alreadyVerified;?>">
    
    <div class="row">
    <label class="col-sm-3">Choose Option</label>
    <div class="col-sm-8">
    	<div class="checkbox col-sm-6" style="margin-top:0;padding-left:0;">
          <label><input type="checkbox" value="Yes" name="performerChk" id="performerChk" <?=$performerChk;?> <?=$disabledApprover?> onchange="javascript:showHideFields('Performer');">Approver</label>
        </div>
        <div class="checkbox col-sm-6" style="margin-top:0;padding-left:0;">
          <label><input type="checkbox" value="Yes" name="verifierChk" id="verifierChk" <?=$verifierChk;?> <?=$disabledVerifier?> onchange="javascript:showHideFields('Verifier');">Verifier</label>
        </div>
    </div>
    </div>
    
    <?php if(!$model->isNewRecord){ 
		$disp = "";
		if($model->approval_person_id_fk>0)
			$disp = "display:block;";
		else
			$disp = "display:none;";
	?>
    <div id="approverDiv" style="<?=$disp;?>" class="row">
    <?php } else { ?>
    <div id="approverDiv" style="display:none;" class="row">
    <?php } ?>
    
    <?php if($approvalExitsArray['exists'] == 'No'){ //if approver person is not exists then show message to user?>
        <label class="col-sm-3"></label>
        <div class="col-sm-8"><p class="form-control-static text-muted">[Person "<?=$approvalExitsArray['fullname']?>" was not found, select other person as Approver]</p></div>
    <?php }?>
    
    <label class="col-sm-3">BPR Approver<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<? if($disabledApprover == 'disabled="disabled"'){?>
        	<?= $form->field($model, 'approval_person_id_fk')->dropDownList($persondpdw,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'approval_person_id_fk','name'=>'approval_person_id_fk','disabled' => 'disabled'])->label(false); ?>
    	<? } else {?>
        	<?= $form->field($model, 'approval_person_id_fk')->dropDownList($persondpdw,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'approval_person_id_fk','name'=>'approval_person_id_fk'])->label(false); ?>
        <? } ?>
    </div>

	<label class="col-sm-3">Job Function<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<? if($disabledApprover == 'disabled="disabled"'){?>
        	<?= $form->field($model, 'approval_job_function')->textInput(['name'=>'approval_job_function','id'=>'approval_job_function','maxlength' => true,'disabled' => 'disabled'])->label(false); ?>
        <? } else {?>
        	<?= $form->field($model, 'approval_job_function')->textInput(['name'=>'approval_job_function','id'=>'approval_job_function','maxlength' => true])->label(false); ?>
		<? } ?>
    	
    </div>
	</div>
    
     <?php if(!$model->isNewRecord){ 
		$disp = "";
		if($model->verifier_person_id_fk>0)
			$disp1 = "display:block;";
		else
			$disp1 = "display:none;";
	?>
    <div id="verifierDiv" style="<?=$disp1;?>" class="row">
    <?php } else { ?>
    <div id="verifierDiv" style="display:none;" class="row">
    <?php } ?>
    
    <?php if($veriferExitsArray['exists'] == 'No'){ //	if verifer person is not exists then show message to user?>
        <label class="col-sm-3"></label>
        <div class="col-sm-8"><p class="form-control-static text-muted">[Person "<?=$veriferExitsArray['fullname']?>" was not found, select other person as Verifier]</p></div>
    <?php }?>
    
  	<label class="col-sm-3">BPR Verifier<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<? if($disabledVerifier == 'disabled="disabled"'){?>
        	<?= $form->field($model, 'verifier_person_id_fk')->dropDownList($persondpdw,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'verifier_person_id_fk','name'=>'verifier_person_id_fk','disabled' => 'disabled'])->label(false); ?>
        <? } else {?>
        	<?= $form->field($model, 'verifier_person_id_fk')->dropDownList($persondpdw,['prompt'=>'--- Select One ---','class'=>'form-control','id'=>'verifier_person_id_fk','name'=>'verifier_person_id_fk'])->label(false); ?>
        <? } ?>
    </div>

	<label class="col-sm-3">Job Function<span class="text-danger">&nbsp;*&nbsp;</span></label>
    <div class="col-sm-8">
    	<? if($disabledVerifier == 'disabled="disabled"'){?>
        	<?= $form->field($model, 'verifier_job_function')->textInput(['name'=>'verifier_job_function','id'=>'verifier_job_function','maxlength' => true,'disabled' => 'disabled'])->label(false); ?>
        <? } else {?>
        	<?= $form->field($model, 'verifier_job_function')->textInput(['name'=>'verifier_job_function','id'=>'verifier_job_function','maxlength' => true])->label(false); ?>
        <? } ?>
    </div>
	</div>
    
     <?php if(!$model->isNewRecord){ ?>
    <div class="row" id="submitbtnDiv">
    <?php } else { ?>
	<div class="row" id="submitbtnDiv" style="display:none;">
    <?php } ?>
    	<label class="col-sm-3">&nbsp;</label>
        <div class="form-group col-sm-8">
        	<div> 
            <?php if($fromAuditLog!="Yes") { ?>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary']) ?>
            <span class="text-muted">&nbsp; OR &nbsp;</span>
            <a href="<?= \yii\helpers\Url::to([$backUrl]) ?>" class="text-primary" style="text-decoration:underline;">Cancel</a>
            <?php } else{ ?>
            <?= Html::submitButton($model->isNewRecord ? 'ADD' : 'UPDATE', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary', 'disabled'=>'disabled']) ?>
            <?php } ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/jquery.validate.min.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile(Yii::$app->homeUrl.'js/bprapprovals.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
