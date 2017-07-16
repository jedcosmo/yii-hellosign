<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\minstructions\models\Minstructions;

$MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($BPRmodel->bpr_id_pk,$mInstructionSteps['mi_id_pk']);

$backUrl = '/bprrecords/bprrecords/view?id='.$BPRmodel->bpr_id_pk.'&tab=manufacturingInst';
$this->title = "MANUFACTURING INSTRUCTION DETAILS";
$this->params['breadcrumbs'][] = ['label' => 'Manufacturing Instructions', 'url' => [$backUrl]];
$this->params['breadcrumbs'][] = 'Manufacturing Instruction Details';

?>
<div class="bprrecords-view-details">
<div class="modal-header text-primary" style="padding:5px 10px!important;">
   <button type="button" class="close" data-dismiss="modal" >&times;</button>
   <h4 class="modal-title">BPR INSTRUCTIONS</h4>
</div>
<div class="modal-body" style="padding:5px!important;overflow-y:auto;">
<table class="table table-bordered" cellpadding="5" cellspacing="5" width="100%">
	<tr>
    	<td>
        	<label class="control-label">Product Code:</label>
            <span style="margin-left:20px;"><?=wordwrap($BPRmodel->product_code,20,"<br/>",1);?></span>
        </td>
        <td>
        	<label class="control-label">Product Name:</label>
            <span style="margin-left:20px;"><?=wordwrap($MPRDetails['product_name'],20,"<br/>",1);?></span>
        </td>
        <td>
        	<label class="control-label">Product Strength:</label>
            <span style="margin-left:20px;"><?=wordwrap($MPRDetails['product_strength'],20,"<br/>",1);?></span>
        </td>
        <td>
        	<label class="control-label">Batch #:</label>
            <span style="margin-left:20px;"><?=wordwrap($BPRmodel->batch,20,"<br/>",1);?></span>
        </td>
    </tr>

    <tr>
    	<td>
        	<label class="control-label">Step:</label>
            <span style="margin-left:20px;"><?=wordwrap($mInstructionSteps['mi_step'],20,"<br/>",1);?></span>
        </td>
        <td colspan="3">
        	<label class="control-label">Action:</label>
            <span style="margin-left:20px;"><?=wordwrap($mInstructionSteps['mi_action'],70,"<br/>",1);?></span>
        </td>
    </tr>
    
    <tr>
    	<td>&nbsp;</td>
       	<td>
        	<label class="control-label">Target:</label>
            <span style="margin-left:20px;"><?=wordwrap($mInstructionSteps['target'],20,"<br/>",1);?></span>
        </td>
        <td>
        	<label class="control-label">Range:</label>
            <span style="margin-left:20px;"><?=wordwrap($mInstructionSteps['mi_range'],20,"<br/>",1);?></span>
        </td>
        <td>
        	<label class="control-label">Unit:</label>
            <span style="margin-left:20px;"><?=wordwrap(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$mInstructionSteps['unit_id_fk']),20,"<br/>",1);?></span>
        </td>
    </tr>
    
    <tr>
    	<td>
        	<label class="control-label">Result:</label>
        </td>
        <td colspan="3">
        	<?php if($MiApprovalDetails['result'])
						echo wordwrap($MiApprovalDetails['result'],70,"<br/>",1);
				  else
				  		echo "---";
        	?>
        </td>
    </tr>
    
    <tr>
    	<td>
        	<label class="control-label">Comments:</label>
        </td>
        <td colspan="3">
        	<?php if($MiApprovalDetails['comments'])
						echo wordwrap($MiApprovalDetails['comments'],70,"<br/>",1);
				  else
				  		echo "---";
        	?>
        </td>
    </tr>
    
    <tr>
    	<td>
        	<label class="control-label">Deviation Comment:</label>
        </td>
        <td colspan="3">
        	<?php if($MiApprovalDetails['deviation_comments'])
						echo wordwrap($MiApprovalDetails['deviation_comments'],70,"<br/>",1);
				  else
				  		echo "---";
        	?>
        </td>
    </tr>
    
    
   
    <tr>
    	<td><label class="control-label">Performed By:</label></td>
        <td colspan="3">
        	<?php 
			if($mInstructionSteps['perfomer']=='Yes'){
			 if($MiApprovalDetails['arppover_person_id_fk']>0 && $MiApprovalDetails['approval_datetime']!='0000-00-00 00:00:00'){ 
            $personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['arppover_person_id_fk']);
			?>
        	<small><span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['approval_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }else{ echo 'Pending'; }
			} else { echo 'Not Required'; }
			 ?>
        </td>
   </tr>
   
    <?php if($MiApprovalDetails['deviation_comments']!=''){ ?>
   <tr>
   		<td><label class="control-label">QA Reviewed By:</label></td>
        <td colspan="3">
        	<?php if($MiApprovalDetails['QA_person_id_fk']>0 && $MiApprovalDetails['QA_datetime']!='0000-00-00 00:00:00'){ 
					$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['QA_person_id_fk']);
			?>
            <small><span class="text-muted"><span class="glyphicon glyphicon-thumbs-up"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['QA_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php } ?>
        </td>
    </tr>
   <?php } ?>
  
   <tr>
   		<td><label class="control-label">Verified By:</label></td>
        <td colspan="3">
        	<?php 
			if($mInstructionSteps['verifier']=='Yes'){
			if($MiApprovalDetails['verifier_person_id_fk']>0 && $MiApprovalDetails['verified_datetime']!='0000-00-00 00:00:00'){ 
					$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['verifier_person_id_fk']);
			?>
            <small><span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['verified_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }else{ echo 'Pending'; } 
			}else{ echo 'Not Required'; }
			?>
        </td>
    </tr>
   
   
   <?php if($mInstructionSteps['perfomer']=='No' && $mInstructionSteps['verifier']=='No' && $MiApprovalDetails['no_person_id_fk']>0){ ?>
   <tr>
   		<td><label class="control-label">Confirmed By:</label></td>
        <td colspan="3">
        	<?php if($MiApprovalDetails['no_person_id_fk']>0 && $MiApprovalDetails['no_datetime']!='0000-00-00 00:00:00'){ 
					$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['no_person_id_fk']);
			?>
            <small><span class="text-muted"><span class="glyphicon glyphicon-ok"></span>&nbsp;&nbsp;<?=$personDetails['first_name']." ".$personDetails['last_name']." ( ".$personDetails['user_name_person']." )    ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['no_datetime'])).Yii::$app->params['timezoneVar']; ?></span></small>
            <?php }else{ echo 'Pending'; } ?>
        </td>
    </tr>
   <?php } ?>
  
   
</table>
</div>
</div>
