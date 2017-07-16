<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
 
$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($model->mpr_definition_id_fk);
$rflag = checkCommonFunctions::check_If_BPR_Approved($model->bpr_id_pk);
$sCompanyName =  getCommonFunctions::getPersonDetails(Yii::$app->user->identity->person_id_pk)['name'];
?>
<style type="text/css">
.pdftbl td{ border:1px solid #666666; padding:10px; vertical-align:top;}
.tblhead{ background-color:#CCCCCC; text-align:center;}
.stepBG{ background-color:#CCCCCC; }
</style>
<table class="table table-bordered pdftbl" cellpadding="0" cellspacing="0" style="width:100%;">
	<tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3><?=$sCompanyName;?></h3>
        	<h3>Batch Production Record</h3>
        </td>
    </tr>
    <tr>
    	<td colspan="2">BPR #: &nbsp;&nbsp;<?=$model->batch;?></td>
        <td colspan="2">Lot #: &nbsp;&nbsp;<?=$model->lot_hash;?></td>
    </tr>
    <tr>
    	<td><strong>Product Code:</strong></td>
        <td><strong>Product Name:</strong></td>
        <td><strong>Strength:</strong></td>
        <td><strong>Product Part #:</strong></td>
    </tr>
    <tr>
    	<td><?=wordwrap(htmlentities($model->product_code),20,"<br/>",1);?></td>
        <td><?=wordwrap(htmlentities($MPRDetails['product_name']),20,"<br/>",1);?></td>
        <td><?=wordwrap(htmlentities($MPRDetails['product_strength']),20,"<br/>",1);?></td>
        <td><?=wordwrap(htmlentities($MPRDetails['product_part']),15,"<br/>",1);?></td>
    </tr>
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>Cover Page</h3>
        </td>
    </tr>
    <tr>
    	<td>Manufacturing Date:</td>
        <td><?php if($rflag=='Yes'){ echo date("m/d/Y H:i:s", strtotime($model->manufacturing_date)).Yii::$app->params['timezoneVar']; } ?></td>
        <td>Formulation Id:</td>
        <td><?=wordwrap($MPRDetails['formulation_id'],20,"<br/>",1);?></td>
    </tr>
    <tr>
    	<td>Author:</td>
        <td><?=wordwrap(getCommonFunctions::getPersonName($MPRDetails['author']),20,"<br/>",1);?></td>
        <td>Batch Size:</td>
        <td><?=wordwrap($MPRDetails['batch_size'],20,"<br/>",1)." ".wordwrap(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']),50,"<br/>",1);?></td>
    </tr>
    <tr>
    	<td>Theoritical Yield:</td>
        <td><?=wordwrap(htmlentities($MPRDetails['theoritical_yield']),30,"<br/>",1);?></td>
        <td>MPR Version #:</td>
        <td><?=$MPRDetails['MPR_version'];?></td>
    </tr>
    <tr>
    	<td>Purpose:</td>
        <td><?=wordwrap(htmlentities($MPRDetails['purpose']),30,"<br/>",1);?></td>
        <td>Scope:</td>
        <td><?=wordwrap(htmlentities($MPRDetails['scope']),30,"<br/>",1);?></td>
    </tr>
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>Bill of Materials</h3>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding:0px;">
        	<table class="table table-bordered" style="width:100%" border="0" cellpadding="0" cellspacing="0">
            	<tr>
                	<td width="10"><strong>Part</strong></td>
                    <td width="5"><strong>Version</strong></td>
                    <td width="30"><strong>Material</strong></td>
                    <td width="20"><strong>Qty/Batch</strong></td>
                    <td width="10"><strong>Unit</strong></td>
                    <td width="15"><strong>Composition</strong></td>
                    <td width="10"><strong>Unit</strong></td>
                </tr>
           <?php if(is_array($billOfMaterials) && count($billOfMaterials)>0) { 
		   		foreach($billOfMaterials as $bk=>$bv){
		   ?>
                <tr>
                	<td width="10"><?=wordwrap(htmlentities($MPRDetails['product_part']),15,"<br/>",1);?></td>
                    <td width="5"><?=$MPRDetails['MPR_version'];?></td>
                    <td width="30"><?=wordwrap(htmlentities($bv['material_name']),20,"<br/>",1);?></td>
                    <td width="20"><?=$bv['qty_branch'];?></td>
                    <td width="10"><?=wordwrap(htmlentities(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$bv['qb_unit_id_fk'])),15,"<br/>",1);?></td>
                    <td width="15"><?=wordwrap(htmlentities($bv['composition']),20,"<br/>",1);;?></td>
                    <td width="10"><?=wordwrap(htmlentities(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$bv['com_unit_id_fk'])),15,"<br/>",1);;?></td>
                </tr>
           <?php } }else{ ?>
		   		<tr>
                	<td colspan="7">Bill of materials not found</td>
                </tr>
		  <?php  } ?>
            </table>
        </td>
    </tr>
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>Formulation</h3>
        </td>
    </tr>
    <tr>
        <td colspan="4" style="padding:0px;">
        	<table class="table table-bordered" style="width:100%" border="0" cellpadding="0" cellspacing="0">
            	<tr>
                	<td width="30"><strong>Part #</strong></td>
                    <td width="40"><strong>Ingredient Name</strong></td>
                    <td width="15"><strong>Formulation %</strong></td>
                    <td width="15"><strong>W/W</strong></td>
                </tr>
           <?php if(is_array($formulations) && count($formulations)>0) { 
		   		$totalKGWeight = 0;
				$totalPercentage = 0;
		   		foreach($formulations as $fk=>$fv){
		   ?>
                <tr>
                	<td width="10"><?=wordwrap(htmlentities($fv['material_part']),20,"<br/>",1);?></td>
                    <td width="5"><?=wordwrap(htmlentities($fv['material_name']),20,"<br/>",1);?></td>
                    <td width="15"><?=wordwrap(htmlentities($fv['formulation_percentage']),20,"<br/>",1)." %";?></td>
                    <td width="15"><?php
                                if(is_array($MPRDetails) && count($MPRDetails)>0)
                                {
                                    $unitnm = getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']);
                                    $batchSizeInKG = CommonFunctions::unitConversionToKG($unitnm,$MPRDetails['batch_size']);
                                }
                                $kgWeight = (($batchSizeInKG*floatval($fv['formulation_percentage']))/100);
                                $kgWeight = round($kgWeight,2);
                                
                                $gmWeight = ((($batchSizeInKG*floatval($fv['formulation_percentage']))/100)*1000);
                                $gmWeight = round($gmWeight,2);
								
								$totalKGWeight = $totalKGWeight + $kgWeight;
								$totalPercentage = $totalPercentage + floatval($fv['formulation_percentage']);
                                
                                if(floatval($fv['formulation_percentage'])==0)
                                {
                                    echo '--';
                                }
                                elseif(floatval($fv['formulation_percentage'])<=0.01)
                                {
                                    echo $gmWeight.'  G';
                                }
                                else
                                {
                                    echo $kgWeight.'  KG';
                                }
                    	?></td>
                </tr>
           <?php } ?>
		   		<tr>
                	<td width="10"></td>
                    <td width="40" align="right">Total:</td>
                    <td width="15"><?php
						$totalPercentage = round($totalPercentage,10);
                    	if($totalPercentage!=0 && ($totalPercentage>100 || $totalPercentage<100))
						{
							echo '<span style="color:#FB3738;">'.$totalPercentage.'  %</span>';
						}
						else if($totalPercentage==100)
						{
							echo '<span style="color:#85C08A;">'.$totalPercentage.'  %</span>';
						}
						else
						{
							echo $totalPercentage.'  %';
						}
						?></td>
                    <td width="15"><?=$totalKGWeight." KG";?></td>
                </tr>
		   <?php }else{ ?>
		   		<tr>
                	<td colspan="4">Formulations not found</td>
                </tr>
		  <?php  } ?>
            </table>
        </td>
    </tr>
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>Equipments</h3>
        </td>
    </tr>
    <tr>
    	<td><strong>Equipment #</strong></td>
        <td><strong>Equipment Name</strong></td>
        <td><strong>Due Date</strong></td>
        <td><strong>Operator Signature</strong></td>
    </tr>
    <?php if(is_array($eQuipments) && count($eQuipments)>0) { 
		   		foreach($eQuipments as $ek=>$ev){
	?>
    <tr>
    	<td><?=$ev['equipment_id_fk'];?></td>
        <td><?=wordwrap(getCommonFunctions::getFieldNameValue("bpr_equipment","name","equipment_id_pk",$ev['equipment_id_fk']),20,"<br/>",1);?></td>
        <td><strong>Calibration Due Date:</strong><br/>
        	<?php $detailsOfEqp = getCommonFunctions::getEquipmentdetails($ev['equipment_id_fk']); ?>
        	<?php if($detailsOfEqp['caliberation_due_date']!='' && $detailsOfEqp['caliberation_due_date']!='0000-00-00'){ echo date("m/d/Y",strtotime($detailsOfEqp['caliberation_due_date'])).Yii::$app->params['timezoneVar'];}else{ echo "";} ?>
        	<br/>&nbsp;<br/>
        	<strong>Preventive Maintenance Due Date:</strong><br/>
            <?php if($detailsOfEqp['preventive_m_due_date']!='' && $detailsOfEqp['preventive_m_due_date']!='0000-00-00'){ echo date("m/d/Y",strtotime($detailsOfEqp['preventive_m_due_date'])).Yii::$app->params['timezoneVar'];}else{ echo "";} ?>
        </td>
        <td><?php 
				if($ev['operator_signature']==0)
				{
					echo 'Not required';
				}
				elseif($ev['operator_signature']==1)
				{
					$operatorSignatureStatus = getCommonFunctions::get_BPREQP_operator_signature_status($ev['equipment_map_id_pk'],$model->mpr_definition_id_fk,$model->bpr_id_pk);
					if(!isset($operatorSignatureStatus['approved_status']) || $operatorSignatureStatus['approved_status']!='Approved')
					{
						echo "Pending"; 
					}
					elseif(count($operatorSignatureStatus)>0 && $operatorSignatureStatus['approved_status']=='Approved' && $operatorSignatureStatus['HS_signature_id']!='')
					{
						$personDetails = getCommonFunctions::getPersonDetails($operatorSignatureStatus['approved_by_person_id_fk']);
						$retStr = htmlentities($personDetails['first_name']).' '.htmlentities($personDetails['last_name']).' ( '.htmlentities($personDetails['user_name_person']).' )<br/>'.date('m/d/Y H:i:s',strtotime($operatorSignatureStatus['approved_datetime'])).Yii::$app->params['timezoneVar'];
						
						echo $retStr;
					}
					else
					{
						echo "";
					}
				}
				else
				{
					echo "";
				}
			?>
        </td>
    </tr>
    <?php }}else{ ?>
    <tr>
        <td colspan="4">Equipments not found</td>
    </tr>
	<?php } ?>
    
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>Manufacturing Instructions</h3>
        </td>
    </tr>
    <?php if(is_array($mInstructionSteps) && count($mInstructionSteps)>0) { 
				foreach($mInstructionSteps as $mk=>$mv){
				 $MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($model->bpr_id_pk,$mv['mi_id_pk']);
	?>
	<tr>
		<td class="stepBG"><strong>Step</strong></td>
		<td colspan="3" class="stepBG"><?=wordwrap($mv['mi_step'],30,"<br/>",1);?></td>
	</tr>
    <tr>
		<td><strong>Action</strong></td>
		<td><?=wordwrap($mv['mi_action'],20,"<br/>",1);?></td>
        <td><strong>Target</strong></td>
		<td><?=wordwrap($mv['target'],20,"<br/>",1);?></td>
	</tr>
    <tr>
		<td><strong>Result</strong></td>
		<td><?=wordwrap($MiApprovalDetails['result'],20,"<br/>",1);?></td>
        <td><strong>Comments</strong></td>
		<td><?=wordwrap($MiApprovalDetails['comments'],20,"<br/>",1);?></td>
	</tr>
    <tr>
		<td><strong>Performer:</strong><br/>
        	<?php 
			 if($MiApprovalDetails['arppover_person_id_fk']>0 && $MiApprovalDetails['approval_datetime']!='0000-00-00 00:00:00')
			 { 
				$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['arppover_person_id_fk']);
			    echo htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." ) ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['approval_datetime'])).Yii::$app->params['timezoneVar']; 
             }elseif($mv['perfomer']=='No'){ echo "Not Required"; }else{ echo "-";} ?>
        </td>
        <td><strong>Verifier:</strong><br/>
        	<?php if($MiApprovalDetails['verifier_person_id_fk']>0 && $MiApprovalDetails['verified_datetime']!='0000-00-00 00:00:00'){ 
				$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['verifier_person_id_fk']);
		
				echo htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." ) ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['verified_datetime'])).Yii::$app->params['timezoneVar']; 
			 }elseif($mv['verifier']=='No'){ echo "Not Required"; }else{ echo "-"; } ?>
        </td>
        <td><strong>Deviation:</strong><br/>
        	<?php if($MiApprovalDetails['deviation_comments']==''){ 
					echo htmlentities("--"); 
				}else{ 
					echo wordwrap(htmlentities($MiApprovalDetails['deviation_comments']),20,"<br/>",1); 
				} ?>
        </td>
       <td><?php if($MiApprovalDetails['deviation_comments']!=''){ ?><strong>QA Review:</strong><br/>
        	<?php if($MiApprovalDetails['QA_person_id_fk']>0 && $MiApprovalDetails['QA_datetime']!='0000-00-00 00:00:00'){ 
				$personDetails = getCommonFunctions::getPersonDetails($MiApprovalDetails['QA_person_id_fk']);
		
				echo htmlentities($personDetails['first_name'])." ".htmlentities($personDetails['last_name'])." ( ".htmlentities($personDetails['user_name_person'])." ) ".date('m/d/Y H:i:s',strtotime($MiApprovalDetails['QA_datetime'])).Yii::$app->params['timezoneVar']; 
			 }elseif($MiApprovalDetails['deviation_comments']==''){ echo "--"; }else{ echo "QA Review Pending"; } ?>
            <?php } ?>
        </td>
	</tr>
	<?php }}else{ ?>
	<tr>
    	<td colspan="4">Manufacturing steps not found</td>
    </tr>
	<?php } ?>
    
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>BPR Approvals</h3>
        </td>
    </tr>
    <tr>
    	<td colspan="4" style="padding:0px;">
        	<table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" style="width:100%;border:0;">
                <tr>
                    <td><strong>Approver</strong></td>
                    <td><strong>Job Function</strong></td>
                    <td><strong>Date/Time</strong></td>
                    <td><strong>Verifier</strong></td>
                    <td><strong>Job Function</strong></td>
                    <td><strong>Date/Time</strong></td>
                </tr>
				<?php if(is_array($bprApprovals) && count($bprApprovals)>0) { 
                            foreach($bprApprovals as $ak=>$av){
                ?>
                <tr>
                    <td><?=getCommonFunctions::getPersonName($av['approval_person_id_fk']);?></td>
                    <td><?=wordwrap(htmlentities($av['approval_job_function']),30,"<br/>",1);?></td>
                    <td><?=($av['approval_datetime']!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($av['approval_datetime'])).Yii::$app->params['timezoneVar']:'';?></td>
                    <td><?=getCommonFunctions::getPersonName($av['verifier_person_id_fk']);?></td>
                    <td><?=wordwrap(htmlentities($av['verifier_job_function']),30,"<br/>",1);?></td>
                    <td><?=($av['verified_datetime']!='0000-00-00 00:00:00')?date("m/d/Y H:i:s",strtotime($av['verified_datetime'])).Yii::$app->params['timezoneVar']:'';?></td>
                </tr>
				<?php }}else{ ?>
                	<tr>
                    	<td colspan="6">Approvals not found</td>
                    </tr>
				<?php } ?>
           </table>
       </td>
    </tr>
    <?php $curStatus = getCommonFunctions::get_BPR_current_status($model->bpr_id_pk);
	if($curStatus!='' && strlen($curStatus)>0) { 
		$outputAll = getCommonFunctions::get_BPR_all_status_approver($model->bpr_id_pk);
	?>
    <tr>
    	<td colspan="4" class="tblhead" align="center">
        	<h3>BPR Status</h3>
        </td>
    </tr>
    <tr style="padding:0px;">
    	<td colspan="4" style="padding:0px;">
            <table class="table table-bordered" border="0" cellpadding="0" cellspacing="0" style="width:100%;border:0;">
                <tr>
                    <td><strong>Status</strong></td>
                    <td><strong>Status Approver</strong></td>
                    <td><strong>Time Stamp</strong></td>
                </tr>
                <?php if(is_array($outputAll) && count($outputAll)>0){ 
                    foreach($outputAll as $ok=>$ov){
                ?>
                <tr>
                    <td><?=$ov['status'];?></td>
                    <td><?=$ov['personname'];?></td>
                    <td><?=$ov['dttm'].Yii::$app->params['timezoneVar'];?></td>
                </tr>
               <?php }} ?>
            </table>
        </td>
    </tr>
    <?php } ?>
</table>
