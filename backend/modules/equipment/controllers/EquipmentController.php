<?php

namespace backend\modules\equipment\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\equipment\models\Equipment;
use backend\modules\equipment\models\EquipmentSearch;
use backend\modules\equipment\models\EquipmentSearchDel;
use backend\modules\equipment\models\EquipmentSearchExp;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\HttpException;
use yii\web\Request;

/**
 * EquipmentController implements the CRUD actions for Equipment model.
 */
class EquipmentController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Equipment');
							   }
                            ],
                            // everything else is denied
                        ],
                    ], 
        ];
    }


	public function actionEquipmentunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		
		switch($action){
			case "check_duplicate_equipment":
				$gmp_equipment_name = trim($request->post('gmp_equipment_name',''));
				$edit_id = intval($request->post('edit_id',0));
			
				$condition = '';
				if(strlen($gmp_equipment_name) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Equipment::find()->where([
										'name' => trim($gmp_equipment_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("equipment_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Equipment::find()->where([
										'name' => trim($gmp_equipment_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}
			break;
		}
	}
    /**
     * Lists all Equipment models.
     * @return mixed
     */
    public function actionIndex()
    {		
		$activeTab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'Active';
			
		if($activeTab=='Active')
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of active equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		else
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of expired equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
        $searchModel = new EquipmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$searchModelExp = new EquipmentSearchExp();
		$dataProviderExp = $searchModelExp->search(Yii::$app->request->queryParams);
		
        return $this->render('index', [
            'searchModel' => $searchModel,
			'searchModelExp' => $searchModelExp,
            'dataProvider' => $dataProvider,
			'dataProviderExp' => $dataProviderExp,
			'activeTab' => $activeTab,
        ]);
    }
	
	public function actionExcel()
	{		
		$activeTab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'Active';
			
		if($activeTab=='Active')
		{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on active equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        	$searchModel = new EquipmentSearch();
        	$models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		}
		else
		{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on expired equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$searchModel = new EquipmentSearchExp();
			$models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		}
	
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}
	
	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of equipments',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new EquipmentSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}

    /**
     * Displays a single Equipment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Equipment::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_equipment','equipment_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."equipment/equipment");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of equipment id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
		
    }

    /**
     * Creates a new Equipment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {	
        $model = new Equipment();
		
		$request = Yii::$app->request;
		$gmp_equipment_id_pk = intval($request->post('gmp_equipment_id_pk',0));
		$gmp_equipment_name = trim($request->post('gmp_equipment_name',''));
		$gmp_equipment_model = trim($request->post('gmp_equipment_model',''));
		$gmp_equipment_serial = trim($request->post('gmp_equipment_serial',''));
		$caliberation_due_date = trim($request->post('caliberation_due_date',''));
		$preventive_m_due_date = trim($request->post('preventive_m_due_date',''));

		if(isset($_POST) && strlen($gmp_equipment_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_equipment WRITE,bpr_documents WRITE,activity_log WRITE")->query();
			$target_name = ''; $uploadedFile= '';
			$docid = 0;
			
			//$is_equipment_exists = checkCommonFunctions::check_if_equipment_exists($model,$gmp_equipment_name,$gmp_equipment_id_pk);
			$fieldArray = array('name' => trim($gmp_equipment_name),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_equipment_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_equipment_exists=="No"){					
				if(isset($_FILES['gmp_equipment_document']["name"]) && $_FILES['gmp_equipment_document']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/documents/';
					
					$gmp_product_document = $_FILES['gmp_equipment_document']["name"];
					$ext = pathinfo($gmp_product_document,PATHINFO_EXTENSION);
					
					$souce_file_name = $_FILES['gmp_equipment_document']["tmp_name"];
					$target_name = time().".".$ext;
					$target_file = $folderPath . basename($target_name);
					$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
					
					if($uploadSuccess)
					{
						$uploadedFile = $target_name;
						$command = Yii::$app->db->createCommand()
							->insert('bpr_documents', [
							'docname' => $target_name,
							'isDeleted' => '0',
							'addedby_person_id_fk' => Yii::$app->user->id,
							'created_datetime' => date("Y-m-d H:i:s"),
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
						])->execute();
						 $docid = Yii::$app->db->getLastInsertID();
					}
					else 
						$uploadedFile = '';
				}
			
				$command = Yii::$app->db->createCommand()
					->insert('bpr_equipment', [
					'name' => trim($gmp_equipment_name),
					'model' => trim($gmp_equipment_model),
					'serial' => trim($gmp_equipment_serial),
					'caliberation_due_date' => date("Y-m-d",strtotime($caliberation_due_date)),
					'preventive_m_due_date'=> date("Y-m-d",strtotime($preventive_m_due_date)),
					'document_id_fk' => $docid,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipment/equipment/view?id=".$id;
						
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ADD'],'Added equipment with name:"'.$gmp_equipment_name.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add equipment screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
			]);
		}
    }

    /**
     * Updates an existing Equipment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {	
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id))){	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
			
        $model = $this->findModel($id);
 
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_equipment','equipment_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."equipment/equipment");
			exit;
	   }
		
		$request = Yii::$app->request;
		$gmp_equipment_id_pk = intval($request->post('gmp_equipment_id_pk',0));
		$gmp_equipment_name = trim($request->post('gmp_equipment_name',''));
		$gmp_equipment_model = trim($request->post('gmp_equipment_model',''));
		$gmp_equipment_serial = trim($request->post('gmp_equipment_serial',''));
		$caliberation_due_date = trim($request->post('caliberation_due_date',''));
		$preventive_m_due_date = trim($request->post('preventive_m_due_date',''));
		
        if(isset($_POST) && strlen($gmp_equipment_name) > 0){
			$productid = $id;	
			$docid = 0;	
			if(isset($_FILES['gmp_equipment_document']["name"]) && $_FILES['gmp_equipment_document']["name"]!=''){
				$folderPath = Yii::$app->basePath.'/web/uploads/documents/';
				
				$gmp_product_document = $_FILES['gmp_equipment_document']["name"];
				$ext = pathinfo($gmp_product_document,PATHINFO_EXTENSION);
				
				$souce_file_name = $_FILES['gmp_equipment_document']["tmp_name"];
				$target_name = time().".".$ext;
				$target_file = $folderPath . basename($target_name);
				$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
				
				if($uploadSuccess)
				{
					$uploadedFile = $target_name;
					$command = Yii::$app->db->createCommand()
						->insert('bpr_documents', [
						'docname' => $target_name,
						'isDeleted' => '0',
						'addedby_person_id_fk' => Yii::$app->user->id,
						'created_datetime' => date("Y-m-d H:i:s"),				
					])->execute();
					 $docid = Yii::$app->db->getLastInsertID();
				}
				else 
					$uploadedFile = '';
			}
			
			$document_id_fk = 0;	
			if($docid>0)
				$document_id_fk = $docid;
			else
				$document_id_fk = $model->document_id_fk;
			
			$command = Yii::$app->db->createCommand()
				->update('bpr_equipment', [
				'name' => trim($gmp_equipment_name),
				'model' => trim($gmp_equipment_model),
				'serial' => trim($gmp_equipment_serial),
				'caliberation_due_date' => date("Y-m-d",strtotime($caliberation_due_date)),
				'preventive_m_due_date'=> date("Y-m-d",strtotime($preventive_m_due_date)),
				'document_id_fk' => $document_id_fk,
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),		
			],"equipment_id_pk='".$productid."'")->execute();
	
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipment/equipment/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated equipment record of id:"'.$id.'"',$addUrl);
			
			return $this->redirect(['index']);
		}else{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of equipment id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
    }

    /**
     * Deletes an existing Equipment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
	   $model = Equipment::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_equipment','equipment_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."equipment/equipment");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipment/equipment/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_equipment', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'equipment_id_pk='.$id)
							->execute();
	
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record deleted successfully.');
		}else{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'Please enter valid reason.');
		}
        return $this->redirect(['index']);
    }
	
	public function actionRestore($id,$delreason)
    {
	   $model = Equipment::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_equipment','equipment_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."equipment/equipment");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."equipment/equipment/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Equipment'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_equipment', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'equipment_id_pk='.$id)
							->execute();
							
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record restored successfully.');
		}else{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'Please enter valid reason.');
		}
        return $this->redirect(['index']);
    }

    /**
     * Finds the Equipment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Equipment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Equipment::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
