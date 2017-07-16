<?php

include('config.php');


	$request = $_POST;
	$modulename = isset($_POST['module_name'])?$_POST['module_name']:'';
	$module_primary_id = isset($_POST['module_primary_id'])?$_POST['module_primary_id']:0;
	$signType = isset($_POST['signType'])?$_POST['signType']:'';
	$signature_id = isset($_POST['signature_id'])?$_POST['signature_id']:'';

	/*$myFile = "eventData.txt";
	$fh = @fopen($myFile, 'a');
	@fwrite($fh, "\n");
	
	@fwrite($fh, "--------- \n Received data as below : \n ");
		
	@fwrite($fh, "signature_id=>".$_POST['signature_id']);
	@fwrite($fh, "module_primary_id=>".$_POST['module_primary_id']);
	@fwrite($fh, "module_name=>".$_POST['module_name']);
	
	@fwrite($fh, " \n --------- \n");
		
	@fclose($fh);*/
	
	// Create connection
	$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

	// Check connection
	if ($conn->connect_error) {
    	die("Connection failed: " . $conn->connect_error);
	}
	
	/****************************************/	
	switch($modulename)
	{
		case 'MPR_Approvals':
			if($module_primary_id>0)
			{
				if($signType=='Approver')
				{
					$sql = "UPDATE bpr_mpr_approval SET HS_approver_signature_id='".$signature_id."' WHERE mpr_approval_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
				elseif($signType=='Verifier')
				{
					$sql = "UPDATE bpr_mpr_approval SET HS_verifier_signature_id='".$signature_id."' WHERE mpr_approval_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
			}
			break;
		case 'BPR_Approvals':
			if($module_primary_id>0)
			{
				if($signType=='Approver')
				{
					$sql = "UPDATE bpr_bpr_approval SET HS_approver_signature_id='".$signature_id."' WHERE bpr_approval_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
				elseif($signType=='Verifier')
				{
					$sql = "UPDATE bpr_bpr_approval SET HS_verifier_signature_id='".$signature_id."' WHERE bpr_approval_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
			}
			break;
		case 'BPR_Step_Approvals':
			if($module_primary_id>0)
			{
				if($signType=='performer')
				{
					$sql = "UPDATE bpr_instructions SET HS_approver_signature_id='".$signature_id."' WHERE mi_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
				elseif($signType=='verifier')
				{
					$sql = "UPDATE bpr_instructions SET HS_verifier_signature_id='".$signature_id."' WHERE mi_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
				elseif($signType=='QAPerson')
				{
					$sql = "UPDATE bpr_instructions SET HS_reviewer_signature_id='".$signature_id."' WHERE mi_id_pk='".$module_primary_id."'";
					$conn->query($sql);
					echo "Success";
				}
			}
			break;
		case 'BPR_Status_Change':
			echo "Success";
			break;
		case 'BPR_Equipment_Operator':
			echo "Success";
			break;
	}
	/***************************************/
	$conn->close();
?>
