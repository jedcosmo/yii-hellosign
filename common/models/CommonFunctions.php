<?php
namespace common\models;

use Yii;
use yii\base\Model;
use yii\db\Query; 

class CommonFunctions extends Model
{

	 public static function post($value)
	 {
	 	return (trim($value));
	 }

	 public static function getRequest($value)
	 {
	 	return (trim($value));
	 }
	 
	 public static function viewDataFromSQL($value,$htmlentities=1)
	 {
	 	if($htmlentities==1)
	 		return stripslashes(htmlentities(trim($value)));
		else
			return stripslashes(trim($value));
	 }
	/***** Function to generate MPR Version Number ******************************/
    public static function generateMPRVersionNo($product_code)
	{
		$version = 0;
		$params = [':product_code' => $product_code, ':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk];
		$mprdef = Yii::$app->db->createCommand('SELECT MPR_version FROM bpr_mpr_defination WHERE product_code=:product_code and super_company_id_fk=:super_company_id_fk ORDER BY MPR_version DESC', $params)->queryOne();
		$mprver = (int)$mprdef['MPR_version']; 
		if(isset($mprdef['MPR_version']))
		{
			if($mprver && $mprver<10)
				$version = $mprver + 1;
			else
				$version = 0;
		}else{
			$version = 1;
		}
		return $version;
	}
	
	/***** Function to copy MPR definition into Bill Of Materials ******************************/
	public static function copyBillofMaterials($prev_mpr_def_id,$new_mpr_def_id)
	{
		$params = [':mpr_defination_id_fk' => $prev_mpr_def_id, ':isDeleted' => '0'];
		$billofMaterial = Yii::$app->db->createCommand('SELECT bom_id_pk, mpr_defination_id_fk, material_name, qty_branch, qb_unit_id_fk, composition, com_unit_id_fk, material_id, material_type_id_fk, product_part, vendor_id, vendor_name, vendor_lot, price_per_unit, maximum_qty, storage_condition, temperature_condition, country_id_fk, total_shelf_life, material_test_status, material_safety_data_sheet, environmental_protection_agency, select_a_file, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_bill_of_material WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted', $params)->queryAll();
		if(is_array($billofMaterial) && count($billofMaterial)>0)
		{
			foreach($billofMaterial as $k=>$v)
			{
				$commandInst = Yii::$app->db->createCommand()
								->insert('bpr_bill_of_material', [
								'mpr_defination_id_fk' => $new_mpr_def_id,
								'material_name' => $v['material_name'],
								'qty_branch' => $v['qty_branch'],
								'qb_unit_id_fk' => $v['qb_unit_id_fk'],
								'composition' => $v['composition'],
								'com_unit_id_fk' => $v['com_unit_id_fk'],
								'material_id' =>  $v['material_id'],
								'material_type_id_fk' =>  $v['material_type_id_fk'],
								'product_part' =>  $v['product_part'],
								'vendor_id' =>  $v['vendor_id'],
								'vendor_name' => $v['vendor_name'],
								'vendor_lot' =>  $v['vendor_lot'],
								'price_per_unit' =>  $v['price_per_unit'],
								'maximum_qty' =>  $v['maximum_qty'],
								'storage_condition' =>  $v['storage_condition'],
								'temperature_condition' =>  $v['temperature_condition'],
								'country_id_fk' =>  $v['country_id_fk'],
								'total_shelf_life' =>  $v['total_shelf_life'],
								'material_test_status' =>  $v['material_test_status'],
								'material_safety_data_sheet' =>  $v['material_safety_data_sheet'],
								'environmental_protection_agency' =>  $v['environmental_protection_agency'],
								'select_a_file' =>  $v['select_a_file'],
								'isDeleted' => '0',
								'addedby_person_id_fk' => Yii::$app->user->id,
								'created_datetime' => date("Y-m-d H:i:s"),	
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
							])->execute();	
			}
		}
	}
	
	/***** Function to copy MPR definition into Formulations ******************************/
	public static function copyFormulations($prev_mpr_def_id,$new_mpr_def_id)
	{
		$params = [':mpr_defination_id_fk' => $prev_mpr_def_id, ':isDeleted' => '0'];
		$formulations = Yii::$app->db->createCommand('SELECT f_id_pk, mpr_defination_id_fk, material_name, material_part, formulation_percentage, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_formulation WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted', $params)->queryAll();
		if(is_array($formulations) && count($formulations)>0)
		{
			foreach($formulations as $k=>$v)
			{
				$commandInst = Yii::$app->db->createCommand()
								->insert('bpr_formulation', [
								'mpr_defination_id_fk' => $new_mpr_def_id,
								'material_name' => $v['material_name'],
								'material_part' => $v['material_part'],
								'formulation_percentage' => $v['formulation_percentage'],
								'isDeleted' => '0',
								'addedby_person_id_fk' => Yii::$app->user->id,
								'created_datetime' => date("Y-m-d H:i:s"),	
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
							])->execute();	
			}
		}
	}
	
	/***** Function to copy MPR definition into equipments map ******************************/
	public static function copyEquipmentsMap($prev_mpr_def_id,$new_mpr_def_id)
	{
		$params = [':mpr_defination_id_fk' => $prev_mpr_def_id, ':isDeleted' => '0'];
		$billofMaterial = Yii::$app->db->createCommand('SELECT equipment_map_id_pk, mpr_defination_id_fk, product_id_fk, product_code, equipment_id_fk, equipment_name, equipment_model, calibration_due_date, preventive_m_due_date, activity, start_date_time, end_date_time, dept_assigned_to, cleaning_agent, batch, product_name, product_part, attachment, comments, operator_signature, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_equipment_map WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted', $params)->queryAll();
		if(is_array($billofMaterial) && count($billofMaterial)>0)
		{
			foreach($billofMaterial as $k=>$v)
			{
				$command = Yii::$app->db->createCommand()
							->insert('bpr_equipment_map', [
							'mpr_defination_id_fk' => $new_mpr_def_id,
							'product_id_fk' => $v['product_id_fk'],
							'product_code' => $v['product_code'],
							'equipment_id_fk' => $v['equipment_id_fk'],
							'equipment_name' => $v['equipment_name'],
							'equipment_model' => $v['equipment_model'],
							'calibration_due_date' => $v['calibration_due_date'],
							'preventive_m_due_date' => $v['preventive_m_due_date'],
							'activity' =>$v['activity'],
							'start_date_time' => $v['start_date_time'],
							'end_date_time' => $v['end_date_time'],
							'dept_assigned_to' => $v['dept_assigned_to'],
							'cleaning_agent' => $v['cleaning_agent'],
							'batch' => $v['batch'],
							'product_name' => $v['product_name'],
							'product_part' => $v['product_part'],
							'attachment' => $v['attachment'],
							'comments' => $v['comments'],
							'operator_signature' => $v['operator_signature'],
							'isDeleted' => '0',
							'addedby_person_id_fk' => Yii::$app->user->id,
							'created_datetime' => date("Y-m-d H:i:s"),
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
						])->execute();
			}
		}
	}
	
	/***** Function to copy MPR definition into equipments map ******************************/
	public static function copyManufacturingInstructions($prev_mpr_def_id,$new_mpr_def_id)
	{
		$params = [':mpr_defination_id_fk' => $prev_mpr_def_id, ':isDeleted'=>'0'];
		$billofMaterial = Yii::$app->db->createCommand('SELECT mi_id_pk, mpr_defination_id_fk, mi_step, mi_action, unit_id_fk, mi_range, target, perfomer, verifier, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_manufacturing_instruction WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted', $params)->queryAll();
		if(is_array($billofMaterial) && count($billofMaterial)>0)
		{
			foreach($billofMaterial as $k=>$v)
			{
				$command = Yii::$app->db->createCommand()
							->insert('bpr_manufacturing_instruction', [
							'mpr_defination_id_fk' => $new_mpr_def_id,
							'mi_step' => $v['mi_step'],
							'mi_action' => $v['mi_action'],
							'unit_id_fk' => $v['unit_id_fk'],
							'mi_range' => $v['mi_range'],
							'target' => $v['target'],
							'perfomer' => $v['perfomer'],
							'verifier' => $v['verifier'],
							'isDeleted' => '0',
							'addedby_person_id_fk' => Yii::$app->user->id,
							'created_datetime' => date("Y-m-d H:i:s"),
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
						])->execute();
			}
		}
	}
	
	/****** Change BPR Status to Quarantine after approval **************************/
	public static function quarantine_BPR_status($modelAppr,$bpr_id,$person_id,$bpr_appr_id,$column_name)
	{
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bpr_id);
		if($rflag=='Yes')
		{
			$apprDetails = $modelAppr->find()->where(['bpr_approval_id_pk'=>$bpr_appr_id])->one();
			$signature_id = (isset($apprDetails[$column_name]) && $apprDetails[$column_name]!='')?$apprDetails[$column_name]:'';
			$params = [':bpr_id_fk' => $bpr_id, ':status' => 'Quarantine'];
			$BPRStatus = Yii::$app->db->createCommand('SELECT status_id_pk FROM bpr_batch_status WHERE bpr_id_fk=:bpr_id_fk and status=:status', $params)->queryOne();
			if(isset($BPRStatus) && isset($BPRStatus['status_id_pk']) && $BPRStatus['status_id_pk']>0)
			{	/* it's already exist in table  */	}
			else
			{
				$now = date("Y-m-d H:i:s");
				$command = Yii::$app->db->createCommand()
							->insert('bpr_batch_status', [
							'bpr_id_fk' => $bpr_id,
							'status' => 'Quarantine',
							'person_id_fk' => $person_id,
							'status_datetime' => $now,
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
							'HS_signature_id' => $signature_id,
						])->execute();
				$command = Yii::$app->db->createCommand()
					->update('bpr_batch_processing_records', [
					'manufacturing_date'=> $now,	
				],"bpr_id_pk='".$bpr_id."'")->execute();
			}
		}
	}
	/****************************************************************************************************/
	public static function display_all_role_modules($modules)
	{
		$roleModules = getCommonFunctions::get_all_role_modules();
		if($modules=='__ALL__'){
			$retStr = "All";
		}else{
			$savedModules = array();
			$savedModules = explode(",",$modules);
			$retStr = '';
			if(is_array($roleModules) && count($roleModules) > 0){
				foreach($roleModules as $k=>$v){ 
					if(in_array($v['name_pk'],$savedModules)){
						$retStr .= $v['display_name'].", ";
					}
				}
			}
		}
		$retStr = trim($retStr,", ");
		return $retStr;
	}
	
	/****************************************************************************************************/
	public static function isAccessible($modulename)
	{
	
		 $role_id_fk = Yii::$app->user->identity->role_id_fk;
		 $sql = "SELECT name,modules,isDeleted from bpr_role where role_id_pk= '".$role_id_fk."' and isDeleted='0'"; 
		 $roleDetails = Yii::$app->db->createCommand($sql)->queryOne();
		 if($roleDetails['modules']=='__ALL__')
		 	return true;
		 else
		 {
		 	$roleModules = array();
			$roleModules = explode(",",$roleDetails['modules']);
			
			if(is_array($roleModules) && count($roleModules)>0)
			{
				if(in_array($modulename,$roleModules))
					return true;
				if(in_array('RM_Audit_Log',$roleModules) && strstr($_SERVER['REQUEST_URI'],"al=1"))
					return true;
			}
		 }
		 return false;
	}
	
	/****************************************************************************************************/
	public static function isAccessibleMPRorBPR($modulename1,$modulename2)
	{
	
		 $role_id_fk = Yii::$app->user->identity->role_id_fk;
		 $sql = "SELECT name,modules,isDeleted from bpr_role where role_id_pk= '".$role_id_fk."' and isDeleted='0'"; 
		 $roleDetails = Yii::$app->db->createCommand($sql)->queryOne();
		 if($roleDetails['modules']=='__ALL__')
		 	return true;
		 else
		 {
		 	$roleModules = array();
			$roleModules = explode(",",$roleDetails['modules']);
			
			if(is_array($roleModules) && count($roleModules)>0)
			{
				if(in_array($modulename1,$roleModules) || in_array($modulename2,$roleModules))
					return true;
				if(in_array('RM_Audit_Log',$roleModules) && strstr($_SERVER['REQUEST_URI'],"al=1"))
					return true;
			}
		 }
		 return false;
	}
	
	/****************************************************************************************************/
	public static function isPersonHavingAccessToModule($personid, $modulename)
	{
		 $personDetails = getCommonFunctions::getPersonDetails($personid);
		 $role_id_fk = $personDetails['role_id_fk'];
		 $sql = "SELECT name,modules,isDeleted from bpr_role where role_id_pk= '".$role_id_fk."' and isDeleted='0'"; 
		 $roleDetails = Yii::$app->db->createCommand($sql)->queryOne();
		 if($roleDetails['modules']=='__ALL__')
		 	return true;
		 else
		 {
		 	$roleModules = array();
			$roleModules = explode(",",$roleDetails['modules']);
			
			if(is_array($roleModules) && count($roleModules)>0)
			{
				if(in_array($modulename,$roleModules))
					return true;
			}
		 }
		 return false;
	}
	/****************************************************************************************************/
	public static function checkPersonCompanyAccess($model){	
		if($model->super_company_id_fk==Yii::$app->user->identity->super_company_id_fk)
			return true;
		else
			return false;
	}
	
	public static function isAccessibleMaster()
	{
		if(Yii::$app->user->identity->is_super_admin==1)
			return true;
		else
			return false;
	}
	/****************************************************************************************************/
	public static function updateRecentActivityTime($personid)
	{
		$now = date("Y-m-d H:i:s");
		$command = Yii::$app->db->createCommand()
					->update('bpr_person', [
					'recent_activity_datetime'=> $now,
				],"person_id_pk='".$personid."'")->execute();
	}
	/****************************************************************************************************/
	public static function lockThisRecord($id,$model,$tablename,$columnname)
	{
		if($model)
		{
			if($model->lock_flag==1 && $model->locked_by!=Yii::$app->user->identity->person_id_pk)
			{
				return false;
			}
			elseif($model->lock_flag==1 && $model->locked_by==Yii::$app->user->identity->person_id_pk)
			{
				return true;
			}
			elseif($model->lock_flag!=1)
			{
				$command = Yii::$app->db->createCommand()
						->update($tablename, [
						'lock_flag'=> 1,
						'locked_by'=> Yii::$app->user->identity->person_id_pk,
					],$columnname."='".$id."'")->execute();
				return true;
			}
			else
			{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
	/****************************************************************************************************/
	public static function unlockTheRecord()
	{		
		$tablesArray = ['bpr_person','bpr_company','bpr_unit','bpr_product','bpr_equipment','bpr_role','bpr_person_company','bpr_mpr_defination','bpr_batch_processing_records','bpr_city','bpr_country','bpr_state'];
		if(is_array($tablesArray) && count($tablesArray)>0)
		{
			foreach($tablesArray as $tk=>$tv)
			{
				$command = Yii::$app->db->createCommand()
					->update($tv, [
					'lock_flag'=> 0,
					'locked_by'=> 0,
				],"locked_by='".Yii::$app->user->identity->person_id_pk."'")->execute();
			}
		}
	}
	
	/****************************************************************************************************/
	public static function unlockAllUserRecords()
	{	
		$date = date("Y-m-d H:i:s");
		$time = strtotime($date);
		$time = $time - (15 * 60);
		$date = date("Y-m-d H:i:s", $time);
		
		$params = [':lock_flag'=>1, ':nowdt'=>$date];
		
		
		$tablesArray = ['bpr_person','bpr_company','bpr_unit','bpr_product','bpr_equipment','bpr_role','bpr_person_company','bpr_mpr_defination','bpr_batch_processing_records','bpr_city','bpr_country','bpr_state'];
		if(is_array($tablesArray) && count($tablesArray)>0)
		{
			foreach($tablesArray as $tk=>$tv)
			{
				$rRows = Yii::$app->db->createCommand("SELECT t1.locked_by FROM ".$tv." as t1 JOIN bpr_person as p ON p.person_id_pk=t1.locked_by WHERE t1.lock_flag=:lock_flag AND p.recent_activity_datetime<:nowdt", $params)->queryAll();
				
				if(is_array($rRows) && count($rRows)>0)
				{
					foreach($rRows as $k=>$v)
					{
						$command = Yii::$app->db->createCommand()
							->update($tv, [
							'lock_flag'=> 0,
							'locked_by'=> 0,
						],"lock_flag=1 AND locked_by='".$v['locked_by']."'")->execute();
					}
				}
			}
		}
	}
	/****************************************************************************************************/
	//Update session id of logged-in user to have track of multiple instances of webpage
	public static function updateSessionUserId($platform, $browser, $version, $ipaddress)
	{
		if(Yii::$app->user->isGuest==false && Yii::$app->user->identity->person_id_pk>0)
		{
			$id = Yii::$app->session->getId();
			$command = Yii::$app->db->createCommand()
				->update('bpr_person', [
				'current_session' => trim($id),
				'platform'		=> $platform,
				'browser'		=> $browser . ' - ' . $version,
				'ip_address'	=> $ipaddress

			],"person_id_pk='".Yii::$app->user->identity->person_id_pk."'")->execute();
		}
	}
	
	public static function checkSessionUserId()
	{
		if(Yii::$app->user->isGuest==false && Yii::$app->user->identity->person_id_pk>0)
		{
			$id = Yii::$app->session->getId();
			$params = [':person_id_pk' => Yii::$app->user->identity->person_id_pk];
			$userdetails = Yii::$app->db->createCommand('SELECT current_session FROM bpr_person WHERE person_id_pk=:person_id_pk', $params)->queryOne();
			if($userdetails['current_session']!=$id)
				return false;
			else
				return true;
		}
		else
			return true;
	}
	/****************************************************************************************************/
	/*** Insert Default Units for newly registered company ***/
	public static function dumpDefaultUnitsForCompany($supercompid)
	{
		$params = [];
		$unitDetails = Yii::$app->db->createCommand('SELECT unit,description FROM bpr_unit_conversion', $params)->queryAll();
		if(is_array($unitDetails) && count($unitDetails)>0)
		{
			foreach($unitDetails as $k=>$v)
			{
				$command = Yii::$app->db->createCommand()
					->insert('bpr_unit', [
					'name' => $v['unit'],
					'description' => $v['description'],
					'symbols'=> '',
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->identity->person_id_pk,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => $supercompid,
				])->execute();
			}
		}
	}
	
	/****************************************************************************************************/
	//Convert unit to KG
	public static function unitConversionToKG($unit,$qty)
	{
		$qtyInKG = 0;
		$params = [':unit'=>$unit];
		$unitDetails = Yii::$app->db->createCommand('SELECT unit,description,kg_conversion FROM bpr_unit_conversion WHERE unit=:unit', $params)->queryOne();
		if(is_array($unitDetails) && isset($unitDetails['unit']) && $unitDetails['unit']!='')
		{
			$qtyInKG = $qty * $unitDetails['kg_conversion'];
		}
		else
		{
			$qtyInKG = '--';
		}
		
		return $qtyInKG;
	}
	
	/****************************************************************************************************/
	/*** Insert Default country,state & cities for newly registered company ***/
	public static function dumpDefaultGeographicsForCompany($supercompid)
	{
		$countryArr = ['101'=>'India'];
		if(is_array($countryArr) && count($countryArr)>0)
		{
			foreach($countryArr as $ck=>$cv)
			{
				/****** Insert Country *******************/
				$params = [':country_id_pk'=>$ck];
				$countryDetails = Yii::$app->db->createCommand('SELECT country_id_pk, shortname, name FROM bpr_country_dump WHERE country_id_pk=:country_id_pk', $params)->queryOne();
				if(is_array($countryDetails) && count($countryDetails)>0)
				{
					$command = Yii::$app->db->createCommand()
							->insert('bpr_country', [
							'name' => $countryDetails['name'],
							'isDeleted' => '0',
							'addedby_person_id_fk' => Yii::$app->user->identity->person_id_pk,
							'created_datetime' => date("Y-m-d H:i:s"),	
							'super_company_id_fk' => $supercompid,
						])->execute();
					$newCountryID = Yii::$app->db->getLastInsertID();
					
					/****** Insert States *******************/
					$paramsState = [':country_id_fk'=>$ck];
					$statesDetails = Yii::$app->db->createCommand('SELECT state_id_pk,name,country_id_fk FROM bpr_state_dump WHERE country_id_fk=:country_id_fk', $paramsState)->queryAll();
					if(is_array($statesDetails) && count($statesDetails)>0)
					{
						foreach($statesDetails as $sk=>$sv)
						{
							$command = Yii::$app->db->createCommand()
									->insert('bpr_state', [
									'name' => $sv['name'],
									'country_id_fk' => $newCountryID,
									'isDeleted' => '0',
									'addedby_person_id_fk' => Yii::$app->user->identity->person_id_pk,
									'created_datetime' => date("Y-m-d H:i:s"),	
									'super_company_id_fk' => $supercompid,
								])->execute();
							$newStateID = Yii::$app->db->getLastInsertID();
							
							/****** Insert Cities *******************/
							$paramsCity = [':country_id_fk'=>$ck,':state_id_fk'=>$sv['state_id_pk']];
							$citiesDetails = Yii::$app->db->createCommand('SELECT name,country_id_fk,state_id_fk FROM bpr_city_dump WHERE country_id_fk=:country_id_fk AND state_id_fk=:state_id_fk', $paramsCity)->queryAll();
							if(is_array($citiesDetails) && count($citiesDetails)>0)
							{
								foreach($citiesDetails as $citykey=>$cityval)
								{
									$command = Yii::$app->db->createCommand()
											->insert('bpr_city', [
											'name' => $cityval['name'],
											'country_id_fk' => $newCountryID,
											'state_id_fk' => $newStateID,
											'isDeleted' => '0',
											'addedby_person_id_fk' => Yii::$app->user->identity->person_id_pk,
											'created_datetime' => date("Y-m-d H:i:s"),	
											'super_company_id_fk' => $supercompid,
										])->execute();
								}
							}
						}
					}
				}
				/****************************************/				
			}
		}
	}
	/**** Function to send email on person accout update ****************************/
	public static function sendAccountUpdateEmail($subject,$emailContent,$toEmail)
	{
		$from = Yii::$app->name." Admin"; 
		$fromEmail = Yii::$app->params['adminEmail'];

		$email_msg = file_get_contents(Yii::$app->basePath."/web/emailtemplate/verify-email.html");
		$email_msg = str_replace('{email_content}',$emailContent,$email_msg);
		$email_msg = str_replace('{subject}',$subject,$email_msg);
		$email_msg = str_replace('{base_url}',Yii::$app->params['baseURLEmailTempPath'],$email_msg);
		$email_msg = str_replace('{sitetitle}',Yii::$app->name,$email_msg);
		$email_msg = str_replace('{date}',date("Y"),$email_msg);

		$name = '=?UTF-8?B?'.base64_encode($from).'?=';
		$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
	/*	$headers = "From: $from <{$fromEmail}>\r\n".
				"Reply-To: {$fromEmail}\r\n".
				"Content-type: text/html; charset=UTF-8";*/
				
		$headers = "From: $from <{$fromEmail}>\r\n";
		$headers.= "Reply-To: {$fromEmail}\r\n";
		$headers.= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
				
		//echo $email_msg; exit;
		@mail(trim($toEmail),$subject,$email_msg,$headers);
	}
	
	/****************************************************************************************************/
}
