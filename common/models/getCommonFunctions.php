<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\Query; 
use backend\modules\equipment\models\Equipment;

class getCommonFunctions extends Model
{
	/***** Function to get any field value for any table ******************************/
	public static function getFieldNameValue($tableName,$getFieldName,$checkFieldName,$value)
	{
		$params = [':id' => $value];
		$Row = Yii::$app->db->createCommand("SELECT ".$getFieldName." FROM ".$tableName." WHERE ".$checkFieldName."=:id", $params)->queryOne();
			if($Row[$getFieldName])
    			$name = htmlentities($Row[$getFieldName]);
			else
				$name = '';
		return $name;
	}
	
	/***** Function to get name of person from id ******************************/
	public static function getPersonName($personid)
	{
		$params = [':person_id_pk' => $personid];
		$Person = Yii::$app->db->createCommand('SELECT first_name,last_name FROM bpr_person WHERE person_id_pk=:person_id_pk', $params)->queryOne();
			if($Person['first_name'])
    			$name = htmlentities($Person['first_name'])." ".htmlentities($Person['last_name']);
			else
				$name = '';
		return $name;
	}
	
	/***** Function to get person details from id ******************************/
	public static function getPersonDetails($personid)
	{
		$personDetails = array();
		$params = [':person_id_pk' => $personid];
		$Person = Yii::$app->db->createCommand('SELECT 
														p.person_id_pk, p.is_super_admin, p.role_id_fk, p.first_name, 
														p.last_name, p.phone, p.fax, p.address, 
														p.city_id_fk, p.state_id_fk, p.pobox, p.zip_pincode, 
														p.country_id_fk, p.emailid, p.user_name_person, p.password_person, 
														p.password_hash, p.password_reset_token, p.auth_key, p.image, 
														p.isDeleted, p.reasonIsDeleted, p.deleted_datetime, p.isRestored, 
														p.reasonIsRestored, p.restore_datetime, p.addedby_person_id_fk, p.created_datetime, 
														p.super_company_id_fk,
														pc.name
												FROM 
													bpr_person as p,bpr_person_company as pc 
												WHERE 
													p.person_id_pk=:person_id_pk 
													and p.super_company_id_fk=pc.company_id_pk', $params)->queryOne();
			if($Person['first_name'])
    			$personDetails = $Person;
				
		return $personDetails;
	}
	
	/***** Function to get person details from id ******************************/
	public static function getUserDetails($loggedinid)
	{
		$personDetails = array();
		$params = [':id' => $loggedinid];
		$Person = Yii::$app->db->createCommand('SELECT id, firstname, lastname, nickname, username, auth_key, password_hash, password_reset_token, image, companyname, email, status, created_at, updated_at FROM bpr_admin WHERE id=:id', $params)->queryOne();
			if($Person['firstname'])
    			$personDetails = $Person;
				
		return $personDetails;
	}
	/***** Function to get person details from username ******************************/
	public function getPersonFromUsername($username, $password)
	{
		$params = [':user_name_person' => $username, ':password_person'=>md5($password),':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk, ':isDeleted' => '0'];
		$Person = Yii::$app->db->createCommand('SELECT person_id_pk FROM bpr_person WHERE (user_name_person=:user_name_person or emailid=:user_name_person) and password_person=:password_person and super_company_id_fk=:super_company_id_fk and isDeleted=:isDeleted', $params)->queryOne();
			if($Person['person_id_pk'])
    			$person_id_pk = $Person['person_id_pk'];
			else
				$person_id_pk = '';
		return $person_id_pk;
	}
	/***** Function to get product codes of approved MPRs ******************************/
	public static function getApprovedProductCodes()
	{
		$approvedMPRs = array();
		$params = [':isDeleted' => '0', ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
		$mprs = Yii::$app->db->createCommand('SELECT md.mpr_defination_id_pk, md.product_id_fk, md.product_code, md.MPR_version, md.product_part, md.author, md.product_name, md.formulation_id, md.product_strength, md.batch_size, md.MPR_unit_id, md.theoritical_yield, md.company_id_fk, md.purpose, md.scope, md.isDeleted, md.reasonIsDeleted, md.deleted_datetime, md.isRestored, md.reasonIsRestored, md.restore_datetime, md.addedby_person_id_fk, md.created_datetime, md.isCopied, md.mpr_token, md.super_company_id_fk FROM bpr_mpr_defination  as md JOIN bpr_product ON bpr_product.product_id_pk=md.product_id_fk WHERE md.isDeleted=:isDeleted AND md.super_company_id_fk=:super_company_id_fk', $params)->queryAll();
		if(is_array($mprs) && count($mprs)>0)
		{
			foreach($mprs as $k=>$v)
			{
				$check_If_MPR_Approved = '';
				$check_If_MPR_Approved = checkCommonFunctions::check_If_MPR_Approved($v['mpr_defination_id_pk']);
				
				$checkIfEquipmentExpiredOrDeleted = '';
				$checkIfEquipmentExpiredOrDeleted = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($v['mpr_defination_id_pk']);
				
				if($check_If_MPR_Approved == "Yes" && $checkIfEquipmentExpiredOrDeleted == 'No')
				{
					$approvedMPRs[$v['product_code']] = $v['product_code'];
				}
			}
		}
		if(is_array($approvedMPRs) && count($approvedMPRs)>0)
		{
			$approvedMPRs = array_filter($approvedMPRs);
			asort($approvedMPRs);
		}
		return $approvedMPRs;
	}
	
	/***** Function to get approved MPR versions from product code ******************************/
	public static function get_MPR_From_Product_Code($product_code)
	{
		$approvedMPRs = array();
		$params = [':product_code' => $product_code, ':isDeleted' => '0', ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
		$mprs = Yii::$app->db->createCommand("SELECT mpr_defination_id_pk, product_id_fk, product_code, MPR_version, product_part, author, product_name, formulation_id, product_strength, batch_size, MPR_unit_id, theoritical_yield, company_id_fk, purpose, scope, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, isCopied, mpr_token, super_company_id_fk FROM bpr_mpr_defination WHERE product_code=:product_code and isDeleted=:isDeleted and super_company_id_fk=:super_company_id_fk", $params)->queryAll();
	
		if(is_array($mprs) && count($mprs)>0)
		{
			foreach($mprs as $k=>$v)
			{
				$check_If_MPR_Approved = '';
				$check_If_MPR_Approved = checkCommonFunctions::check_If_MPR_Approved($v['mpr_defination_id_pk']);
				
				$checkIfEquipmentExpiredOrDeleted = '';
				$checkIfEquipmentExpiredOrDeleted = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($v['mpr_defination_id_pk']);
				
				if($check_If_MPR_Approved == "Yes" && $checkIfEquipmentExpiredOrDeleted == 'No')
				{
					$approvedMPRs[$v['mpr_defination_id_pk']] = $v['MPR_version'];
				}
			}
		}
		return $approvedMPRs;
	}
	
	/***** Function to get approved MPR versions from product part ******************************/
	public static function get_MPR_From_Product_Part($product_code,$product_part,$sort_mpr)
	{
		if($sort_mpr=='')
			$sort_mpr = 'product_code';
			
		$approvedMPRs = array();
		if(isset($product_part) && $product_part!='')
		{
			$params = [':product_code' => $product_code, ':product_part'=>$product_part, ':isDeleted' => '0', ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
			$mprs = Yii::$app->db->createCommand('SELECT mpr_defination_id_pk, product_id_fk, product_code, MPR_version, product_part, author, product_name, formulation_id, product_strength, batch_size, MPR_unit_id, theoritical_yield, company_id_fk, purpose, scope, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, isCopied, mpr_token, super_company_id_fk FROM bpr_mpr_defination WHERE product_code=:product_code and product_part=:product_part and isDeleted=:isDeleted and super_company_id_fk=:super_company_id_fk ORDER BY '.$sort_mpr.' ASC', $params)->queryAll();
		}
		else
		{
			$params = [':product_code' => $product_code, ':isDeleted' => '0', ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
			$mprs = Yii::$app->db->createCommand('SELECT mpr_defination_id_pk, product_id_fk, product_code, MPR_version, product_part, author, product_name, formulation_id, product_strength, batch_size, MPR_unit_id, theoritical_yield, company_id_fk, purpose, scope, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, isCopied, mpr_token, super_company_id_fk FROM bpr_mpr_defination WHERE product_code=:product_code and isDeleted=:isDeleted and super_company_id_fk=:super_company_id_fk ORDER BY '.$sort_mpr.' ASC', $params)->queryAll();
		}
		if(is_array($mprs) && count($mprs)>0)
		{
			foreach($mprs as $k=>$v)
			{
				$retFlag = '';
				$retFlag = checkCommonFunctions::check_If_MPR_Approved($v['mpr_defination_id_pk']);
				if($retFlag=="Yes")
				{
					$approvedMPRs[$v['mpr_defination_id_pk']] = $v['MPR_version'];
				}
			}
		}
		return $approvedMPRs;
	}
	
	/***** Function to get MPR Definition Details from ID ******************************/
	public static function get_MPR_Definition_Details($mpr_def_id)
	{
		$params = [':mpr_defination_id_pk' => $mpr_def_id];
		$MPRDetails = Yii::$app->db->createCommand('SELECT mpr_defination_id_pk, product_id_fk, product_code, MPR_version, product_part, author, product_name, formulation_id, product_strength, batch_size, MPR_unit_id, theoritical_yield, company_id_fk, purpose, scope, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, isCopied, mpr_token, super_company_id_fk FROM bpr_mpr_defination WHERE mpr_defination_id_pk=:mpr_defination_id_pk', $params)->queryOne();
		return $MPRDetails;
	}
	
	/***** Function to get MI Details from bprid & mi_id ******************************/
	public static function getMiApprovalDetails($bpr_id, $mi_id)
	{
		$params = [':bpr_id_fk' => $bpr_id, ':mi_id_pk' => $mi_id];
		$MIDetails = Yii::$app->db->createCommand('SELECT instruction_id_pk, bpr_id_fk, mpr_definition_id_fk, mi_id_pk, result, comments, deviation_comments, arppover_person_id_fk, approval_datetime, verifier_person_id_fk, verified_datetime, QA_person_id_fk, QA_datetime, no_person_id_fk, no_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id, HS_reviewer_signature_id FROM bpr_instructions WHERE bpr_id_fk=:bpr_id_fk and mi_id_pk=:mi_id_pk', $params)->queryOne();
		return $MIDetails;
	}
	
	/*******Function to get unapproved BPR Instructions **************************/
	public static function getOutstandingBPRInstructions($bpr_id,$mi_definition_id)
	{
		$performerOutstanding = array();
		$verifierOutstanding = array();
		$QAOutstanding = array();
		
		$params = [':mpr_defination_id_fk' => $mi_definition_id, ':isDeleted' => '0'];
		$mInstructionSteps = Yii::$app->db->createCommand('SELECT mi_id_pk, mpr_defination_id_fk, mi_step, mi_action, unit_id_fk, mi_range, target, perfomer, verifier, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_manufacturing_instruction WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted ORDER BY mi_id_pk ASC', $params)->queryAll();
		if(is_array($mInstructionSteps) && count($mInstructionSteps)>0)
		{
			foreach($mInstructionSteps as $k=>$v)
			{
				$params1 = [':mpr_definition_id_fk' => $mi_definition_id, 'miIDstr' => $v['mi_id_pk'], ':bpr_id' => $bpr_id];
				$bprSteps = Yii::$app->db->createCommand('SELECT instruction_id_pk, bpr_id_fk, mpr_definition_id_fk, mi_id_pk, result, comments, deviation_comments, arppover_person_id_fk, approval_datetime, verifier_person_id_fk, verified_datetime, QA_person_id_fk, QA_datetime, no_person_id_fk, no_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id, HS_reviewer_signature_id FROM bpr_instructions WHERE mpr_definition_id_fk=:mpr_definition_id_fk and mi_id_pk=:miIDstr  and bpr_id_fk=:bpr_id ORDER BY mi_id_pk ASC', $params1)->queryAll();
				
				if(is_array($bprSteps) && count($bprSteps)>0)
				{
					foreach($bprSteps as $bk=>$bv)
					{
						if($v['perfomer'] == 'Yes' && $bv['arppover_person_id_fk']<=0)
						{
							$performerOutstanding[] = $v['mi_step'];
						}
						if($v['verifier'] == 'Yes' && $bv['verifier_person_id_fk']<=0)
						{
							$verifierOutstanding[] = $v['mi_step'];
						}
						if($bv['deviation_comments']!='' && $bv['QA_person_id_fk']<=0)
						{
							$QAOutstanding[] = $v['mi_step'];
						}
					}
				}
				else
				{
					if($v['perfomer'] == 'Yes')
					{
						$performerOutstanding[] = $v['mi_step'];
					}
					if($v['verifier'] == 'Yes')
					{
						$verifierOutstanding[] = $v['mi_step'];
					}
				}		
			}//end of foreach
		}//end of first if loop 
	
		$performerStr = ''; $verifierStr =''; $QAStr = '';
		
		if(is_array($performerOutstanding) && count($performerOutstanding)>0)
			$performerStr = implode(", ",$performerOutstanding);
		
		if(is_array($verifierOutstanding) && count($verifierOutstanding)>0)
			$verifierStr = implode(", ",$verifierOutstanding);
		
		if(is_array($QAOutstanding) && count($QAOutstanding)>0)
			$QAStr = implode(", ",$QAOutstanding);
			
		$output = array('performer' => $performerStr, 'verifier' => $verifierStr, 'QAOutstanding' => $QAStr );
		return $output;
	}
	
	/***** Function to get BPR Record Details from ID ******************************/
	public static function get_BPR_Record_Details($bpr_id)
	{
		$params = [':bpr_id_pk' => $bpr_id];
		$BPRDetails = Yii::$app->db->createCommand('SELECT bpr_id_pk, batch, manufacturing_date, mpr_definition_id_fk, product_code, mpr_version, isDeleted, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_batch_processing_records WHERE bpr_id_pk=:bpr_id_pk', $params)->queryOne();
		return $BPRDetails;
	}
	
	/****** Get current status of BPR record from ID **************************/
	public static function get_BPR_current_status($bpr_id)
	{
		$params = [':bpr_id_fk' => $bpr_id];
		$BPRStatus = Yii::$app->db->createCommand('SELECT status_id_pk, bpr_id_fk, status, person_id_fk, status_datetime, super_company_id_fk, HS_signature_id FROM bpr_batch_status WHERE bpr_id_fk=:bpr_id_fk ORDER BY status_id_pk DESC', $params)->queryOne();
		if(isset($BPRStatus) && isset($BPRStatus['status']) && $BPRStatus['status']!='')
			return $BPRStatus['status'];
		else
			return '';
	}
	
	/***** Function to get BPR status approver's & timestamp ******************************/
	public static function get_BPR_ApprovesVerifers($bpr_id)
	{
		$approverUsers = array(); $verifierUsers = array();
		$params = [':bpr_id_fk' => $bpr_id];
		$apprRows = Yii::$app->db->createCommand('SELECT bpr_approval_id_pk, bpr_id_fk, approval_person_id_fk, approval_job_function, approval_status, approval_datetime, verifier_person_id_fk, verifier_job_function, verified_status, verified_datetime, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id FROM bpr_bpr_approval WHERE bpr_id_fk=:bpr_id_fk and isDeleted="0" ORDER BY bpr_approval_id_pk ASC', $params)->queryAll();
		$outStr = '';
		
		if(is_array($apprRows) && count($apprRows)>0)
		{
			$a = 0; $ve = 0; 
			foreach($apprRows as $k=>$v)
			{
				if($v['approval_person_id_fk']>0 && $v['approval_status']=='Approved')
				{
					$approverUsers[$a]['person'] = $v['approval_person_id_fk'];
					$approverUsers[$a]['datetime'] = $v['approval_datetime'];
					$personname = getCommonFunctions::getPersonName($v['approval_person_id_fk']);
					$outStr .= 'Approved by '.$personname.' on '.date("m/d/Y H:i:s",strtotime($v['approval_datetime']));
					$a++;
				}
										
				if($v['verifier_person_id_fk']>0 && $v['verified_status']=='Verified')
				{
					$verifierUsers[$ve]['person'] = $v['verifier_person_id_fk'];
					$verifierUsers[$ve]['datetime'] = $v['verified_datetime'];
					$personname = getCommonFunctions::getPersonName($v['verifier_person_id_fk']);
					$outStr .= 'Verified by '.$personname.' on '.date("m/d/Y H:i:s",strtotime($v['verified_datetime']));
					$ve++;
				}
			}
		}
		$output = array('approvers' => $approverUsers, 'verifers' => $verifierUsers, 'outStr' => $outStr);
		return $output;
	}	
	
	/***** Function to get BPR last approver's & timestamp ******************************/
	public static function get_BPR_last_ApprovesVerifers($bpr_id)
	{
		$approverUsers = array(); $verifierUsers = array();
		$params = [':bpr_id_fk' => $bpr_id];
		$apprRows = Yii::$app->db->createCommand('SELECT bpr_approval_id_pk, bpr_id_fk, approval_person_id_fk, approval_job_function, approval_status, approval_datetime, verifier_person_id_fk, verifier_job_function, verified_status, verified_datetime, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id FROM bpr_bpr_approval WHERE bpr_id_fk=:bpr_id_fk and isDeleted="0" ORDER BY bpr_approval_id_pk DESC', $params)->queryOne();
		$outStr = ''; $personname =''; $dttm = '';
		
		if(is_array($apprRows) && count($apprRows)>0)
		{
			if($apprRows['approval_person_id_fk']>0 && $apprRows['approval_status']=='Approved')
			{
				$personDetails = getCommonFunctions::getPersonDetails($apprRows['approval_person_id_fk']);
				$personname = $personDetails['first_name'].' '.$personDetails['last_name'].' <br/> ( '. $personDetails['user_name_person'] . ' ) ';
				$dttm = date("m/d/Y H:i:s",strtotime($apprRows['approval_datetime']));
			}						
		}
		$output = array('personname' => $personname, 'dttm' => $dttm );
		return $output;
	}	
	
	/****** Get current status of BPR with person name & datetime ID **************************/
	public static function get_BPR_last_status_approver($bpr_id)
	{
		$personname =''; $dttm = ''; $HS_signature_doc = '';
		
		$params = [':bpr_id_fk' => $bpr_id];
		$BPRStatus = Yii::$app->db->createCommand('SELECT status_id_pk, bpr_id_fk, status, person_id_fk, status_datetime, super_company_id_fk, HS_signature_id FROM bpr_batch_status WHERE bpr_id_fk=:bpr_id_fk ORDER BY status_id_pk DESC', $params)->queryOne();
		if(isset($BPRStatus) && isset($BPRStatus['status']) && $BPRStatus['status']!='')
		{
			$personDetails = getCommonFunctions::getPersonDetails($BPRStatus['person_id_fk']);
			if($personDetails['first_name'])
			{
				$personname = $personDetails['first_name'].' '.$personDetails['last_name'].' <br/> ( '. $personDetails['user_name_person'] . ' ) ';
			}
			else
			{
				$personname = "";
			}
			$dttm = date("m/d/Y H:i:s",strtotime($BPRStatus['status_datetime']));
			
			if($BPRStatus['HS_signature_id']!='')
				$HS_signature_doc = getCommonFunctions::getSignedDocFromSignatureId($BPRStatus['HS_signature_id']);
			else
				$HS_signature_doc = '';
		}
		$output = array('personname' => $personname, 'dttm' => $dttm, 'signatureDoc' => $HS_signature_doc);
		return $output;
	}
	
	/************************************************************************************/
	public static function get_all_role_modules()
	{
		$sql = "SELECT name_pk,display_name,display_icon FROM bpr_role_modules";
		$modules = Yii::$app->db->createCommand($sql)->queryAll();
		return $modules;
	}
	/***********************************************************************************/
	public static function getRoleModulesFromRoleID($roleid)
	{
		 $sql = "SELECT name,modules,isDeleted from bpr_role where role_id_pk= '".$roleid."' "; 
		 $roleDetails = Yii::$app->db->createCommand($sql)->queryOne();
		 return $roleDetails['modules'];
	}
	/***********************************************************************************/
	public static function getAllPersonsOfCompany()
	{
		$params = [':isDeleted' => '0',':super_company_id_fk'=>Yii::$app->user->identity->super_company_id_fk];
		 $sql = "SELECT 
		 				p.person_id_pk, p.is_super_admin, p.role_id_fk, p.first_name, 
						p.last_name, p.phone, p.fax, p.address, 
						p.city_id_fk, p.state_id_fk, p.pobox, p.zip_pincode, 
						p.country_id_fk, p.emailid, p.user_name_person, p.password_person, 
						p.password_hash, p.password_reset_token, p.auth_key, p.image, 
						p.isDeleted, p.reasonIsDeleted, p.deleted_datetime, p.isRestored, 
						p.reasonIsRestored, p.restore_datetime, p.addedby_person_id_fk, p.created_datetime, 
						p.super_company_id_fk
				FROM 
					bpr_person as p
				WHERE 
					p.isDeleted=:isDeleted 
					and p.super_company_id_fk=:super_company_id_fk ORDER BY p.first_name ASC"; 
		 $Persons = Yii::$app->db->createCommand($sql,$params)->queryAll();
		 return $Persons;
	}
	
	/**********************************************************************************/
	public static function getSignedDocFromSignatureId($signature_id)
	{
		$sql = "SELECT doc_file_url from bpr_hellosign_requests where HS_signature_id_fk= '".$signature_id."' "; 
		$docDetails = Yii::$app->db->createCommand($sql)->queryOne();

		$documentArr = explode("/",$docDetails['doc_file_url']);
		$documentID = array_pop($documentArr);
		
		//$docuemntFetchURL = "https://".Yii::$app->params['HSAPIKEY'].":@api.hellosign.com/v3/signature_request/files/".$documentID;
		$docuemntFetchURL = Yii::$app->homeUrl."HelloSign/HelloSignDocument.php?documentID=".$documentID;
		
		 if($docDetails['doc_file_url']!='')
			return "<a href='".$docuemntFetchURL."' class='small' target='_blank' style='font-size:12px!important;text-decoration:underline;' custAttr='".$documentID."' title='View'>View Signed Doc</a>";
		 else
		 	return "";
	}
	/****** Get all status of BPR with person name & datetime ID **************************/
	public static function get_BPR_all_status_approver($bpr_id)
	{
		$personname =''; $dttm = ''; $HS_signature_doc = '';
		
		$params = [':bpr_id_fk' => $bpr_id];
		$BPRStatus = Yii::$app->db->createCommand('SELECT status_id_pk, bpr_id_fk, status, person_id_fk, status_datetime, super_company_id_fk, HS_signature_id FROM bpr_batch_status WHERE bpr_id_fk=:bpr_id_fk ORDER BY status_id_pk DESC', $params)->queryAll();
		if(isset($BPRStatus) && count($BPRStatus)>0)
		{
			foreach($BPRStatus as $k=>$v)
			{
				if(isset($v['status']) && $v['status']!='')
				{
					$personDetails = getCommonFunctions::getPersonDetails($v['person_id_fk']);
					$personname = $personDetails['first_name'].' '.$personDetails['last_name'].' <br/> ( '. $personDetails['user_name_person'] . ' ) ';
					$dttm = date("m/d/Y H:i:s",strtotime($v['status_datetime']));
					
					if($v['HS_signature_id']!='')
						$HS_signature_doc = getCommonFunctions::getSignedDocFromSignatureId($v['HS_signature_id']);
					else
						$HS_signature_doc = '';
				}
				$output[] = array('status'=>$v['status'],'personname' => $personname, 'dttm' => $dttm, 'signatureDoc' => $HS_signature_doc);
			}
		}
		return $output;
	}
	/*******Function to get unapproved BPR Equipments **************************/
	public static function getOutstandingBPREquipments($bpr_id,$mpr_definition_id)
	{
		$output = array();
		
		$params = [':mpr_defination_id_fk' => $mpr_definition_id, ':isDeleted' => '0', ':operator_signature'=>1]; //':approved_status'=>'Approved'
		$equipments = Yii::$app->db->createCommand('SELECT equipment_map_id_pk,equipment_id_fk  FROM bpr_equipment_map WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted and operator_signature=:operator_signature', $params)->queryAll();
		if(isset($equipments) && count($equipments)>0)
		{
			$idArr = array();
			foreach($equipments as $k=>$v)
			{
				$operatorSignatureStatus = getCommonFunctions::get_BPREQP_operator_signature_status($v['equipment_map_id_pk'],$mpr_definition_id,$bpr_id);
				if(!isset($operatorSignatureStatus) || count($operatorSignatureStatus)<=0 || $operatorSignatureStatus['approved_status']!='Approved')
				{
					$idArr[] = $v['equipment_id_fk'];
				}
			}
			if(count($idArr)>0)
			{
				$idStr = implode(",",$idArr);
				$output = array('pending' => 'Yes', 'equipment_ids' => $idStr);
			}
		}
		return $output;
	}
	
	/*******Function to get Equipment Details **************************/
	public static function getEquipmentdetails($id)
	{
		$eqpDetails = array();
		$eqpDetails = Equipment::find()->where(['equipment_id_pk'=>$id])->asArray()->one();
		return $eqpDetails;
	}
	
	/****** Get current status of BPR record from ID **************************/
	public static function get_BPREQP_operator_signature_status($eqp_map_id,$mpr_id,$bpr_id)
	{
		$params = [':eqp_map_id_fk'=>$eqp_map_id,':bpr_id_fk' => $bpr_id, ':mpr_definition_id_fk' => $mpr_id];
		$OperatorStatus = Yii::$app->db->createCommand('SELECT id, eqp_map_id_fk, bpr_id_fk, mpr_definition_id_fk, approved_status, approved_datetime, approved_by_person_id_fk, HS_signature_id FROM bpr_operator_signature_status WHERE eqp_map_id_fk=:eqp_map_id_fk AND bpr_id_fk=:bpr_id_fk AND mpr_definition_id_fk=:mpr_definition_id_fk', $params)->queryOne();
		return $OperatorStatus;
	
	}
	/**********************************************************************************/
}
