<?php

namespace backend\modules\minstructions\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\minstructions\models\Minstructions;
use backend\modules\minstructions\models\MinstructionsSearch;
use backend\modules\minstructions\models\MinstructionsSearchDel;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\bprrecords\models\Bprrecords;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * MinstructionsController implements the CRUD actions for Minstructions model.
 */
class MinstructionsController extends Controller
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
								    return CommonFunctions::isAccessibleMPRorBPR('RM_MPR', 'RM_BPR');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Minstructions models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of manufacturing instructions',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new MinstructionsSearch();
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
	   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of manufacturing instructions',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new MinstructionsSearchDel();
		$searchModelDel->mpr_defination_id_fk = $mprid;
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
	
	public function actionStepunique()
	{
		$request = Yii::$app->request;
		
		try{
				$mi_step = trim($request->post('mi_step',''));
				$edit_id = intval($request->post('edit_id',0));
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
				$condition = '';
				if(strlen($mi_step) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Minstructions::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'mi_step' => trim($mi_step),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("mi_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Minstructions::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'mi_step' => trim($mi_step),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					
					return ($count > 0?"false":"true");
				}
			}catch(Exception $e){ return false; }
	}
    /**
     * Displays a single Minstructions model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$mprid='')
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
		
		if($mode=='bpr')
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
		   
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of BPR Manufacturing Instruction',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
		else
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
		   
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of MPR Manufacturing Instruction',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
			
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Minstructions model.
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
	
       $model = new Minstructions();
	   $request = Yii::$app->request;
	   $mi_id_pk = intval($request->post('mi_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   $mi_step = trim($request->post('mi_step',''));
	   $unit_id_fk = intval($request->post('unit_id_fk',0));
	   $mi_action = trim($request->post('mi_action',''));
	   $mi_range = trim($request->post('mi_range',''));
	   $target = trim($request->post('target',''));

	   $rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_fk);
	   
        if(isset($_POST) && strlen($mi_step)>0 && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_manufacturing_instruction WRITE,activity_log WRITE")->query();
			
			//$is_mistep_exists = checkCommonFunctions::check_if_mi_step_exists_in_MPR($model,$mi_step,$mi_id_pk,$mpr_defination_id_fk);
			
			$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
                    'mi_step' => trim($mi_step),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_mistep_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_mistep_exists == "No"){
				$performer = 'No'; $verifier = 'No';
				if(isset($_POST['perfomer']) && $_POST['perfomer']=='Yes')
					$performer = 'Yes';
					
				if(isset($_POST['verifier']) && $_POST['verifier']=='Yes')
					$verifier = 'Yes';
					
				
				$command = Yii::$app->db->createCommand()
					->insert('bpr_manufacturing_instruction', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'mi_step' => trim($mi_step),
					'mi_action' => trim($mi_action),
					'unit_id_fk' => $unit_id_fk,
					'mi_range' => trim($mi_range),
					'target' => trim($target),
					'perfomer' => $performer,
					'verifier' => $verifier,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."minstructions/minstructions/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ADD'],'Added MPR Manufacturing Instruction',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_POST['mpr_defination_id_fk']."&tab=manufacturingInst");
			exit;
		}else{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add MPR manufacturing instruction screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			 
			 return $this->render('create', [
				'model' => $model,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Updates an existing Minstructions model.
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
	   $mi_id_pk = intval($request->post('mi_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   $mi_step = trim($request->post('mi_step',''));
	   $unit_id_fk = intval($request->post('unit_id_fk',0));
	   $mi_action = trim($request->post('mi_action',''));
	   $mi_range = trim($request->post('mi_range',''));
	   $target = trim($request->post('target',''));

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
	   
		 
        if(isset($_POST) && strlen($mi_step)>0 && $rflag!='Yes')
		{
			$performer = 'No'; $verifier = 'No';
			if(isset($_POST['perfomer']) && $_POST['perfomer']=='Yes')
				$performer = 'Yes';
				
			if(isset($_POST['verifier']) && $_POST['verifier']=='Yes')
				$verifier = 'Yes';

			$command = Yii::$app->db->createCommand()
				->update('bpr_manufacturing_instruction', [
				'mpr_defination_id_fk' => $mpr_defination_id_fk,
				'mi_step' => trim($mi_step),
				'mi_action' => trim($mi_action),
				'unit_id_fk' => $unit_id_fk,
				'mi_range' => trim($mi_range),
				'target' => trim($target),
				'perfomer' => $performer,
				'verifier' => $verifier,
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],'mi_id_pk="'.$id.'"')->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."minstructions/minstructions/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated MPR Manufacturing Instruction',$addUrl);
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_POST['mpr_defination_id_fk']."&tab=manufacturingInst");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of MPR Manufacturing Instruction',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			 return $this->render('update', [
				'model' => $model,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Deletes an existing Minstructions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."minstructions/minstructions/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_manufacturing_instruction', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'mi_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=manufacturingInst");
		exit;
    }

	public function actionRestore($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."minstructions/minstructions/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_manufacturing_instruction', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'mi_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=manufacturingInst");
		exit;
    }
    /**
     * Finds the Minstructions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Minstructions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Minstructions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
