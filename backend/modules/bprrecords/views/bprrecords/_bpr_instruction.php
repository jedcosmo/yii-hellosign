<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\minstructions\models\Minstructions;


$MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($BPRmodel->bpr_id_pk,$mInstructionSteps['mi_id_pk']);

$unitClass = ''; $unitTitle = '';
$isUnitDeleted = checkCommonFunctions::check_If_Record_Deleted($mInstructionSteps['unit_id_fk'],'bpr_unit','unit_id_pk');
if($isUnitDeleted=="Yes")
{
	$unitClass = 'error';
	$unitTitle = 'This unit is deleted';
}
?>
<div id="bpr_inst_div">
<div class="modal-header text-primary" style="padding:5px 10px!important;">
   <button type="button" class="close" data-dismiss="modal" onclick="javascript:closeBprInstructionPopup();">&times;</button>
   <h4 class="modal-title">BPR INSTRUCTIONS</h4>
</div>
<div class="modal-body" style="padding:5px!important;height:530px;overflow-y:auto;">
<?php $form = ActiveForm::begin(['options' => ['id' => 'bpr_instructions','name'=>'bpr_instructions', 'enableClientValidation' => false, 'autocomplete'=> 'off']]); ?>
<input type="hidden" name="bpr_id_pk" id="bpr_id_pk" value="<?=$BPRmodel->bpr_id_pk;?>">
<input type="hidden" name="mpr_definition_id_fk" id="mpr_definition_id_fk" value="<?=$BPRmodel->mpr_definition_id_fk;?>">
<input type="hidden" name="mi_id_pk" id="mi_id_pk" value="<?=$mInstructionSteps['mi_id_pk'];?>">
<input type="hidden" name="act_member_hid" id="act_member_hid" value="<?=$actmember;?>">

<table class="table table-bordered" cellpadding="2" cellspacing="2" width="100%">
	<tr>
    	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Product Code:</label>
            <span style="margin-left:5px;"><?=wordwrap($BPRmodel->product_code,20,"<br/>",1);?></span>
        </td>
        <td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Product Name:</label>
            <span style="margin-left:5px;"><?=wordwrap($MPRDetails['product_name'],20,"<br/>",1);?></span>
        </td>
        <td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Product Strength:</label>
            <span style="margin-left:5px;"><?=wordwrap($MPRDetails['product_strength'],20,"<br/>",1);?></span>
        </td>
        <td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Batch #:</label>
            <span style="margin-left:5px;"><?=wordwrap($BPRmodel->batch,20,"<br/>",1);?></span>
        </td>
    </tr>
    
    <tr>
    	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Step:</label>
            <div style="margin-left:5px;word-wrap:break-word;"><?=wordwrap($mInstructionSteps['mi_step'],20,"<br/>",1);?></div>
        </td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<label class="control-label">Action:</label>
            <span style="margin-left:5px;"><?=wordwrap($mInstructionSteps['mi_action'],70,"<br/>",1);?></span>
        </td>
    </tr>
    
    <tr>
    	<td width="25%">&nbsp;</td>
       	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Target:</label>
            <span style="margin-left:5px;"><?=wordwrap($mInstructionSteps['target'],20,"<br/>",1);?></span>
        </td>
        <td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Range:</label>
            <span style="margin-left:5px;"><?=wordwrap($mInstructionSteps['mi_range'],20,"<br/>",1);?></span>
        </td>
        <td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Unit:</label>
            <span style="margin-left:5px;" class='<?=$unitClass;?>' title='<?=$unitTitle;?>'><?=wordwrap(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$mInstructionSteps['unit_id_fk']),20,"<br/>",1);?></span>
        </td>
    </tr>
    
    <tr>
    	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Result:</label>
        </td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<textarea rows="2" class="form-control" name="result" id="result" maxlength="200"><?=$MiApprovalDetails['result'];?></textarea>
        </td>
    </tr>
    
    <tr>
    	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Comments:</label>
        </td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<textarea rows="2" class="form-control" name="comments" id="comments" maxlength="200"><?=$MiApprovalDetails['comments'];?></textarea>
        </td>
    </tr>
    
    <tr>
    	<td width="25%" style="word-wrap:break-word;">
        	<label class="control-label">Deviation Comment:</label>
        </td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        <?php if($MiApprovalDetails['QA_person_id_fk']>0 && $MiApprovalDetails['QA_datetime']!='0000-00-00 00:00:00'){ ?>
        	<textarea rows="2" class="form-control" name="deviation_comments" id="deviation_comments" maxlength="200" readonly="readonly"><?=$MiApprovalDetails['deviation_comments'];?></textarea>
        <?php }else{ ?>
        	<textarea rows="2" class="form-control" name="deviation_comments" id="deviation_comments" maxlength="200"><?=$MiApprovalDetails['deviation_comments'];?></textarea>
        <?php } ?>
        </td>
    </tr>
    
    
    <?php if($MiApprovalDetails['deviation_comments']!=''){ ?>
   <tr>
   		<td width="25%" style="word-wrap:break-word;"><label class="control-label">QA Signature Required:</label></td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<?php if($MiApprovalDetails['QA_person_id_fk']>0 && $MiApprovalDetails['QA_datetime']!='0000-00-00 00:00:00'){ 
					$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['QA_person_id_fk']);
			?>
            <small><span class="text-muted"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['QA_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }elseif($actmember=='QAPerson'){ ?>
            <input type="hidden" name="performer_id_fk" id="performer_id_fk" value="">
        	<div id="sign_btn_div">
        		<button type="button" class="btn btn-sm btn-success" onclick="javascript:sign_bpr_mi('<?=$BPRmodel->bpr_id_pk;?>','<?=$BPRmodel->mpr_definition_id_fk;?>','<?=$mInstructionSteps['mi_id_pk'];?>','<?=$signType;?>');"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;Review</button>
            </div>
            <div id="signed_user_div" style="display:none;"></div>
            <?php }else{ ?> <span class="text-muted">Pending</span> <?php } ?>
        </td>
    </tr>
   <?php } ?> 
    
    <?php if($mInstructionSteps['perfomer']=='Yes'){ ?>
    <tr>
    	<td width="25%" style="word-wrap:break-word;"><label class="control-label">Performed By:</label></td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<?php 
			 if($MiApprovalDetails['arppover_person_id_fk']>0 && $MiApprovalDetails['approval_datetime']!='0000-00-00 00:00:00'){ 
            $personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['arppover_person_id_fk']);
			?>
        	<small><span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['approval_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }elseif($actmember=='performer'){ ?>
            <input type="hidden" name="performer_id_fk" id="performer_id_fk" value="">
        	<div id="sign_btn_div">
        		<button type="button" class="btn btn-sm btn-success" onclick="javascript:sign_bpr_mi('<?=$BPRmodel->bpr_id_pk;?>','<?=$BPRmodel->mpr_definition_id_fk;?>','<?=$mInstructionSteps['mi_id_pk'];?>','<?=$signType;?>');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Sign</button>
            </div>
            <div id="signed_user_div" style="display:none;"></div>
            <?php } else{ ?> <span class="text-muted">Pending</span> <?php } ?>
        </td>
   </tr>
   <?php } ?>
    
	<?php if($mInstructionSteps['verifier']=='Yes'){ ?>
   <tr>
   		<td width="25%" style="word-wrap:break-word;"><label class="control-label">Verified By:</label></td>
        <td width="75%" colspan="3" style="word-wrap:break-word;">
        	<?php if($MiApprovalDetails['verifier_person_id_fk']>0 && $MiApprovalDetails['verified_datetime']!='0000-00-00 00:00:00'){ 
					$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['verifier_person_id_fk']);
			?>
            <small><span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['verified_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }elseif($actmember=='verifier'){ ?>
            <input type="hidden" name="performer_id_fk" id="performer_id_fk" value="">
        	<div id="sign_btn_div">
        		<button type="button" class="btn btn-sm btn-success" onclick="javascript:sign_bpr_mi('<?=$BPRmodel->bpr_id_pk;?>','<?=$BPRmodel->mpr_definition_id_fk;?>','<?=$mInstructionSteps['mi_id_pk'];?>','<?=$signType;?>');"><span class="glyphicon glyphicon-pencil"></span>&nbsp;Sign</button>
            </div>
            <div id="signed_user_div" style="display:none;"></div>
            <?php }else{ ?> <span class="text-muted">Pending</span> <?php } ?>
        </td>
    </tr>
   <?php } ?>    
    <tr>
    	<td width="25%">&nbsp;</td>
        <td colspan="3" align="left">
        	<?php if($actmember=='noone'){ ?>
            <input type="hidden" name="performer_id_fk" id="performer_id_fk" value="<?=Yii::$app->user->id;?>">
            <button type="button" class="btn btn-primary btn-flat" id="confirm_btn" onclick="javascript:confirmBprStep('<?=$actmember;?>');"><span class="glyphicon glyphicon-ok"></span>&nbsp;Confirm</button>
            <?php } else { ?>
        	<button type="button" class="btn btn-primary btn-flat" id="confirm_btn" disabled="disabled" onclick="javascript:confirmBprStep('<?=$actmember;?>');"><span class="glyphicon glyphicon-ok"></span>&nbsp;Confirm</button>
            <?php } ?>
            <button type="button" class="btn btn-default btn-flat" onclick="javascript:closeBprInstructionPopup();">Close</button>
        </td>
    </tr>
    
</table>
	 <?php ActiveForm::end(); ?>
</div>
</div>
<script type="text/javascript" src="<?=Yii::$app->homeUrl;?>js/bprmaster.js"></script>
