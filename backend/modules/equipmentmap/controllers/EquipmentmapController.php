<?php

namespace backend\modules\equipmentmap\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\equipment\models\Equipment;
use backend\modules\equipmentmap\models\Equipmentmap;
use backend\modules\equipmentmap\models\EquipmentmapSearch;
use backend\modules\equipmentmap\models\EquipmentmapSearchDel;
use backend\modules\bprrecords\models\Bprrecords;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * EquipmentmapController implements the CRUD actions for Equipmentmap model.
 */
class EquipmentmapController extends Controller
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
     * Lists all Equipmentmap models.
     * @return mixed
     */
    public function actionIndex()
    {
	ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of MPR Equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new EquipmentmapSearch();
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
	   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of MPR Equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new EquipmentmapSearchDel();
		$searchModelDel->mpr_defination_id_fk = $mprid;
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
	
	public function actionEquipmentunique()
	{
		$request = Yii::$app->request;
		
		try{
				$equipment_id_fk = intval($request->post('equipment_id_fk',0));
				$edit_id = intval($request->post('edit_id',0));
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
				$product_id_fk = intval($request->post('product_id_fk',0));
				$product_code = trim($request->post('product_code',''));
				$condition = '';
				if(strlen($equipment_id_fk) > 0){				
					$count = 0;
					if($edit_id > 0){
						$count = Equipmentmap::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'product_code' => trim($product_code),
										'equipment_id_fk' => $equipment_id_fk,
										'product_id_fk' => $product_id_fk,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("equipment_map_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Equipmentmap::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'product_code' => trim($product_code),
										'equipment_id_fk' => $equipment_id_fk,
										'product_id_fk' => $product_id_fk,
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					
					return ($count > 0?"false":"true");					
				}
			}catch(Exception $e){ return false; }
	}
	
	public function actionEquipmentdetails($id)
	{
		$eqpDetails = Equipment::find()->where(['equipment_id_pk'=>$id])->asArray()->one();
		$eqpDetails['caliberation_due_date'] = date("m/d/Y", strtotime($eqpDetails['caliberation_due_date']));
		$eqpDetails['preventive_m_due_date'] =  date("m/d/Y", strtotime($eqpDetails['preventive_m_due_date']));
		return json_encode($eqpDetails);
	}
    /**
     * Displays a single Equipmentmap model.
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
		   
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of BPR equipment',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
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
		   
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of MPR equipment',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
		
		$model = $this->findModel($id);

		$mprDef = Mprdefination::findOne($model->mpr_defination_id_fk);
		$eqpDet = Equipment::findOne($model->equipment_id_fk);
		
        return $this->render('view', [
            'model' => $model,
			'mprDef' => $mprDef,
			'eqpDet' => $eqpDet,
        ]);
    }

    /**
     * Creates a new Equipmentmap model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new Equipmentmap();
		$mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
		$mprDef = Mprdefination::findOne($mprid);
	   
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
		$request = Yii::$app->request;
	    $equipment_map_id_pk = intval($request->post('equipment_map_id_pk',0));
	    $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
		$product_id_fk = intval($request->post('product_id_fk',0));
		$product_code = trim($request->post('product_code',''));
		$equipment_id_fk = intval($request->post('equipment_id_fk',0));
		
		$equipment_name = trim($request->post('equipment_name',''));
		$equipment_model = trim($request->post('equipment_model',''));
		$calibration_due_date = trim($request->post('calibration_due_date',''));
		$preventive_m_due_date = trim($request->post('preventive_m_due_date',''));
		$activity = trim($request->post('activity',''));
		
		$start_date_time = trim($request->post('start_date_time',''));
		if($start_date_time!='')
			$start_date_time = date("Y-m-d H:i:s",strtotime($start_date_time));
		else
			$start_date_time = '0000-00-00 00:00:00';
			
		$end_date_time = trim($request->post('end_date_time',''));
		if($end_date_time!='')
			$end_date_time = date("Y-m-d H:i:s",strtotime($end_date_time));
		else
			$end_date_time = '0000-00-00 00:00:00';
			
		$dept_assigned_to = trim($request->post('dept_assigned_to',''));
		$cleaning_agent = trim($request->post('cleaning_agent',''));
		$product_name = trim($request->post('product_name',''));
		$product_part = trim($request->post('product_part',''));
		$comments = trim($request->post('comments',''));
		$operator_signature = intval($request->post('operator_signature',''));
		
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_fk);
		
       if(isset($_POST) && $equipment_id_fk>0 && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_equipment_map WRITE,activity_log WRITE")->query();
			
			//$is_eqpmap_exists = checkCommonFunctions::check_if_equipment_exists_in_MPR($model,$equipment_id_fk, $equipment_map_id_pk,$mpr_defination_id_fk,$product_id_fk,$product_code);
			
			$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
                    'product_code' => trim($product_code),
                    'equipment_id_fk' => $equipment_id_fk,
                    'product_id_fk' => $product_id_fk,
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_eqpmap_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_eqpmap_exists=="No"){
			
				$target_name1 = ''; $uploadedFile1= '';
				if(isset($_FILES['attachment']["name"]) && $_FILES['attachment']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/equipmentmap/';
					
					$attachment = $_FILES['attachment']["name"];
					$ext1 = pathinfo($attachment,PATHINFO_EXTENSION);
					
					$souce_file_name1 = $_FILES['attachment']["tmp_name"];
					$target_name1 = time().".".$ext1;
					$target_file1 = $folderPath . basename($target_name1);
					$uploadSuccess1 = move_uploaded_file($souce_file_name1,$target_file1);
					
					if($uploadSuccess1)
						$uploadedFile1 = $target_name1;
					else 
						$uploadedFile1 = '';
				}
			
			
				$command = Yii::$app->db->createCommand()
					->insert('bpr_equipment_map', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'product_id_fk' => $product_id_fk,
					'product_code' => trim($product_code),
					'equipment_id_fk' => $equipment_id_fk,

					'equipment_name' => $equipment_name,
					'equipment_model' => $equipment_model,
					'calibration_due_date' => date("Y-m-d",strtotime($calibration_due_date)),
					'preventive_m_due_date' => date("Y-m-d",strtotime($preventive_m_due_date)),
					'activity' => $activity,
					'start_date_time' => $start_date_time,
					'end_date_time' => $end_date_time,
					'dept_assigned_to' => $dept_assigned_to,
					'cleaning_agent' => $cleaning_agent,
					'batch' => '',
					'product_name' => $product_name,
					'product_part' => $product_part,
					'attachment' => $uploadedFile1,
					'comments' => $comments,
					'operator_signature' => $operator_signature,
			
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipmentmap/equipmentmap/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ADD'],'Added MPR Equipment',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_POST['mpr_defination_id_fk']."&tab=equipments");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add MPR equipment screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
				'mprDef' => $mprDef,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Updates an existing Equipmentmap model.
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
		$PreviousFile = $model->attachment;
		$mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
		$mprDef = Mprdefination::findOne($mprid);
		
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
		$request = Yii::$app->request;
	    $equipment_map_id_pk = intval($request->post('equipment_map_id_pk',0));
	    $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
		$product_id_fk = intval($request->post('product_id_fk',0));
		$product_code = trim($request->post('product_code',''));
		$equipment_id_fk = intval($request->post('equipment_id_fk',0));
		
		$equipment_name = trim($request->post('equipment_name',''));
		$equipment_model = trim($request->post('equipment_model',''));
		$calibration_due_date = trim($request->post('calibration_due_date',''));
		$preventive_m_due_date = trim($request->post('preventive_m_due_date',''));
		$activity = trim($request->post('activity',''));
		
		$start_date_time = trim($request->post('start_date_time',''));
		if($start_date_time!='')
			$start_date_time = date("Y-m-d H:i:s",strtotime($start_date_time));
		else
			$start_date_time = '0000-00-00 00:00:00';
			
		$end_date_time = trim($request->post('end_date_time',''));
		if($end_date_time!='')
			$end_date_time = date("Y-m-d H:i:s",strtotime($end_date_time));
		else
			$end_date_time = '0000-00-00 00:00:00';
			
		$dept_assigned_to = trim($request->post('dept_assigned_to',''));
		$cleaning_agent = trim($request->post('cleaning_agent',''));
		$product_name = trim($request->post('product_name',''));
		$product_part = trim($request->post('product_part',''));
		$comments = trim($request->post('comments',''));
		$operator_signature = intval($request->post('operator_signature',''));
		
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mpr_defination_id_fk);
		
        if(isset($_POST) && $equipment_id_fk>0 && $rflag!='Yes')
		{
			$target_name1 = ''; $uploadedFile1= $PreviousFile;
			if(isset($_FILES['attachment']["name"]) && $_FILES['attachment']["name"]!=''){
				$folderPath = Yii::$app->basePath.'/web/uploads/equipmentmap/';
				
				$attachment = $_FILES['attachment']["name"];
				$ext1 = pathinfo($attachment,PATHINFO_EXTENSION);
				
				$souce_file_name1 = $_FILES['attachment']["tmp_name"];
				$target_name1 = time().".".$ext1;
				$target_file1 = $folderPath . basename($target_name1);
				$uploadSuccess1 = move_uploaded_file($souce_file_name1,$target_file1);
				
				if($uploadSuccess1)
					$uploadedFile1 = $target_name1;
				else 
					$uploadedFile1 = '';
			}
				
			$command = Yii::$app->db->createCommand()
				->update('bpr_equipment_map', [
				'mpr_defination_id_fk' => $mpr_defination_id_fk,
				'product_id_fk' => $product_id_fk,
				'product_code' => trim($product_code),
				'equipment_id_fk' => $equipment_id_fk,
				
				'equipment_name' => $equipment_name,
				'equipment_model' => $equipment_model,
				'calibration_due_date' => date("Y-m-d",strtotime($calibration_due_date)),
				'preventive_m_due_date' => date("Y-m-d",strtotime($preventive_m_due_date)),
				'activity' => $activity,
				'start_date_time' => $start_date_time,
				'end_date_time' => $end_date_time,
				'dept_assigned_to' => $dept_assigned_to,
				'cleaning_agent' => $cleaning_agent,
				'batch' => '',
				'product_name' => $product_name,
				'product_part' => $product_part,
				'attachment' => $uploadedFile1,
				'comments' => $comments,
				'operator_signature' => $operator_signature,
				
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],'equipment_map_id_pk="'.$id.'"')->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipmentmap/equipmentmap/view?id=".$id.'&mprid='.$_POST['mpr_defination_id_fk'];
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated MPR equipment record',$addUrl);
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mpr_defination_id_fk."&tab=equipments");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of MPR equipment',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('update', [
				'model' => $model,
				'mprDef' => $mprDef,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Deletes an existing Equipmentmap model.
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
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipmentmap/equipmentmap/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			$model = $this->findModel($id);
			$command = Yii::$app->db->createCommand()
							->update('bpr_equipment_map', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'equipment_map_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=equipments");
		exit;
    }
	
	public function actionRestore($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipmentmap/equipmentmap/view?id=".$id.'&mprid='.$mprid;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			$command = Yii::$app->db->createCommand()
							->update('bpr_equipment_map', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'equipment_map_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=equipments");
		exit;
    }

    /**
     * Finds the Equipmentmap model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Equipmentmap the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Equipmentmap::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
