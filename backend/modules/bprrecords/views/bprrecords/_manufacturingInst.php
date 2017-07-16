<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\minstructions\models\Minstructions;

$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_pk);
$readonly = '';
if($rflag=="Yes")
{
	$readonly = 'disabled="disabled"';
}

$commonModel = new getCommonFunctions();

$fromAuditLog = (isset($_REQUEST['al']) && $_REQUEST['al']==1)?"Yes":"No";
$visibleFlag = true;
if($fromAuditLog=="Yes") $visibleFlag = false;

$session = Yii::$app->session;
$bprSuccess = $session->get('bprSuccess');
$bprError = $session->get('bprError');
$session->remove('bprSuccess');
$session->remove('bprError');
if(isset($bprSuccess) && $bprSuccess!=''){ ?>
	<div id="msgE" class="alert alert-success">
	  <strong>Success!</strong>&nbsp; <?=$bprSuccess;?>
	</div>
<?php }elseif(isset($bprError) && $bprError!=''){ ?>
	<div id="msgE" class="alert alert-danger">
	   <?=$bprError;?>
	</div>
<?php } ?>
<table class="table table-bordered table-striped" cellpadding="5" cellspacing="5" width="100%">
<?php
$tdClass = '';
if(is_array($mInstructionSteps) && count($mInstructionSteps)>0)
{
	foreach($mInstructionSteps as $k=>$v)
	{
		$MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($model->bpr_id_pk,$v['mi_id_pk']);
		if($rflag!="Yes" && $fromAuditLog!="Yes")
		{
			$actmember = '';
			$href = 'javascript:void(0)';
			$onfunction2 = '';
			if($v['perfomer']=='Yes' && $MiApprovalDetails['arppover_person_id_fk']<=0)
				$actmember = 'performer';
			elseif($MiApprovalDetails['deviation_comments']!='' && $MiApprovalDetails['QA_person_id_fk']<=0)
				$actmember = 'QAPerson';
			elseif($v['verifier']=='Yes' && $MiApprovalDetails['verifier_person_id_fk']<=0)
				$actmember = 'verifier';
			elseif($v['perfomer']=='No' && $v['verifier']=='No' && $MiApprovalDetails['no_person_id_fk']<=0)
			{
				$actmember = 'noone';
				/*$actmember = '';
				$queryString = ($fromAuditLog == "Yes"?"&al=1":"");
				$href = Yii::$app->homeUrl.'bprrecords/bprrecords/manufacturing_inst_view?id='.$v['mi_id_pk'].'&bprid='.$model->bpr_id_pk.'&mode=bpr'.$queryString;	
				$onfunction2 = "showInstructionViewPopup('".$v['mi_id_pk']."','".$model->bpr_id_pk."');";*/
			}
			else
			{
				$queryString = ($fromAuditLog == "Yes"?"&al=1":"");
				$href = Yii::$app->homeUrl.'bprrecords/bprrecords/manufacturing_inst_view?id='.$v['mi_id_pk'].'&bprid='.$model->bpr_id_pk.'&mode=bpr'.$queryString;	
				$onfunction2 = "showInstructionViewPopup('".$v['mi_id_pk']."','".$model->bpr_id_pk."');";
			}
			
			$onfunction = '';
			if($actmember!='')
			{
				$tdClass = '';
				$onfunction = "showPerformerPopup('".$actmember."','".$v['mi_id_pk']."','".$model->bpr_id_pk."');";
			}
			else
			{
				$tdClass = 'green';
			}
		}
		else{
			if($MiApprovalDetails['arppover_person_id_fk']>0 || $MiApprovalDetails['verifier_person_id_fk']>0 || $MiApprovalDetails['QA_person_id_fk']>0 || $MiApprovalDetails['no_person_id_fk']>0)
				$tdClass = 'green';
			else
				$tdClass = '';
				
			$onfunction = '';
			$queryString = ($fromAuditLog == "Yes"?"&al=1":"");
			$href = Yii::$app->homeUrl.'bprrecords/bprrecords/manufacturing_inst_view?id='.$v['mi_id_pk'].'&bprid='.$model->bpr_id_pk.'&mode=bpr'.$queryString;	
			$onfunction2 = "showInstructionViewPopup('".$v['mi_id_pk']."','".$model->bpr_id_pk."');";
		}
		?>
        <tr <?php if($tdClass == 'green'){ ?> style="color:#00a65a !important;" <?php } ?>>
        	<td width="3%" <?php if($MiApprovalDetails['deviation_comments']!='' && $MiApprovalDetails['QA_person_id_fk']<=0){?> style="color:#FF0000;"<?php } ?>>
           	<?php 
			if($fromAuditLog!="Yes"){ ?>
            <a href="javascript:void(0);" onclick="<?php if($onfunction!=''){ echo $onfunction; }else{ echo $onfunction2; }?>"><span class="glyphicon glyphicon-eye-open"></span></a>
          <?php } ?>
           </td>
            <td width="97%" <?php if($MiApprovalDetails['deviation_comments']!='' && $MiApprovalDetails['QA_person_id_fk']<=0){?> style="color:#FF0000;"<?php } ?>>
            	<div class="row">
                	<label class="control-label col-sm-2 col-md-1 col-xs-2">Step:</label>
                	<div class="col-sm-9 col-md-10 col-xs-9" style="word-wrap:break-word;"><?=wordwrap(htmlentities($v['mi_step']),80,"<br/>",1);?></div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                    
                    <label class="control-label col-sm-2 col-md-1 col-xs-2">Action:</label>
                	<div class="col-sm-9 col-md-10 col-xs-9" style="word-wrap:break-word;"><?=wordwrap(htmlentities($v['mi_action']),80,"<br/>",1);?></div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                    
                    <label class="control-label col-sm-2 col-md-1 col-xs-2">Result:</label>
                	<div class="col-sm-9 col-md-10 col-xs-9" style="word-wrap:break-word;"><?=wordwrap(htmlentities($MiApprovalDetails['result']),80,"<br/>",1);?></div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                    
                    <label class="control-label col-sm-2 col-md-1 col-xs-2">Comments:</label>
                	<div class="col-sm-9 col-md-10 col-xs-9" style="word-wrap:break-word;"><?=wordwrap(htmlentities($MiApprovalDetails['comments']),80,"<br/>",1);?></div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                    
                    <?php if($MiApprovalDetails['deviation_comments']!=''){ ?>
                    <label class="control-label col-sm-1 col-md-1 col-xs-1">Deviation:</label>
                	<div class="col-sm-10 col-md-10 col-xs-10" style="word-wrap:break-word;"><?=wordwrap(htmlentities($MiApprovalDetails['deviation_comments']),80,"<br/>",1);?></div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                    <?php } ?>
                    
                    <label class="control-label col-sm-6 col-md-2 col-xs-6">Performer:</label>
                	<div class="col-sm-6 col-md-2 col-xs-6 pvdPadding">
                    	 <label><input type="checkbox" value="" onclick="showPerformerPopup(this,'performer','<?=$v['mi_id_pk'];?>','<?=$model->bpr_id_pk;?>');" disabled="disabled" <?php if($v['perfomer']=='Yes'){ ?> checked="checked" <?php } ?>></label>
                    	<?php if($MiApprovalDetails['arppover_person_id_fk']>0 && $MiApprovalDetails['approval_datetime']!='0000-00-00 00:00:00'){ 
								$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['arppover_person_id_fk']);
						?>
                        	<small><span class="text-muted">&nbsp;&nbsp;<?=htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." )<br/><span class='gmpMarginLft'>".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['approval_datetime'])).Yii::$app->params['timezoneVar']; ?></span></span></small>
                           <?php if($MiApprovalDetails['HS_approver_signature_id']!=""){ 
						   	echo "<br/><span class='gmpMarginLft'>".getCommonFunctions::getSignedDocFromSignatureId($MiApprovalDetails['HS_approver_signature_id'])."</span>";
							} ?>
                        <?php } ?>
                    </div>
              
                    <label class="control-label col-sm-6 col-md-2 col-xs-6">Verifier:</label>
                    <div class="col-sm-6 col-md-2 col-xs-6 pvdPadding">
                    	<label><input type="checkbox" value="" onclick="showPerformerPopup(this,'verifier','<?=$v['mi_id_pk'];?>','<?=$model->bpr_id_pk;?>');" disabled="disabled" <?php if($v['verifier']=='Yes'){ ?> checked="checked" <?php } ?>></label>
                    	<?php if($MiApprovalDetails['verifier_person_id_fk']>0 && $MiApprovalDetails['verified_datetime']!='0000-00-00 00:00:00'){ 
								$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['verifier_person_id_fk']);
						?>
                        	<small><span class="text-muted">&nbsp;&nbsp;<?=htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." )<br/><span class='gmpMarginLft'>".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['verified_datetime'])).Yii::$app->params['timezoneVar']; ?></span></span></small>
                            <?php if($MiApprovalDetails['HS_verifier_signature_id']!=''){ 
						   	echo "<br/><span class='gmpMarginLft'>".getCommonFunctions::getSignedDocFromSignatureId($MiApprovalDetails['HS_verifier_signature_id'])."</span>"; 
							} ?>
                        <?php } ?>
                    </div>
                    
                    <label class="control-label col-sm-6 col-md-2 col-xs-6">Deviation:</label>
                    <div class="col-sm-6 col-md-2 col-xs-6 pvdPadding">
                    	<label><input type="checkbox" value="" onclick="showPerformerPopup(this,'QAPerson','<?=$v['mi_id_pk'];?>','<?=$model->bpr_id_pk;?>');" disabled="disabled" <?php if($MiApprovalDetails['deviation_comments']!=''){ ?> checked="checked" <?php } ?>></label>
                    	<?php if($MiApprovalDetails['QA_person_id_fk']>0 && $MiApprovalDetails['QA_datetime']!='0000-00-00 00:00:00'){ 
								$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['QA_person_id_fk']);
						?>
                        	<small><span class="text-muted">&nbsp;&nbsp;<?=htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." )<br/><span class='gmpMarginLft'>".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['QA_datetime'])).Yii::$app->params['timezoneVar']; ?></span></span></small>
                         <?php if($MiApprovalDetails['HS_reviewer_signature_id']!=''){ 
						   	echo "<br/><span class='gmpMarginLft'>".getCommonFunctions::getSignedDocFromSignatureId($MiApprovalDetails['HS_reviewer_signature_id'])."</span>"; 
							} ?>
						<?php } ?>
                    </div>
                    <div class="col-sm-12 col-md-12 col-xs-12"><br/></div>
                </div>
            </td>
        </tr>
        <?php
	}
}else{ ?>
<tr>
	<td colspan="2">No records found.</td>
</tr>
<?php } ?>
</table>
<style type="text/css">
.pvdPadding{ padding-right:0px!important; }
.gmpMarginLft{ padding-left:20px!important; }
</style>
<?php 
$this->registerJsFile(Yii::$app->homeUrl.'js/bprmaster.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
