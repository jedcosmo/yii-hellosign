<?php

namespace backend\modules\mprapprovals\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\mprapprovals\models\Mprapprovals;
use backend\modules\mprapprovals\models\MprapprovalsSearch;
use backend\modules\mprapprovals\models\MprapprovalsSearchDel;
use backend\modules\mprdefinition\models\Mprdefination;
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
class MprapprovalsController extends Controller
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
								   return CommonFunctions::isAccessible('RM_MPR');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Mprapprovals models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of MPR Approvals',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new MprapprovalsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionDeletedlist($mprid)
	{	
	   $mprDef = Mprdefination::findOne($mprid);
		   
	   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
	   }
	   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of MPR Approvals',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new MprapprovalsSearchDel();
		$searchModelDel->mpr_defination_id_fk = $mprid;
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}

	public function actionMprapprovalunique()
	{
		
		$request = Yii::$app->request;
		$action = $request->post('action');
		
		switch($action){
		
		case 'Approver':
				$edit_id = intval($request->post('edit_id',0));
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
				$approval_person_id_fk = intval($request->post('approval_person_id_fk',0));
				$approval_job_function = trim($request->post('approval_job_function',''));
				$condition = '';
				if($approval_person_id_fk > 0){
					$count = 0;
					
					if($edit_id > 0){
						$count = Mprapprovals::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'approval_person_id_fk' => $approval_person_id_fk,
										'approval_job_function' => $approval_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("mpr_approval_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Mprapprovals::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
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
				$edit_id = intval($_REQUEST['edit_id']);
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
				$verifier_person_id_fk = intval($request->post('verifier_person_id_fk',0));
				$verifier_job_function = trim($request->post('verifier_job_function',''));
				$condition = '';
				if(strlen($verifier_person_id_fk) > 0){
					$count = 0;
					
					if($edit_id > 0){
						$count = Mprapprovals::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'verifier_person_id_fk' => $verifier_person_id_fk,
										'verifier_job_function' => $verifier_job_function,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("mpr_approval_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Mprapprovals::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
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
    /**
     * Displays a single Mprapprovals model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$mprid='')
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
	   $mprDef = Mprdefination::findOne($mprid);
	   
	   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of MPR Approvals record of id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
	
	public function actionChecklogincredentials()
	{
		$loggedPersonID = getCommonFunctions::getPersonFromUsername($_REQUEST['username'], $_REQUEST['password']);
		$modelAppr = $this->findModel($_REQUEST['mpr_appr_id']);
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

	public function actionApporveperformer($mpr_appr_id,$masterMPR='')
	{
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($mpr_appr_id);
		$eqpExpiredflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($masterMPR);
		$prdDeletedflag = checkCommonFunctions::checkIfProductDeletedofMPR($masterMPR);
		$BOMFlag = checkCommonFunctions::checkIsBOMSAddedToMPR($masterMPR);
		$bomsRejectedFlag = 'No';
		if($BOMFlag=='Yes')
		{
			$bomsRejectedFlag = checkCommonFunctions::checkIfBOMSApprovedOfMPR($masterMPR);
		}
		$EqpsFlag = checkCommonFunctions::checkIsEqpsAddedToMPR($masterMPR);
		$MiStepsFlag = checkCommonFunctions::checkIsStepsAddedToMPR($masterMPR);
		
		if(isset($_POST) && isset($_POST['username']) && $_POST['username']!='')
		{
			$loggedPersonID = getCommonFunctions::getPersonFromUsername($_POST['username'], $_POST['password']);
			if ($loggedPersonID == $modelAppr->approval_person_id_fk && Yii::$app->user->id==$loggedPersonID && $eqpExpiredflag!="Yes" && $prdDeletedflag!="Yes" && $BOMFlag=="Yes" && $EqpsFlag=="Yes" && $MiStepsFlag=="Yes" && $bomsRejectedFlag!="Yes") 
			{
				$session = Yii::$app->session;
				$session->remove('errorLogin');	
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'approval_status' => 'Approved',
					'approval_datetime' => date("Y-m-d H:i:s"),
				],'mpr_approval_id_pk="'.$mpr_appr_id.'" and mpr_defination_id_fk="'.$masterMPR.'"')->execute();
				$session->set('successLogin', 'MPR approved successfully');
				ActivityLog::logUserActivity($modelAppr->approval_person_id_fk,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['APPROVED'],'Approved MPR Definition record of id:"'.$masterMPR.'"',$_SERVER['HTTP_REFERER']);
				
				$outputArr = ['success'=>'Yes', 'id'=>$masterMPR, 'tab'=>'mprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			}
			else
			{
				$errorTextMessage = '';
				if($eqpExpiredflag=="Yes") 
					$errorTextMessage .= "<br/>Equipment related to this MPR is either expired or deleted.";
					
				if($prdDeletedflag=="Yes") 
					$errorTextMessage .= "<br/>Product related to this MPR is deleted.";
					
				if($BOMFlag!="Yes")
					$errorTextMessage .= "<br/>Bill of Materials are not yet added for this MPR.";
					
				if($EqpsFlag!="Yes")
					$errorTextMessage .= "<br/>Equipments are not yet added for this MPR.";
					
				if($MiStepsFlag!="Yes")
					$errorTextMessage .= "<br/>Manufacturing Steps are not yet added for this MPR.";
					
				if($bomsRejectedFlag=="Yes")
					$errorTextMessage .= "<br/>Bill of Materials are pending for status Approval.";
					
				$errorTextMessage = trim($errorTextMessage,"<br/>");
				$session = Yii::$app->session;
				$session->set('errorLogin', $errorTextMessage);
				
				$outputArr = ['success'=>'Yes', 'id'=>$masterMPR, 'tab'=>'mprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			} 
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$masterMPR."&tab=mprApprovals");
			exit;
		}
		else
		{
			return $this->renderPartial('approve_performer', [
           		'model' => $model,
				'eqpExpiredflag' => $eqpExpiredflag,
				'BOMFlag' => $BOMFlag,
				'bomsRejectedFlag' => $bomsRejectedFlag,
				'EqpsFlag' => $EqpsFlag,
				'MiStepsFlag' => $MiStepsFlag,
				'masterMPR' => $masterMPR,
				'prdDeletedflag' => $prdDeletedflag,
				'signType' => 'Approver',
        	]);
		}
		
	}
	
	public function actionVerifyperformer($mpr_appr_id,$masterMPR='')
	{
		$model = new AdminLoginForm();
		$modelAppr = $this->findModel($mpr_appr_id);
		$eqpExpiredflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($masterMPR);
		$prdDeletedflag = checkCommonFunctions::checkIfProductDeletedofMPR($masterMPR);
		$BOMFlag = checkCommonFunctions::checkIsBOMSAddedToMPR($masterMPR);
		$bomsRejectedFlag = 'No';
		if($BOMFlag=='Yes')
		{
			$bomsRejectedFlag = checkCommonFunctions::checkIfBOMSApprovedOfMPR($masterMPR);
		}
		$EqpsFlag = checkCommonFunctions::checkIsEqpsAddedToMPR($masterMPR);
		$MiStepsFlag = checkCommonFunctions::checkIsStepsAddedToMPR($masterMPR);
		
		if(isset($_POST) && isset($_POST['username']) && $_POST['username']!='')
		{
			$loggedPersonID = getCommonFunctions::getPersonFromUsername($_POST['username'], $_POST['password']);
			if ($loggedPersonID == $modelAppr->verifier_person_id_fk && Yii::$app->user->id==$loggedPersonID && $eqpExpiredflag!="Yes" && $prdDeletedflag!="Yes" && $BOMFlag=="Yes" && $EqpsFlag=="Yes" && $MiStepsFlag=="Yes" && $bomsRejectedFlag!="Yes") 
			{
				$session = Yii::$app->session;
				$session->remove('errorLogin');	
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'verified_status' => 'Verified',
					'verified_datetime' => date("Y-m-d H:i:s"),
				],'mpr_approval_id_pk="'.$mpr_appr_id.'" and mpr_defination_id_fk="'.$masterMPR.'"')->execute();
				$session->set('successLogin', 'MPR verified successfully');
				ActivityLog::logUserActivity($modelAppr->verifier_person_id_fk,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['VERIFIED'],'Verified MPR Definition record of id:"'.$masterMPR.'"',$_SERVER['HTTP_REFERER']);
				
				$outputArr = ['success'=>'Yes', 'id'=>$masterMPR, 'tab'=>'mprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			}
			else
			{			
				$errorTextMessage = '';
				if($eqpExpiredflag=="Yes") 
					$errorTextMessage .= "<br/>Equipment related to this MPR is either expired or deleted.";
					
				if($prdDeletedflag=="Yes") 
					$errorTextMessage .= "<br/>Product related to this MPR is deleted.";
					
				if($BOMFlag!="Yes")
					$errorTextMessage .= "<br/>Bill of Materials are not yet added for this MPR.";
					
				if($EqpsFlag!="Yes")
					$errorTextMessage .= "<br/>Equipments are not yet added for this MPR.";
					
				if($MiStepsFlag!="Yes")
					$errorTextMessage .= "<br/>Manufacturing Steps are not yet added for this MPR.";
					
				if($bomsRejectedFlag=="Yes")
					$errorTextMessage .= "<br/>Bill of Materials are pending for status Approval.";
					
				$errorTextMessage = trim($errorTextMessage,"<br/>");
				$session = Yii::$app->session;
				$session->set('errorLogin', $errorTextMessage);
				$outputArr = ['success'=>'Yes', 'id'=>$masterMPR, 'tab'=>'mprApprovals'];
				echo $outputArr = json_encode($outputArr);
				exit;
			} 
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$masterMPR."&tab=mprApprovals");
			exit;
		}
		else
		{
			return $this->renderPartial('approve_performer', [
           		'model' => $model,
				'eqpExpiredflag' => $eqpExpiredflag,
				'BOMFlag' => $BOMFlag,
				'bomsRejectedFlag' => $bomsRejectedFlag,
				'EqpsFlag' => $EqpsFlag,
				'MiStepsFlag' => $MiStepsFlag,
				'masterMPR' => $masterMPR,
				'prdDeletedflag' => $prdDeletedflag,
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
	   $mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
	   $mprDef = Mprdefination::findOne($mprid);
	   
	   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
	   }	
		
        $model = new Mprapprovals();
		
		$request = Yii::$app->request;
		$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
		$mpr_approval_id_pk = intval($request->post('mpr_approval_id_pk',0));
		
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_fk);
		
		if(isset($_POST) && $mpr_defination_id_fk>0 && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_mpr_approval WRITE,bpr_person WRITE, activity_log WRITE")->query();
			
			$approval_person = intval($request->post('approval_person_id_fk',0));			
			$approval_job = trim($request->post('approval_job_function',''));
			$verifier_person = intval($request->post('verifier_person_id_fk',0));
			$verifier_job = trim($request->post('verifier_job_function',''));
			
		//$is_approver_exists = checkCommonFunctions::check_if_Approvers_exists_in_MPR($model,$mpr_approval_id_pk,$mpr_defination_id_fk,$approval_person,$approval_job);
		
		$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
                    'approval_person_id_fk' => intval($approval_person),
                    'approval_job_function' => trim($approval_job),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
		$is_approver_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
		
		//$is_verifier_exists = checkCommonFunctions::check_if_Verifiers_exists_in_MPR($model,$mpr_approval_id_pk,$mpr_defination_id_fk,$verifier_person,$verifier_job);
		
		$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
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
					->insert('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
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
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprapprovals/mprapprovals/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ADD'],'Added MPR Approval',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_POST['mpr_defination_id_fk']."&tab=mprApprovals");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add MPR Approval screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				
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
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
        $model = $this->findModel($id);

		$request = Yii::$app->request;
		$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
		$approval_person = intval($request->post('approval_person_id_fk',0));			
		$approval_job = trim($request->post('approval_job_function',''));
		$verifier_person = intval($request->post('verifier_person_id_fk',0));
		$verifier_job = trim($request->post('verifier_job_function',''));
				
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_fk);
		
		$mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
	    $mprDef = Mprdefination::findOne($mprid);
	   
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
        if(isset($_POST) && $mpr_defination_id_fk>0 && $rflag!='Yes')
		{
			if(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'mpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && $_POST['bpr_alreadyVerified']!='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'verifier_person_id_fk' => 0,
					'verifier_job_function' => '',
					'verified_status' => '',
					'verified_datetime' => '',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'mpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes' && $_POST['bpr_alreadyApproved']!='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'approval_person_id_fk' => 0,
					'approval_job_function' => '',
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'approval_status' => '',
					'approval_datetime' => '',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'mpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['performerChk']) && $_POST['performerChk']=='Yes' && $_POST['bpr_alreadyVerified']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'approval_person_id_fk' => $approval_person,
					'approval_job_function' => trim($approval_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'mpr_approval_id_pk="'.$id.'"')->execute();
			}
			elseif(isset($_POST['verifierChk']) && $_POST['verifierChk']=='Yes' && $_POST['bpr_alreadyApproved']=='Yes')
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_approval', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'verifier_person_id_fk' => $verifier_person,
					'verifier_job_function' => trim($verifier_job),
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],'mpr_approval_id_pk="'.$id.'"')->execute();
			}
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprapprovals/mprapprovals/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated MPR Approvals record of id:"'.$id.'"',$addUrl);
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_POST['mpr_defination_id_fk']."&tab=mprApprovals");
			exit;
		}else{ 
		
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of MPR Approvals record of id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
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
    public function actionDelete($id,$mprid,$delreason)
    {
	    $mprDef = Mprdefination::findOne($mprid);
	   
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprapprovals/mprapprovals/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_mpr_approval', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'mpr_approval_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This MPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=mprApprovals");
		exit;
    }
	
	public function actionRestore($id,$mprid,$delreason)
    {
		$mprDef = Mprdefination::findOne($mprid);
	   
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprapprovals/mprapprovals/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_mpr_approval', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'mpr_approval_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This MPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=mprApprovals");
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
        if (($model = Mprapprovals::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
