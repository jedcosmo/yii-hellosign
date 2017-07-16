<?php

namespace backend\modules\bprrecords\controllers;

use Yii;
use backend\modules\activitylog\models\ActivityLog;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\adminUser\models\Admin;
use common\models\AdminLoginForm;
use backend\modules\bprrecords\models\Bprrecords;
use backend\modules\bprrecords\models\BprrecordsSearch;
use backend\modules\billofmaterial\models\Billofmaterial;
use backend\modules\billofmaterial\models\BillofmaterialSearch;
use backend\modules\formulation\models\Formulation;
use backend\modules\formulation\models\FormulationSearch;
use backend\modules\equipmentmap\models\Equipmentmap;
use backend\modules\equipmentmap\models\EquipmentmapSearch;
use backend\modules\minstructions\models\Minstructions;
use backend\modules\minstructions\models\MinstructionsSearch;
use backend\modules\bprapprovals\models\Bprapprovals;
use backend\modules\bprapprovals\models\BprapprovalsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * BprrecordsController implements the CRUD actions for Bprrecords model.
 */
class BprrecordsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
			'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'rules' => [
                            // allow authenticated users
                            [
                                'allow' => true,
                                'roles' => ['@'],
								'matchCallback' => function ($rule, $action) {
								   return CommonFunctions::isAccessible('RM_BPR');
								 }
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Bprrecords models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of BPR',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new BprrecordsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
    /*********Print to PDF************************************/
	public function actionPdf($id)
	{	
		$model = $this->findModel($id);
				
		$billOfMaterials = Billofmaterial::find()->where(['mpr_defination_id_fk'=>$model->mpr_definition_id_fk,'isDeleted'=>'0'])->orderby('bom_id_pk ASC')->all();
		
		$formulations = Formulation::find()->where(['mpr_defination_id_fk'=>$model->mpr_definition_id_fk,'isDeleted'=>'0'])->orderby('f_id_pk ASC')->all();
		
		$eQuipments = Equipmentmap::find()->where(['mpr_defination_id_fk'=>$model->mpr_definition_id_fk,'isDeleted'=>'0'])->orderby('equipment_map_id_pk DESC')->all();
			
		$mInstructionSteps = Minstructions::find()->where(['mpr_defination_id_fk'=>$model->mpr_definition_id_fk,'isDeleted'=>'0'])->orderby('mi_id_pk ASC')->all();
	
		$bprApprovals = Bprapprovals::find()->where(['bpr_id_fk'=>$model->bpr_id_pk,'isDeleted'=>'0'])->orderby('bpr_approval_id_pk ASC')->all();
		
		Yii::$app->response->format = 'pdf';
        $this->layout = '//print';
		return $this->renderPartial('pdfview', [
            'model' => $model,
			'billOfMaterials' => $billOfMaterials,
			'eQuipments' => $eQuipments,
			'mInstructionSteps' => $mInstructionSteps,
			'bprApprovals' => $bprApprovals,
			'formulations' => $formulations,
        ]);
	}
	/**********************************************************/
	
	public function actionManufacturing_inst_view($id, $bprid, $mode='')
	{		
		$BPRmodel = $this->findModel($bprid);
		
		$params = [':mi_id_pk' => $id, ':isDeleted' => '0'];
		$mInstructionSteps = Yii::$app->db->createCommand('SELECT mi_id_pk, mpr_defination_id_fk, mi_step, mi_action, unit_id_fk, mi_range, target, perfomer, verifier, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_manufacturing_instruction WHERE mi_id_pk=:mi_id_pk and isDeleted=:isDeleted', $params)->queryOne();
		
		$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($BPRmodel->mpr_definition_id_fk);
		
		return $this->renderPartial('_manufacturingInstView', [
				'BPRmodel' => $BPRmodel,
				'mInstructionSteps' => $mInstructionSteps,
				'MPRDetails' => $MPRDetails,
			]);
	}

	public function actionBpr_instruction($mi_id_pk, $bpr_id_fk, $actmember,$bpr_status_signature_id='')
	{
		$signType = $actmember; 
		$logmessage = ''; $logact = '';
		$BPRmodel = $this->findModel($bpr_id_fk);
		$session = Yii::$app->session;
		$params = [':mi_id_pk' => $mi_id_pk, ':isDeleted' => '0'];
		$mInstructionSteps = Yii::$app->db->createCommand('SELECT mi_id_pk, mpr_defination_id_fk, mi_step, mi_action, unit_id_fk, mi_range, target, perfomer, verifier, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_manufacturing_instruction WHERE mi_id_pk=:mi_id_pk and isDeleted=:isDeleted', $params)->queryOne();
		
		$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($BPRmodel->mpr_definition_id_fk);
		$bprError = '';$bprSuccess='';
		
		$request = Yii::$app->request;
		$performer_id_fk = intval($request->post('performer_id_fk',0));
		$bpr_id_pk = intval($request->post('bpr_id_pk',0));
		$mi_id_pk = intval($request->post('mi_id_pk',0));
		$mpr_definition_id_fk = intval($request->post('mpr_definition_id_fk',0));
		$result = trim($request->post('result',''));
		$comments = trim($request->post('comments',''));
		$deviation_comments = trim($request->post('deviation_comments',''));
		$colHelloSignApr = 'HS_approver_signature_id';
		
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bpr_id_pk);
		
		if(isset($_POST) && $performer_id_fk>0 && $rflag!='Yes')
		{
			$MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($bpr_id_pk,$mi_id_pk);
			
			if($actmember=='performer')
			{
				$col1 = 'arppover_person_id_fk'; 
				$col2 = 'approval_datetime';
				$colHelloSignApr = 'HS_approver_signature_id';
				$logmessage = 'Approved the manufacturing step';
				$logact = Yii::$app->params['audit_log_action']['APPROVED'];
				$bprSuccess = 'Step approved by performer successfully!';
				if($deviation_comments!='')
					$actmember = 'QAPerson';
				elseif($mInstructionSteps['verifier']=='Yes')
					$actmember = 'verifier';
				else
					$actmember = '';
				
				if(trim($deviation_comments)!='')
				{
					$bprSuccess = ''; 
					$bprError = 'Step has a deviation!';
				}
			}
			elseif($actmember=='verifier')
			{
				$col1 = 'verifier_person_id_fk'; 
				$col2 = 'verified_datetime';
				$colHelloSignApr = 'HS_verifier_signature_id';
				$logmessage = 'Verified the manufacturing step';
				$logact = Yii::$app->params['audit_log_action']['VERIFIED'];
				$bprSuccess = 'Step verified by verifier successfully!';
				if(isset($MiApprovalDetails) && $MiApprovalDetails['instruction_id_pk']>0 && $_POST['deviation_comments']!='' && $MiApprovalDetails['QA_person_id_fk']<=0)
					$actmember = 'QAPerson';
				else
					$actmember = '';
				if(trim($deviation_comments)!='' && $MiApprovalDetails['QA_person_id_fk']<=0)
				{
					$bprSuccess = ''; 
					$bprError = 'Step has a deviation!';
				}
			}
			elseif($actmember=='QAPerson')
			{
				$col1 = 'QA_person_id_fk'; 
				$col2 = 'QA_datetime';
				$colHelloSignApr = 'HS_reviewer_signature_id';
				$logmessage = 'Reviewed & signed the manufacturing step';
				$logact = Yii::$app->params['audit_log_action']['REVIEWED'];
				$bprSuccess = 'Step reviewed by QA successfully!';
			if(isset($MiApprovalDetails) && $MiApprovalDetails['instruction_id_pk']>0 && $mInstructionSteps['verifier']=='Yes' && $MiApprovalDetails['verifier_person_id_fk']>0)
				$actmember = '';
			elseif(isset($MiApprovalDetails) && $MiApprovalDetails['instruction_id_pk']>0 && $mInstructionSteps['verifier']=='No' && $MiApprovalDetails['verifier_person_id_fk']==0)
				$actmember = '';
			else
				$actmember = 'verifier';
			}
			elseif($actmember=='noone')
			{
				$col1 = 'no_person_id_fk'; 
				$col2 = 'no_datetime';
				$logmessage = 'Approved the manufacturing step';
				$logact = Yii::$app->params['audit_log_action']['APPROVED'];
				$bprSuccess = 'Step confirmed successfully!';
				$actmember = '';
				if(trim($deviation_comments)!='' && $MiApprovalDetails['QA_person_id_fk']<=0)
				{
					$bprSuccess = ''; 
					$bprError = 'Step has a deviation!';
				}
				
			}
			$signType = $actmember;  
			if(isset($MiApprovalDetails) && $MiApprovalDetails['instruction_id_pk']>0)
			{
				if($MiApprovalDetails[$col1]<=0 && ($MiApprovalDetails[$col2]=='' || $MiApprovalDetails[$col2]=='0000-00-00 00:00:00'))
				{
					$command = Yii::$app->db->createCommand()
							->update('bpr_instructions', [
							'bpr_id_fk' => $bpr_id_pk,
							'mpr_definition_id_fk' => $mpr_definition_id_fk,
							'mi_id_pk' => $mi_id_pk,
							'result' => trim($result),
							'comments' => trim($comments),
							'deviation_comments'=> trim($deviation_comments),
							$col1 => $performer_id_fk,
							$col2 => date("Y-m-d H:i:s"),
						],'instruction_id_pk="'.$MiApprovalDetails['instruction_id_pk'].'"')->execute();
						$session->set('bprSuccess', $bprSuccess);
						$session->set('bprError', $bprError);
					ActivityLog::logUserActivity($performer_id_fk,Yii::$app->params['audit_log_screen_name']['BPR_Manufacturing_Instructions'],$logact,$logmessage,$_SERVER['HTTP_REFERER']);
				}
				else
				{
					$session->set('bprSuccess', '');
					if($col1 == 'arppover_person_id_fk')
						$session->set('bprError', 'Step already performed');
					elseif($col1 == 'verifier_person_id_fk')
						$session->set('bprError', 'Step already verified');
					elseif($col1 == 'QA_person_id_fk')
						$session->set('bprError', 'Step already reviewed');
					elseif($col1 == 'no_person_id_fk')
						$session->set('bprError', 'Step already confirmed');
					echo "gotoInstructions";
					exit;
				}
			}
			else
			{
				$bpr_status_signature_id = isset($bpr_status_signature_id)?$bpr_status_signature_id:'';
				
				$command = Yii::$app->db->createCommand()
							->insert('bpr_instructions', [
							'bpr_id_fk' => $bpr_id_pk,
							'mpr_definition_id_fk' => $mpr_definition_id_fk,
							'mi_id_pk' => $mi_id_pk,
							'result' => trim($result),
							'comments' => trim($comments),
							'deviation_comments'=> trim($deviation_comments),
							$col1 => $performer_id_fk,
							$col2 => date("Y-m-d H:i:s"),
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
							$colHelloSignApr => $bpr_status_signature_id, //'HS_approver_signature_id' => $bpr_status_signature_id,
						])->execute();
						$session->set('bprSuccess', $bprSuccess);
						$session->set('bprError', $bprError);
					ActivityLog::logUserActivity($performer_id_fk,Yii::$app->params['audit_log_screen_name']['BPR_Manufacturing_Instructions'],$logact,$logmessage,$_SERVER['HTTP_REFERER']);
			}
		}	
		if($actmember=='' || $rflag=='Yes')
		{
			echo "gotoInstructions"	;
		}
		else
		{
			return $this->renderPartial('_bpr_instruction', [
				'BPRmodel' => $BPRmodel,
				'mInstructionSteps' => $mInstructionSteps,
				'MPRDetails' => $MPRDetails,
				'actmember' => $actmember,
				'signType' => $signType,
			]);
		}	
	}
	
	public function actionMprversion_popup($product_code)
	{
		$product_code = base64_decode($product_code);
		$mprVersion = getCommonFunctions::get_MPR_From_Product_Code($product_code);
		
		return $this->renderPartial('_mprversion_popup', [
           		'mprVersion' => $mprVersion,
				'product_code' => $product_code,
        ]);
	}
	
	public function actionAjaxmprlisting($product_part,$product_code,$sort_mpr)
	{
		$mprVersion = getCommonFunctions::get_MPR_From_Product_Part($product_code,$product_part,$sort_mpr);
		
		return $this->renderPartial('_search_part', [
           		'mprVersion' => $mprVersion,
				'product_code' => $product_code,
        ]);
	}
	/*
	* Get State combobox
	*/
	public function actionMprversionlist($product_code)
    {
		
		$mprVersion = getCommonFunctions::get_MPR_From_Product_Code($product_code);
	
    	echo "<option value=''>--- Select One ---</option>";
        if(is_array($mprVersion) && count($mprVersion)>0){
			
            foreach($mprVersion as $k=>$v){
                echo "<option value='".$k."'>".$v."</option>";
            }
        }
	}
	
	public function actionMprversiondetails($mpr_def_id)
    {
		$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($mpr_def_id);
		
		$content = '<label class="col-sm-3">Part #</label><div class="col-sm-8">'.wordwrap($MPRDetails['product_part'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Author</label><div class="col-sm-8">'.wordwrap(getCommonFunctions::getPersonName($MPRDetails['author']),25,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Product Name</label><div class="col-sm-8">'.wordwrap($MPRDetails['product_name'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Formulation Id</label><div class="col-sm-8">'.wordwrap($MPRDetails['formulation_id'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Product Strength</label><div class="col-sm-8">'.wordwrap($MPRDetails['product_strength'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Batch Size</label><div class="col-sm-8">'.wordwrap($MPRDetails['batch_size'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Unit</label><div class="col-sm-8">'.wordwrap(getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']),25,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Theoritical Yield</label><div class="col-sm-8">'.wordwrap($MPRDetails['theoritical_yield'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Company</label><div class="col-sm-8">'.wordwrap(getCommonFunctions::getFieldNameValue("bpr_company","name","company_id_pk",$MPRDetails['company_id_fk']),25,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Purpose</label><div class="col-sm-8">'.wordwrap($MPRDetails['purpose'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>
					<label class="col-sm-3">Scope</label><div class="col-sm-8">'.wordwrap($MPRDetails['scope'],40,"<br/>",1).'</div><div class="col-sm-12"><br/></div>';
    	return $content;
	}
	
	
	public function actionChecklogincredentials()
	{
		$loggedPersonID = getCommonFunctions::getPersonFromUsername($_REQUEST['username'], $_REQUEST['password']);
		if($loggedPersonID>0)
		{
			if(Yii::$app->user->id==$loggedPersonID)
				echo "success";
			else
				echo "You are not authorized person to perform this action.";
		}
		else
			echo "Incorrect username or password";
	}
	
	public function actionSign_approve($bpr_id,$mpr_def_id,$mi_id,$signType)
	{
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($bpr_id);
		$MiApprovalDetails = getCommonFunctions::getMiApprovalDetails($bpr_id,$mi_id);
		
		$instruction_id_pk = (isset($MiApprovalDetails) && isset($MiApprovalDetails['instruction_id_pk']))?$MiApprovalDetails['instruction_id_pk']:0;
		
		$request = Yii::$app->request;
		$username = trim($request->post('username',''));
		$password = trim($request->post('password',''));
		
		if(isset($_POST) && strlen($username)>0)
		{
			$personDetails = array();
			$loggedPersonID = getCommonFunctions::getPersonFromUsername($username, $password);
			if ($loggedPersonID>0 && Yii::$app->user->id==$loggedPersonID) 
			{
				$personDetails = getCommonFunctions::getPersonDetails($loggedPersonID);
				$output = array('success'=>'1','personDetails' => $personDetails);	
			}
			else
			{
				$output = array('success'=>'0','personDetails' => $personDetails);
			}
			echo json_encode($output);
		}
		else
		{
			return $this->renderPartial('_sign_approve', [
           		'model' => $model,
				'bpr_id' => $bpr_id,
				'mpr_def_id' => $mpr_def_id,
				'mi_id' => $mi_id,
				'signType' => $signType,
				'instruction_id_pk' => $instruction_id_pk,
        	]);
		}
		
	}
	
	
	public function actionChange_bpr_status($status, $bprid)
	{
	
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($bprid);
		$bprDetails = getCommonFunctions::get_BPR_Record_Details($bprid);
		$bprPreviousStatus = getCommonFunctions::get_BPR_all_status_approver($bprid);
		if($status=='Approved')
		{
			$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($bprDetails['mpr_definition_id_fk']);
			$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($bprDetails['mpr_definition_id_fk']);
		}
		else
		{
			$eflag = '';
			$pflag = '';
		}
		
		$request = Yii::$app->request;
		$username = trim($request->post('username',''));
		$password = trim($request->post('password',''));
		$bpr_status_signature_id = trim($request->post('bpr_status_signature_id',''));
		
		if(isset($_POST) && strlen($username)>0)
		{
			$personDetails = array();
			$loggedPersonID = getCommonFunctions::getPersonFromUsername($username, $password);
			if ($loggedPersonID>0 && Yii::$app->user->id==$loggedPersonID) 
			{
				$session = Yii::$app->session;
				$command = Yii::$app->db->createCommand()
							->insert('bpr_batch_status', [
							'bpr_id_fk' => $bprid,
							'status' => $status,
							'person_id_fk' => $loggedPersonID,
							'status_datetime' => date("Y-m-d H:i:s"),
							'HS_signature_id' => $bpr_status_signature_id,
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
						])->execute();
				$output = array('success'=>'1');
				$session->set('bprStatusSuccess', "BPR status changed successfully.");
				
				$viewUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$bprid;
				$actionStatus = strtoupper($status);
				
				ActivityLog::logUserActivity($loggedPersonID,Yii::$app->params['audit_log_screen_name']['BPR_Status'],Yii::$app->params['audit_log_action'][$actionStatus],'Updated BPR status to "'.$status.'"',$viewUrl);
			}
			else
			{
				$output = array('success'=>'0');
			}
			echo json_encode($output);
		}
		else
		{
			return $this->renderPartial('_status_approve', [
           		'model' => $model,
				'bpr_id' => $bprid,
				'status' => $status,
				'eflag' => $eflag,
				'pflag' => $pflag,
				'signType' => $status,
				'bprPreviousStatus' => $bprPreviousStatus,
        	]);
		}
	
		
	}
	
	
	public function actionEquipment_operator_signature($bpr_id, $mpr_def_id,$equipment_map_id_pk,$signType)
	{
		$model = new AdminLoginForm();
				
		$request = Yii::$app->request;
		$username = trim($request->post('username',''));
		$password = trim($request->post('password',''));
		$bpr_status_signature_id = trim($request->post('bpr_status_signature_id',''));
		
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bpr_id);
		
		if(isset($_POST) && strlen($username)>0 && $rflag!='Yes')
		{
			$personDetails = array();
			$loggedPersonID = getCommonFunctions::getPersonFromUsername($username, $password);
			if ($loggedPersonID>0 && Yii::$app->user->id==$loggedPersonID) 
			{
				$params = [':mpr_defination_id_fk' => $mpr_def_id, ':equipment_map_id_pk' => $equipment_map_id_pk];
				$equipments = Yii::$app->db->createCommand('SELECT approved_status  FROM bpr_equipment_map WHERE mpr_defination_id_fk=:mpr_defination_id_fk and equipment_map_id_pk=:equipment_map_id_pk', $params)->queryOne();
				/*if(isset($equipments) && count($equipments)>0 && $equipments['approved_status']=='Approved')
				{
					$session = Yii::$app->session;
					$session->set('bprEqpError', "Equipment already signed by operator.");
					$output = array('success'=>'1');
				}
				else
				{*/
					$session = Yii::$app->session;
					$command = Yii::$app->db->createCommand()
							->insert('bpr_operator_signature_status', [
							'eqp_map_id_fk' => $equipment_map_id_pk,
							'bpr_id_fk' => $bpr_id,
							'mpr_definition_id_fk' => $mpr_def_id,
							'approved_status' => 'Approved',
							'approved_datetime' => date("Y-m-d H:i:s"),
							'approved_by_person_id_fk' => $loggedPersonID,
							'HS_signature_id' => $bpr_status_signature_id,
						])->execute();
					/*$command = Yii::$app->db->createCommand()
								->update('bpr_equipment_map', [
								'approved_status' => 'Approved',
								'approved_datetime' => date("Y-m-d H:i:s"),
								'approved_by_person_id_fk' => $loggedPersonID,
								'HS_signature_id' => $bpr_status_signature_id,
							],"equipment_map_id_pk='".$equipment_map_id_pk."' and mpr_defination_id_fk='".$mpr_def_id."'")->execute();*/
					$output = array('success'=>'1');
					$session->set('bprEqpSuccess', "Equipment approved by operator successfully.");
					
					$viewUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$bpr_id."&tab=equipments";
					
					ActivityLog::logUserActivity($loggedPersonID,Yii::$app->params['audit_log_screen_name']['BPR_Equipments'],Yii::$app->params['audit_log_action']['APPROVED'],'Operator signature done for equipment',$viewUrl);
					$output = array('success'=>'1');
				//}
			}
			else
			{
				$output = array('success'=>'0');
			}
			echo json_encode($output);
		}
		else
		{
			return $this->renderPartial('_operator_signature', [
           		'model' => $model,
				'bpr_id' => $bpr_id,
				'equipment_map_id_pk' => $equipment_map_id_pk,
				'mpr_def_id' => $mpr_def_id,
				'signType' => $signType,
        	]);
		}	
	}
    /**
     * Displays a single Bprrecords model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$tab='')
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$model = $this->findModel($id);

		if(!CommonFunctions::lockThisRecord($id,$model,'bpr_batch_processing_records','bpr_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		}
		
		switch($tab){
			case 'coverpage':
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Cover_Page'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed cover page of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'billofmaterials':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed bill of materials of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'formulation':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Formulation'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed formulation of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'equipments':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed equipments of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'manufacturingInst':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed manufacturing instructions of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'bprApprovals':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed approvals of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			default:
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Cover_Page'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed cover page of BPR ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;	
		}
		
		
		$searchModel = new BillofmaterialSearch();
		$searchModel->mpr_defination_id_fk = $model->mpr_definition_id_fk;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$formulasearchModel = new FormulationSearch();
		$formulasearchModel->mpr_defination_id_fk = $model->mpr_definition_id_fk;
        $formuladataProvider = $formulasearchModel->search(Yii::$app->request->queryParams);
		
		$eqpsearchModel = new EquipmentmapSearch();
		$eqpsearchModel->mpr_defination_id_fk = $model->mpr_definition_id_fk;
        $eqpdataProvider = $eqpsearchModel->search(Yii::$app->request->queryParams);
		
		$minstsearchModel = new MinstructionsSearch();
		$minstsearchModel->mpr_defination_id_fk = $model->mpr_definition_id_fk;
        $minstdataProvider = $minstsearchModel->search(Yii::$app->request->queryParams);
		
		$params = [':mpr_defination_id_fk' => $model->mpr_definition_id_fk, ':isDeleted' => '0'];
		$mInstructionSteps = Yii::$app->db->createCommand('SELECT mi_id_pk, mpr_defination_id_fk, mi_step, mi_action, unit_id_fk, mi_range, target, perfomer, verifier, document_id_fk, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_manufacturing_instruction WHERE mpr_defination_id_fk=:mpr_defination_id_fk and isDeleted=:isDeleted ORDER BY mi_id_pk ASC', $params)->queryAll();
		
		$bapprsearchModel = new BprapprovalsSearch();
		$bapprsearchModel->bpr_id_fk = $model->bpr_id_pk;
        $bapprdataProvider = $bapprsearchModel->search(Yii::$app->request->queryParams);
		
		return $this->render('view', [
            'model' => $model,
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'activetab' => $tab,
			'eqpsearchModel' => $eqpsearchModel,
			'eqpdataProvider' => $eqpdataProvider,
			'minstsearchModel' => $minstsearchModel,
			'minstdataProvider' => $minstdataProvider,
			'mInstructionSteps' => $mInstructionSteps,
			'bapprsearchModel' => $bapprsearchModel,
			'bapprdataProvider' => $bapprdataProvider,
			'formulasearchModel' => $formulasearchModel,
			'formuladataProvider' => $formuladataProvider,
        ]);
    }

    /**
     * Creates a new Bprrecords model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$model = new Bprrecords();
		$request = Yii::$app->request;
		$bpr_id_pk = intval($request->post('bpr_id_pk',0));
		$mpr_def_id_fk = intval($request->post('mpr_def_id_fk',0));
		$product_code = trim($request->post('product_code',''));
		$mpr_version_ro = trim($request->post('mpr_version_ro',''));
		$lot_hash = trim($request->post('lot_hash',''));
		
        if (isset($_POST) && strlen($product_code)>0) {
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_batch_processing_records WRITE,activity_log WRITE")->query();
			
			$fieldArray = array(
					'mpr_definition_id_fk' => $mpr_def_id_fk,
                    'product_code' => trim($product_code),
                    'mpr_version' => trim($mpr_version_ro),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_BPR_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			//if($is_BPR_exists=="No"){
				$command = Yii::$app->db->createCommand()
					->insert('bpr_batch_processing_records', [
					'mpr_definition_id_fk' => $mpr_def_id_fk,
					'product_code' => trim($product_code),
					'mpr_version' => $mpr_version_ro,
					'lot_hash' => $lot_hash,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,			
				])->execute();
				$bprid = Yii::$app->db->getLastInsertID();
				if($bprid)
				{
					$last = 1000 + $bprid;
					$batchNumber = date('Y').'-'.$bprid.'-'.$last;
					$command = Yii::$app->db->createCommand()
						->update('bpr_batch_processing_records', [
						'batch'=> $batchNumber,	
					],"bpr_id_pk='".$bprid."'")->execute();
				}
				
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$bprid;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['ADD'],'Added BPR with batch:"'.$batchNumber.'"',$addUrl);
			//}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
        } else {
		
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add BPR screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Bprrecords model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
        $model = $this->findModel($id);	
		$request = Yii::$app->request;
		$bpr_id_pk = intval($request->post('bpr_id_pk',0));
		$mpr_def_id_fk = intval($request->post('mpr_def_id_fk',0));
		$product_code = trim($request->post('product_code',''));
		$mpr_version = trim($request->post('mpr_version',''));
			
        if (isset($_POST) && strlen($product_code)>0) {
            $command = Yii::$app->db->createCommand()
				->update('bpr_batch_processing_records', [
				'mpr_definition_id_fk' => $mpr_version,
				'product_code' => trim($product_code),
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],'bpr_id_pk="'.$id.'"')->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated BPR batch:"'.$model->batch.'"',$addUrl);
			return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of BPR Batch:"'.$model->batch.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Bprrecords model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
		$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$id;
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR'],Yii::$app->params['audit_log_action']['DELETE'],'Deleted BPR record of id:"'.$id.'"',$delUrl);
		
        $model = $this->findModel($id);
		 
		$command = Yii::$app->db->createCommand()
						->update('bpr_batch_processing_records', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1'], 'bpr_id_pk='.$id)
						->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bprrecords model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bprrecords the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bprrecords::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
