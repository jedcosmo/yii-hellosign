<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\Query; 
 
class checkCommonFunctions extends Model
{
	/***** Function to cross check if perticular MPR record is approved/verified ******************************/
	public static function check_If_MPR_Approved($mpr_definition_id)
	{
		$approvedFlag = 'No'; $verifiedFlag = 'No';
		$params = [':mpr_defination_id_fk' => $mpr_definition_id, ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
		$apprRows = Yii::$app->db->createCommand('SELECT mpr_approval_id_pk, mpr_defination_id_fk, approval_person_id_fk, approval_job_function, approval_status,approval_datetime, verifier_person_id_fk, verifier_job_function, verified_status, verified_datetime, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id FROM bpr_mpr_approval WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted="0" and super_company_id_fk=:super_company_id_fk', $params)->queryAll();
		
		if(is_array($apprRows) && count($apprRows)>0)
		{
			foreach($apprRows as $k=>$v)
			{
				if($v['approval_person_id_fk']>0 && $v['approval_status']=='Approved')
					$approvedFlag = 'Yes';
				elseif($v['approval_person_id_fk']==0 && $v['approval_status']=='')
					$approvedFlag = 'Yes';
				else
				{
					$approvedFlag = 'No';
					break;
					return "No";
				}
						
				if($v['verifier_person_id_fk']>0 && $v['verified_status']=='Verified')
					$verifiedFlag = 'Yes';
				elseif($v['verifier_person_id_fk']==0 && $v['verified_status']=='')
					$verifiedFlag = 'Yes';
				else
				{
					$verifiedFlag = 'No';
					break;
					return "No";
				}
			}
		}
		
		if($approvedFlag=='Yes' && $verifiedFlag=='Yes')
			return "Yes";
		else
			return "No";
	}
	/***** Function to cross check if perticular BPR record is approved/verified ******************************/
	public static function check_If_BPR_Approved($bpr_id)
	{
		$approvedFlag = 'No'; $verifiedFlag = 'No';
		$params = [':bpr_id_fk' => $bpr_id];
		$apprRows = Yii::$app->db->createCommand('SELECT bpr_approval_id_pk, bpr_id_fk, approval_person_id_fk, approval_job_function, approval_status, approval_datetime, verifier_person_id_fk, verifier_job_function, verified_status, verified_datetime, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk, HS_approver_signature_id, HS_verifier_signature_id FROM bpr_bpr_approval WHERE bpr_id_fk=:bpr_id_fk and isDeleted="0"', $params)->queryAll();
		
		if(is_array($apprRows) && count($apprRows)>0)
		{
			foreach($apprRows as $k=>$v)
			{
				if($v['approval_person_id_fk']>0 && $v['approval_status']=='Approved')
					$approvedFlag = 'Yes';
				elseif($v['approval_person_id_fk']==0 && $v['approval_status']=='')
					$approvedFlag = 'Yes';
				else
				{
					$approvedFlag = 'No';
					break;
					return "No";
				}
						
				if($v['verifier_person_id_fk']>0 && $v['verified_status']=='Verified')
					$verifiedFlag = 'Yes';
				elseif($v['verifier_person_id_fk']==0 && $v['verified_status']=='')
					$verifiedFlag = 'Yes';
				else
				{
					$verifiedFlag = 'No';
					break;
					return "No";
				}
			}
		}
		
		if($approvedFlag=='Yes' && $verifiedFlag=='Yes')
			return "Yes";
		else
			return "No";
	}
	/***** Function to get BPR Details to find if it exists ******************************/
	public static function check_if_BPR_already_exists($mpr_definition_id_fk, $product_code, $mpr_version)
	{
		$params = [':mpr_definition_id_fk' => $mpr_definition_id_fk, ':product_code' => trim($product_code), ':mpr_version' => $mpr_version, ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
		$MIDetails = Yii::$app->db->createCommand('SELECT mpr_version FROM bpr_batch_processing_records WHERE mpr_definition_id_fk=:mpr_definition_id_fk and product_code=:product_code and mpr_version=:mpr_version and super_company_id_fk=:super_company_id_fk', $params)->queryOne();
		if(isset($MIDetails['mpr_version']))
			return 'Yes';
		else
			return 'No';
	}
	/************************************************************************************/
	public static function checkIfMPRApprovalVeriferIsDeletedBeforeApprove($mpr_approval_id_pk){
		$returnFlag = "No";
		$params = [':mpr_approval_id_pk' => $mpr_approval_id_pk, ':isDeleted' => '0'];
		
		$apprRows = Yii::$app->db->createCommand("SELECT 
														approval_person_id_fk,approval_status,verifier_person_id_fk,verified_status 
													FROM 
														bpr_mpr_approval 
													WHERE 
														mpr_approval_id_pk=:mpr_approval_id_pk 
													and isDeleted=:isDeleted
		", $params)->queryOne();
		
		if($apprRows['approval_status'] == 'Approved' && $apprRows['verified_status'] == 'Approved'){
			$returnFlag = "No";
		}else{
			//	check any one users is deleted
			$paramsPerson = [':approval_person_id_fk' => $apprRows['approval_person_id_fk'], ':isDeleted' => '1',':verifier_person_id_fk' => $apprRows['verifier_person_id_fk']];
			$appPersonRows = Yii::$app->db->createCommand("SELECT 
														person_id_pk 
													FROM 
														bpr_person 
													WHERE 
														(person_id_pk=:approval_person_id_fk AND isDeleted=:isDeleted) 
														OR (person_id_pk=:verifier_person_id_fk AND isDeleted=:isDeleted)
													and isDeleted=:isDeleted
			", $paramsPerson)->queryOne();
			if($appPersonRows['person_id_pk'] > 0){
				$returnFlag = "Yes";
			}
		}
		return $returnFlag;
	}
	/************************************************************************************/
	public static function check_if_MPR_exists_with_same($model,$edit_id,$product_id_fk,$product_code,$product_part,$author,$product_name,$formulation_id,$product_strength,$batch_size,$MPR_unit_id,$theoritical_yield,$gmp_company,$purpose,$scope,$mytoken)
	{
		$edit_id = (int)$edit_id;		
		$condition = '';
		if(strlen($product_id_fk) > 0){
			
			$params = [':product_id_fk' => $product_id_fk, ':product_code' => trim($product_code), ':product_part'=>trim($product_part), ':author'=>$author, ':product_name'=>trim($product_name), ':formulation_id'=> $formulation_id, ':product_strength'=>$product_strength, ':batch_size'=>$batch_size, ':MPR_unit_id'=> $MPR_unit_id, ':theoritical_yield'=>$theoritical_yield, ':gmp_company'=> $gmp_company, ':purpose'=>$purpose, ':scope'=>$scope, ':isCopied'=>'0', ':super_company_id_fk'=>Yii::$app->user->identity->super_company_id_fk ];
										
			$sql = "SELECT COUNT(mpr_defination_id_pk) as mis,mpr_defination_id_pk,mpr_token from bpr_mpr_defination where product_id_fk=:product_id_fk AND product_code=:product_code AND product_part=:product_part AND author=:author AND product_name=:product_name AND formulation_id=:formulation_id AND product_strength=:product_strength AND batch_size=:batch_size AND MPR_unit_id=:MPR_unit_id AND theoritical_yield=:theoritical_yield AND company_id_fk=:gmp_company AND purpose=:purpose AND scope=:scope AND isCopied=:isCopied  and super_company_id_fk=:super_company_id_fk ";
			
			$mis = Yii::$app->db->createCommand($sql,$params)->queryAll();
			$numMIS = (int)$mis[0]["mis"];
		
			if($numMIS > 0)
			{
				$mpr_defination_id_pk = (int)$mis[0]['mpr_defination_id_pk'];
				$rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_pk);
				if($rflag=='Yes')
				{
					if($mis[0]['mpr_token']!=$mytoken) 
						return 'No';
					else
						return 'Yes';
				}
			} else {
				return "No";
			}
		}
	}
	/************************************************************************************/
	public static function check_if_role_is_administrator($role_id){
		if(Yii::$app->user->identity->is_super_admin == 1){
			return "No";
		}else{
			$sql = "SELECT is_administrator from bpr_role where role_id_pk='".$role_id."' ";
			$role = Yii::$app->db->createCommand($sql)->queryOne();
			if($role['is_administrator']==1)
				return "Yes";
			else
				return "No";
		}
	}
	/************************************************************************************/
	public static function checkIfEquipmentExpiredOrDeleted($mpr_definition_id)
	{
		if($mpr_definition_id>0)
		{
			$params = [':mpr_defination_id_fk'=>$mpr_definition_id, ':isDeleted' => '0'];
			$equipments = Yii::$app->db->createCommand('SELECT em.product_id_fk,em.equipment_id_fk,e.caliberation_due_date,e.preventive_m_due_date,e.isDeleted FROM bpr_equipment_map as em JOIN bpr_equipment as e ON e.equipment_id_pk=em.equipment_id_fk WHERE em.mpr_defination_id_fk=:mpr_defination_id_fk AND em.isDeleted=:isDeleted', $params)->queryAll();
			
			if(is_array($equipments) && count($equipments)>0)
			{
				foreach($equipments as $ek=>$ev)
				{
					if($ev['isDeleted']=='1')
						return "Yes";
					if(date("Y-m-d",strtotime($ev['caliberation_due_date'])) < date("Y-m-d") || date("Y-m-d",strtotime($ev['preventive_m_due_date']))<date("Y-m-d"))
						return "Yes";
				}
			}
		}
		return "No";
	}
	/************************************************************************************/ 
	public static function checkIsBOMSAddedToMPR($mpr_definition_id)
	{
		if($mpr_definition_id>0)
		{
			$params = [':mpr_defination_id_fk'=>$mpr_definition_id, ':isDeleted' => '0'];
			$BillofMaterials = Yii::$app->db->createCommand('SELECT COUNT(bom_id_pk) as boms FROM bpr_bill_of_material WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted', $params)->queryAll();
			$numMaterials = (int)$BillofMaterials[0]["boms"];
		
			return ($numMaterials > 0?"Yes":"No");		
		}
		return "Yes";
	}
	
	/************************************************************************************/ 
	public static function checkIfBOMSApprovedOfMPR($mpr_definition_id)
	{
		if($mpr_definition_id>0)
		{
			$params = [':mpr_defination_id_fk'=>$mpr_definition_id, ':isDeleted' => '0'];
			$BillofMaterials = Yii::$app->db->createCommand('SELECT COUNT(bom_id_pk) as boms FROM bpr_bill_of_material WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted AND material_test_status in("", "Rejected", "Quarantine")', $params)->queryAll();
			$numMaterials = (int)$BillofMaterials[0]["boms"];
		
			return ($numMaterials > 0?"Yes":"No");
		}
		return "Yes";
	}
	/************************************************************************************/	
	public static function checkIsEqpsAddedToMPR($mpr_definition_id)
	{
		if($mpr_definition_id>0)
		{
			$params = [':mpr_defination_id_fk'=>$mpr_definition_id, ':isDeleted' => '0'];
					
			$Equipments = Yii::$app->db->createCommand('SELECT COUNT(equipment_map_id_pk) as eqps FROM bpr_equipment_map WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted', $params)->queryAll();
			$numEquipments = (int)$Equipments[0]["eqps"];
		
			return ($numEquipments > 0?"Yes":"No");
		}
		return "Yes";
	}	
	/************************************************************************************/
	public static function checkIsStepsAddedToMPR($mpr_definition_id){
		if($mpr_definition_id > 0){
			$params = [':mpr_defination_id_fk'=>$mpr_definition_id, ':isDeleted' => '0'];			
			$MISteps = Yii::$app->db->createCommand('SELECT COUNT(mi_id_pk) as mis FROM bpr_manufacturing_instruction WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted', $params)->queryAll();
			$numMISteps = (int)$MISteps[0]["mis"];
		
			return ($numMISteps > 0?"Yes":"No");
		}
		return "Yes";
	}
	/************************************************************************************/
	public static function checkIfProductDeletedofMPR($mpr_definition_id){
		if($mpr_definition_id > 0){
			$params = [':mpr_defination_id_pk'=>$mpr_definition_id];
			$product = Yii::$app->db->createCommand('SELECT m.product_id_fk, p.isDeleted FROM bpr_mpr_defination as m JOIN bpr_product as p ON p.product_id_pk=m.product_id_fk WHERE mpr_defination_id_pk=:mpr_defination_id_pk', $params)->queryOne();
			
			if(is_array($product) && count($product) > 0){
				if($product['isDeleted']=='1')
					return "Yes";	
			}
		}
		return "No";
	}
	/************************************************************************************/	
	public static function checkIfEquipmentExistsInDropdown($equipment_id_fk){
		$params = [':isDeleted'=>"0", 'curdate' => date("Y-m-d"), ':super_company_id_fk'=>Yii::$app->user->identity->super_company_id_fk];
		$Eqps = Yii::$app->db->createCommand('SELECT equipment_id_pk FROM bpr_equipment WHERE isDeleted=:isDeleted and (caliberation_due_date >= :curdate and preventive_m_due_date >= :curdate) and super_company_id_fk=:super_company_id_fk',$params)->queryAll();
		$existingEqps = array();
		if(is_array($Eqps) && count($Eqps) > 0){
			foreach($Eqps as $k=>$v){
				if($equipment_id_fk == $v['equipment_id_pk'])
					return "Yes";
			}
		}			
		return "No";
	}
	/************************************************************************************/
	public static function check_if_person_exists($person_id_pk)
	{
		$array = array('fullname' => '','exists' => '');
		if($person_id_pk > 0){
			$sql = "SELECT person_id_pk,isDeleted,CONCAT(first_name,' ',last_name) AS fullname from bpr_person where person_id_pk = '". $person_id_pk."'";
			$users = Yii::$app->db->createCommand($sql)->queryOne();
		
			$array['fullname'] = $users['fullname'];
			if($users['person_id_pk'] > 0 && $users['isDeleted'] == '0'){
				$array['exists'] = "Yes";
			} else {
				$array['exists'] = "No";
			}
		}
		return $array;
	}
	/************************************************************************************/
	/*
		$fieldArray = array('name' => trim($gmp_state_name),'country_id_fk' => intval($gmp_country_id_fk));
		$andWhere = "state_id_pk != 0";
	*/
	public static function check_If_Record_Exists($model,$fieldArray,$andWhere = ''){
		$count = 0;
		if(strlen(trim($andWhere)) > 0){
			$count = $model->find()->where($fieldArray)->andWhere($andWhere)->count();
		}else{
			$count = $model->find()->where($fieldArray)->count();
		}
		return ($count > 0?"Yes":"No");
	}	
	/************************************************************************************/
	public static function check_If_Record_Deleted($id,$tableName,$fieldName){
		$params = [':fieldName' => $id];
		$Row = Yii::$app->db->createCommand('SELECT isDeleted FROM '.$tableName.' WHERE '.$fieldName.'=:fieldName', $params)->queryOne();
		return ($Row['isDeleted'] == '1'?"Yes":"No");
	}
	/************************************************************************************/
}
