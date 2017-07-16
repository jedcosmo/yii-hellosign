<?php

namespace backend\modules\bprapprovals\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\bprapprovals\models\Bprapprovals;
use backend\modules\bprapprovals\models\BprapprovalsSearch;
use backend\modules\bprapprovals\models\BprapprovalsSearchDel;
use backend\modules\bprrecords\models\Bprrecords;
use backend\modules\adminUser\models\Admin;
use common\models\AdminLoginForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;
 
/**
 * MprapprovalsController implements the CRUD actions for Mprapprovals model.
 */
class BprapprovalsController extends Controller
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
     * Lists all Bprapprovals models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of Executed BPR Approvals',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new BprapprovalsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


	public function actionDeletedlist($bprid)
	{ 
	   $bprDet = Bprrecords::findOne($bprid);
	   if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
	   }
		   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of Executed BPR Approvals',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new BprapprovalsSearchDel();
		$searchModelDel->bpr_id_fk = $bprid;
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Bprapprovals model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$mprid='')
    {
		 $bprid = intval(isset($_REQUEST['bprid'])?$_REQUEST['bprid']:0);
		 $bprDet = Bprrecords::findOne($bprid);
		 if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		 {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		 }
		   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of Executed BPR Approval id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionBprapprovalunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		
		switch($action){
		
		case 'Approver':
				$edit_id = intval($request->post('edit_id',0));
				$bpr_id_fk = intval($request->post('bpr_id_fk',0));
				$approval_person_id_fk = intval($request->post('approval_person_id_fk',0));
				$approval_job_function = trim($request->post('approval_job_function',''));
				$condition = '';
				if($approval_person_id_fk > 0){
										
					$count = 0;
					if($edit_id > 0){
						$count = Bprapprovals::find()->where([
										'bpr_id_fk' => $bpr_id_fk,
										'approval_person_id_fk' => $approval_person_id_fk,
										'approval_job_function' => $approval_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("bpr_approval_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Bprapprovals::find()->where([
										'bpr_id_fk' => $bpr_id_fk,
										'approval_person_id_fk' => $approval_person_id_fk,
										'approval_job_function' => $approval_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}else{
					return "true";
				}
			break;
		case 'Verifier':
				$edit_id = intval($request->post('edit_id',0));
				$bpr_id_fk = intval($request->post('bpr_id_fk',0));
				$verifier_person_id_fk = intval($request->post('verifier_person_id_fk',0));
				$verifier_job_function = trim($request->post('verifier_job_function',''));
				$condition = '';
				if(strlen($verifier_person_id_fk) > 0){
										
					$count = 0;
					if($edit_id > 0){
						$count = Bprapprovals::find()->where([
										'bpr_id_fk' => $bpr_id_fk,
										'verifier_person_id_fk' => $verifier_person_id_fk,
										'verifier_job_function' => $verifier_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("bpr_approval_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Bprapprovals::find()->where([
										'bpr_id_fk' => $bpr_id_fk,
										'verifier_person_id_fk' => $verifier_person_id_fk,
										'verifier_job_function' => $verifier_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
					
				}else{
					return "true";
				}
			break;
		}
	}

	public function actionChecklogincredentials()
	{
		$loggedPersonID = getCommonFunctions::getPersonFromUsername($_REQUEST['username'], $_REQUEST['password']);
		$modelAppr = $this->findModel($_REQUEST['bpr_appr_id']);
		if($loggedPersonID>0)
		{
			if(isset($_REQUEST['signType']) && $_REQUEST['signType']!='')
			{
				if ($_REQUEST['signType']=='Approver' && $loggedPersonID == $modelAppr->approval_person_id_fk && Yii::$app->user->id==$loggedPersonID) 
					echo "success";
				elseif ($_REQUEST['signType']=='Verifier' && $loggedPersonID == $modelAppr->verifier_person_id_fk && Yii::$app->user->id==$loggedPersonID) 
					echo "success";
				else
					echo "You are not authorized person to perform this action.";
			}
			else
				echo "You are not authorized person to perform this action.";
		}
		else
			echo "Incorrect username or password";
	}

	public function actionApporveperformer($bpr_appr_id,$masterBPR='')
	{
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($bpr_appr_id);
		
		$stopApproval = "No";
		$outstandingSteps = array();
		$outstandingEqps = array();
		$eqpExpiredflag = '';
		if($masterBPR)
		{
			$BPRDetails = getCommonFunctions::get_BPR_Record_Details($masterBPR);
			$eqpExpiredflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($BPRDetails['mpr_definition_id_fk']);
			$prdDeletedflag = checkCommonFunctions::checkIfProductDeletedofMPR($BPRDetails['mpr_definition_id_fk']);
			if($BPRDetails['mpr_definition_id_fk'])
			{
				$outstandingSteps = getCommonFunctions::getOutstandingBPRInstructions($masterBPR,$BPRDetails['mpr_definition_id_fk']);
				$outstandingEqps = getCommonFunctions::getOutstandingBPREquipments($masterBPR,$BPRDetails['mpr_definition_id_fk']);
			}
		}
		
		if(isset($outstandingSteps) && is_array($outstandingSteps) && ($outstandingSteps['performer']!='' || $outstandingSteps['verifier']!='' || $outstandingSteps['QAOutstanding']!='' || $eqpExpiredflag=="Yes" || $prdDeletedflag=="Yes" || count($outstandingEqps)>0)) 
		{
			$stopApproval = "Yes";
		}
	
		if(isset($_POST) && isset($_POST['username']) && $_POST['username']!='')
		{
			if($stopApproval!="Yes")
			{
				$loggedPersonID = getCommonFunctions::getPersonFromUsername($_POST['username'], $_POST['password']);
				if ($loggedPersonID == $modelAppr->approval_person_id_fk && Yii::$app->user->id==$loggedPersonID && $eqpExpiredflag!="Yes" && $prdDeletedflag!="Yes") 
				{
					$session = Yii::$app->session;
					$session->remove('errorLogin');	
					$command = Yii::$app->db->createCommand()
						->update('bpr_bpr_approval', [
						'approval_status' => 'Approved',
						'approval_datetime' => date("Y-m-d H:i:s"),
					],'bpr_approval_id_pk="'.$bpr_appr_id.'" and bpr_id_fk="'.$masterBPR.'"')->execute();
					
					$quarantineBPR = CommonFunctions::quarantine_BPR_status($modelAppr,$masterBPR,$modelAppr->approval_person_id_fk,$bpr_appr_id,'HS_approver_signature_id');
					$session->set('successLogin', 'BPR approved successfully');
					ActivityLog::logUserActivity($modelAppr->approval_person_id_fk,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['APPROVED'],'Approved BPR of id:"'.$masterBPR.'"',$_SERVER['HTTP_REFERER']);
					
					$outputArr = ['success'=>'Yes', 'id'=>$masterBPR, 'tab'=>'bprApprovals'];
					echo $outputArr = json_encode($outputArr);
					exit;
				}
				else
				{
					$session = Yii::$app->session;
					$session->set('errorLogin', 'You are not authorized to perform this action.');
				} 
			}
			else
			{
				$errorTextMessage = '';
				
				if($prdDeletedflag=="Yes") 
            		$errorTextMessage .= "<br/> Product related to this BPR is deleted.";
          
            	if($eqpExpiredflag=="Yes")
            		$errorTextMessage .= "<br/> Equipment related to this BPR is either expired or deleted.";
           
             	if($outstandingSteps['performer']!='')
            		$errorTextMessage .= "<br/> Performer outstanding Step(s) # : ".wordwrap($outstandingSteps['performer'],30,"<br/>",1);
           
            	if($outstandingSteps['verifier']!='')
            		$errorTextMessage .= "<br/> Verifier outstanding Step(s) # : ".wordwrap($outstandingSteps['verifier'],30,"<br/>",1);
            
             	if($outstandingSteps['QAOutstanding']!='') 
            		$errorTextMessage .= "<br/> QA Review outstanding Step(s) # : ".wordwrap($outstandingSteps['QAOutstanding'],30,"<br/>",1);
            
             	if(isset($outstandingEqps['equipment_ids']) && $outstandingEqps['equipment_ids']!='')
            		$errorTextMessage .= "<br/> Operator signature pending for Equipment# : ".wordwrap($outstandingEqps['equipment_ids'],30,"<br/>",1);
          
		  		$errorTextMessage = trim($errorTextMessage,"<br/>");
				$session = Yii::$app->session;
				$session->set('errorLogin', $errorTextMessage);
				$outputArr = ['success'=>'Yes', 'id'=>$masterBPR, 'tab'=>'bprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			}
			header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$masterBPR."&tab=bprApprovals");
			exit;
		}
		else
		{
			return $this->renderPartial('approve_performer', [
				'model' => $model,
				'outstandingSteps' => $outstandingSteps,
				'outstandingEqps' => $outstandingEqps,
				'eqpExpiredflag' => $eqpExpiredflag,
				'prdDeletedflag' => $prdDeletedflag,
				'masterBPR' => $masterBPR,
				'signType' => 'Approver',
			]);
		}
	}
	
	public function actionVerifyperformer($bpr_appr_id,$masterBPR='')
	{
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($bpr_appr_id);
		$outstandingSteps = array();
		$outstandingEqps = array();
		$eqpExpiredflag = '';
		$stopApproval = "No";
		
		if($masterBPR)
		{
			$BPRDetails = getCommonFunctions::get_BPR_Record_Details($masterBPR);
			$eqpExpiredflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($BPRDetails['mpr_definition_id_fk']);
			$prdDeletedflag = checkCommonFunctions::checkIfProductDeletedofMPR($BPRDetails['mpr_definition_id_fk']);
			if($BPRDetails['mpr_definition_id_fk'])
			{
				$outstandingSteps = getCommonFunctions::getOutstandingBPRInstructions($masterBPR,$BPRDetails['mpr_definition_id_fk']);
				$outstandingEqps = getCommonFunctions::getOutstandingBPREquipments($masterBPR,$BPRDetails['mpr_definition_id_fk']);
			}
		}
		
		if(isset($outstandingSteps) && is_array($outstandingSteps) && ($outstandingSteps['performer']!='' || $outstandingSteps['verifier']!='' || $outstandingSteps['QAOutstanding']!='' || $eqpExpiredflag=="Yes" || $prdDeletedflag=="Yes" || count($outstandingEqps)>0)) 
		{
			$stopApproval = "Yes";
		}

		
		if(isset($_POST) && isset($_POST['username']) && $_POST['username']!='')
		{
			if($stopApproval!="Yes")
			{
				$loggedPersonID = getCommonFunctions::getPersonFromUsername($_POST['username'], $_POST['password']);
				if ($loggedPersonID == $modelAppr->verifier_person_id_fk && Yii::$app->user->id==$loggedPersonID && $eqpExpiredflag!="Yes" && $prdDeletedflag!="Yes") 
				{
					$session = Yii::$app->session;
					$session->remove('errorLogin');	
					$command = Yii::$app->db->createCommand()
						->update('bpr_bpr_approval', [
						'verified_status' => 'Verified',
						'verified_datetime' => date("Y-m-d H:i:s"),
					],'bpr_approval_id_pk="'.$bpr_appr_id.'" and bpr_id_fk="'.$masterBPR.'"')->execute();
					$quarantineBPR = CommonFunctions::quarantine_BPR_status($modelAppr,$masterBPR,$modelAppr->verifier_person_id_fk,$bpr_appr_id,'HS_verifier_signature_id');
					$session->set('successLogin', 'BPR verified successfully');
					ActivityLog::logUserActivity($modelAppr->verifier_person_id_fk,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['VERIFIED'],'Verified BPR of id:"'.$masterBPR.'"',$_SERVER['HTTP_REFERER']);
					
					$outputArr = ['success'=>'Yes', 'id'=>$masterBPR, 'tab'=>'bprApprovals'];
					echo $outputArr = json_encode($outputArr);
					exit;
					
				}
				else
				{
					$session = Yii::$app->session;
					$session->set('errorLogin', 'You are not authorized to perform this action.');
				}
			}
			else
			{
				$errorTextMessage = '';
				
				if($prdDeletedflag=="Yes") 
            		$errorTextMessage .= "<br/> Product related to this BPR is deleted.";
          
            	if($eqpExpiredflag=="Yes")
            		$errorTextMessage .= "<br/> Equipment related to this BPR is either expired or deleted.";
           
             	if($outstandingSteps['performer']!='')
            		$errorTextMessage .= "<br/> Performer outstanding Step(s) # : ".wordwrap($outstandingSteps['performer'],30,"<br/>",1);
           
            	if($outstandingSteps['verifier']!='')
            		$errorTextMessage .= "<br/> Verifier outstanding Step(s) # : ".wordwrap($outstandingSteps['verifier'],30,"<br/>",1);
            
             	if($outstandingSteps['QAOutstanding']!='') 
            		$errorTextMessage .= "<br/> QA Review outstanding Step(s) # : ".wordwrap($outstandingSteps['QAOutstanding'],30,"<br/>",1);
            
             	if(isset($outstandingEqps['equipment_ids']) && $outstandingEqps['equipment_ids']!='')
            		$errorTextMessage .= "<br/> Operator signature pending for Equipment# : ".wordwrap($outstandingEqps['equipment_ids'],30,"<br/>",1);
          
		  		$errorTextMessage = trim($errorTextMessage,"<br/>");
				$session = Yii::$app->session;
				$session->set('errorLogin', $errorTextMessage);
				$outputArr = ['success'=>'Yes', 'id'=>$masterBPR, 'tab'=>'bprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			}
			header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$masterBPR."&tab=bprApprovals");
			exit;
		}
		else
		{
			return $this->renderPartial('approve_performer', [
				'model' => $model,
				'outstandingSteps' => $outstandingSteps,
				'outstandingEqps' => $outstandingEqps,
				'eqpExpiredflag' => $eqpExpiredflag,
				'prdDeletedflag' => $prdDeletedflag,
				'masterBPR' => $masterBPR,
				'signType' => 'Verifier',
			]);
		}
	}
    /**
     * Creates a new Mprapprovals model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
		$bprid = intval(isset($_REQUEST['bprid'])?$_REQUEST['bprid']:0);
		 $bprDet = Bprrecords::findOne($bprid);
		 if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		 {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		 }
		 
        $model = new Bprapprovals();
		$request = Yii::$app->request;
		$bpr_id_fk = intval($request->post('bpr_id_fk',0));
		$bpr_approval_id_pk = intval($request->post('bpr_approval_id_pk',0));
		
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bpr_id_fk);
		
		if(isset($_POST) && $bpr_id_fk>0 && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_bpr_approval WRITE,bpr_person WRITE,activity_log WRITE")->query();
			
			$approval_person = intval($request->post('approval_person_id_fk',0));
			$approval_job = trim($request->post('approval_job_function',''));
			$verifier_person = intval($request->post('verifier_person_id_fk',0));
			$verifier_job = trim($request->post('verifier_job_function',''));
			
			//$is_approver_exists = checkCommonFunctions::check_if_Approvers_exists_in_BPR($model,$bpr_approval_id_pk,$bpr_id_fk,$approval_person,$approval_job);
			
			$fieldArray = array(
					'bpr_id_fk' => $bpr_id_fk,
                    'approval_person_id_fk' => intval($approval_person),
                    'approval_job_function' => trim($approval_job),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_approver_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			//$is_verifier_exists = checkCommonFunctions::check_if_Verifiers_exists_in_BPR($model,$bpr_approval_id_pk,$bpr_id_fk,$verifier_person,$verifier_job);
			
			$fieldArray = array(
					'bpr_id_fk' => $bpr_id_fk,
                    'verifier_person_id_fk' => intval($verifier_person),
                    'verifier_job_function' => trim($verifier_job),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_verifier_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			$addFlag = 'Yes';
			if($approval_person>0 && $is_approver_exists=="Yes")
				$addFlag = 'No';
			if($verifier_person>0 && $is_verifier_exists=="Yes")
				$addFlag = 'No';
				
			if($addFlag == 'Yes')
			{
				$command = Yii::$app->db->createCommand()
					->insert('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprapprovals/bprapprovals/view?id=".$id;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ADD'],'Added BPR Approval',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$_POST['bpr_id_fk']."&tab=bprApprovals");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Access add BPR Approval screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			 return $this->render('create', [
				'model' => $model,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Updates an existing Mprapprovals model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$bprid = intval(isset($_REQUEST['bprid'])?$_REQUEST['bprid']:0);
		 $bprDet = Bprrecords::findOne($bprid);
		 if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		 {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		 }
		 
		$model = $this->findModel($id);

		$request = Yii::$app->request;
		$bpr_id_fk = intval($request->post('bpr_id_fk',0));
				
		$approval_person = intval($request->post('approval_person_id_fk',0));
		$approval_job = trim($request->post('approval_job_function',''));
		$verifier_person = intval($request->post('verifier_person_id_fk',0));
		$verifier_job = trim($request->post('verifier_job_function',''));
		
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bpr_id_fk);
		
        if(isset($_POST) && $bpr_id_fk>0 && $rflag!='Yes')
		{
			if(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'bpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && $_POST['bpr_alreadyVerified']!='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'verifier_person_id_fk' => 0,
					'verifier_job_function' => '',
					'verified_status' => '',
					'verified_datetime' => '',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'bpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes' && $_POST['bpr_alreadyApproved']!='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'approval_person_id_fk' => 0,
					'approval_job_function' => '',
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'approval_status' => '',
					'approval_datetime' => '',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'bpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && $_POST['bpr_alreadyVerified']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'bpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes' && $_POST['bpr_alreadyApproved']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_bpr_approval', [
					'bpr_id_fk' => $bpr_id_fk,
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'bpr_approval_id_pk="'.$id.'"')->execute();
			}
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprapprovals/bprapprovals/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated BPR Approvals record of id:"'.$id.'"',$addUrl);
			
			header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$_POST['bpr_id_fk']."&tab=bprApprovals");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of BPR Approvals record of id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			return $this->render('update', [
				'model' => $model,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Deletes an existing Mprapprovals model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$bprid,$delreason)
    {
		 $bprDet = Bprrecords::findOne($bprid);
		 if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		 {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		 }
		 
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bprid);
		
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprapprovals/bprapprovals/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_bpr_approval', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'bpr_approval_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This BPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$bprid."&tab=bprApprovals");
		exit;
    }

	public function actionRestore($id,$bprid,$delreason)
    {
		$bprDet = Bprrecords::findOne($bprid);
		 if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		 {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
			exit;
		 }
		 
		$rflag = checkCommonFunctions::check_If_BPR_Approved($bprid);
		
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."bprapprovals/bprapprovals/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Approvals'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_bpr_approval', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'bpr_approval_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This BPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."bprrecords/bprrecords/view?id=".$bprid."&tab=bprApprovals");
		exit;
    }

    /**
     * Finds the Mprapprovals model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mprapprovals the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bprapprovals::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
