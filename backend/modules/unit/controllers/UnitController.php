<?php

namespace backend\modules\unit\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\unit\models\Unit;
use backend\modules\unit\models\UnitSearch;
use backend\modules\unit\models\UnitSearchDel;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\imagine\Image;
use yii\web\HttpException;
use yii\web\Request;

/**
 * UnitController implements the CRUD actions for Unit model.
 */
class UnitController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Unit');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Unit models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of units',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new UnitSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);		
    }

	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on units',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new UnitSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of units',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new UnitSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Unit model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Unit::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_unit','unit_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."unit/unit");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ACCESS'],'Access details of unit id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionUnitunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		
		switch($action){
			case "check_duplicate_unit":
				try{
						$gmp_unit_name = trim($request->post('gmp_unit_name',''));
						$edit_id = intval($request->post('edit_id',0));
						$unit_description = trim($request->post('unit_description',''));
						
						$condition = '';
						if(strlen($gmp_unit_name) > 0){
							$count = 0;
							if($edit_id > 0){
								$count = Unit::find()->where([
												'name' => trim($gmp_unit_name),
												'description' => trim($unit_description),
												'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
												])->andWhere("unit_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
							}else{
								$count = Unit::find()->where([
												'name' => trim($gmp_unit_name),
												'description' => trim($unit_description),
												'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
												])->count();
							}
							
							return ($count > 0?"false":"true");
						}
					}catch(Exception $e){ return false; }
			break;
			case "check_duplicate_desc":
				try{
					$gmp_unit_name = trim($request->post('gmp_unit_name',''));
					$edit_id = intval($request->post('edit_id',0));
					$gmp_unit_description = trim($request->post('gmp_unit_description',''));
					
					$condition = '';
					if(strlen($gmp_unit_description) > 0){
						$count = 0;
						if($edit_id > 0){
							$count = Unit::find()->where([
											'name' => trim($gmp_unit_name),
											'description' => trim($gmp_unit_description),
											'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
											])->andWhere("unit_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
						}else{
							$count = Unit::find()->where([
											'name' => trim($gmp_unit_name),
											'description' => trim($gmp_unit_description),
											'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
											])->count();
						}
						return ($count > 0?"false":"true");
					}
				}catch(Exception $e){ return false; }	
			break;
		}
	}

    /**
     * Creates a new Unit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new Unit();
		
		$request = Yii::$app->request;
		$gmp_unit_id_pk = intval($request->post('gmp_unit_id_pk',0));
		$gmp_unit_name = trim($request->post('gmp_unit_name',''));
		$gmp_unit_description = trim($request->post('gmp_unit_description',''));
			
		if(isset($_POST) && strlen($gmp_unit_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_unit WRITE,activity_log WRITE")->query();
			
			//$is_unit_exists = checkCommonFunctions::check_if_unit_exists($model,$gmp_unit_name,$gmp_unit_id_pk,$gmp_unit_description);
			$fieldArray = array(
								'name' => trim($gmp_unit_name), 
								'description' => trim($gmp_unit_description), 
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
								);
			$is_unit_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_unit_exists=="No"){
				$target_name = ''; $uploadedFile= '';
				if(isset($_FILES['gmp_unit_symbols']["name"]) && $_FILES['gmp_unit_symbols']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/symbols/';
					
					$gmp_unit_symbols = $_FILES['gmp_unit_symbols']["name"];
					$ext = pathinfo($gmp_unit_symbols,PATHINFO_EXTENSION);
					
					$souce_file_name = $_FILES['gmp_unit_symbols']["tmp_name"];
					$target_name = time().".".$ext;
					$target_file = $folderPath . basename($target_name);
					$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
					
					if($uploadSuccess)
						$uploadedFile = $target_name;
					else 
						$uploadedFile = '';
				}
				
				$command = Yii::$app->db->createCommand()
					->insert('bpr_unit', [
					'name' => $gmp_unit_name,
					'description' => $gmp_unit_description,
					'symbols'=> $uploadedFile,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
				])->execute();						
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."unit/unit/view?id=".$id;
		
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ADD'],'Added unit with name:"'.$_POST['gmp_unit_name'].'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add unit screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    /**
     * Updates an existing Unit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {		
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id))){	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Unit::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_unit','unit_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."unit/unit");
			exit;
	   }
	   
		$request = Yii::$app->request;
		$gmp_unit_id_pk = intval($request->post('gmp_unit_id_pk',0));
		$gmp_unit_name = trim($request->post('gmp_unit_name',''));
		$gmp_unit_description = trim($request->post('gmp_unit_description',''));
		
        $model = $this->findModel($id);
		$PreviousImage = $model->symbols;
        
			if(isset($_POST) && strlen($gmp_unit_name) > 0){
				$model = $this->findModel($gmp_unit_id_pk);
				$PreviousImage = $model->symbols;
			
				if(isset($_FILES['gmp_unit_symbols']["name"]) && $_FILES['gmp_unit_symbols']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/symbols/';
						
					$gmp_unit_symbols = $_FILES['gmp_unit_symbols']["name"];
					$ext = pathinfo($gmp_unit_symbols,PATHINFO_EXTENSION);
					
					$souce_file_name = $_FILES['gmp_unit_symbols']["tmp_name"];
					$target_name = time().".".$ext;
					$target_file = $folderPath . basename($target_name);
					$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
					
					if($uploadSuccess)
						$uploadedFile = $target_name;
					else 
						$uploadedFile = '';
						
					$command = Yii::$app->db->createCommand()
							->update('bpr_unit', [
							'name' => $gmp_unit_name,
							'description' => $gmp_unit_description,
							'symbols'=> $uploadedFile,
							'addedby_person_id_fk' => Yii::$app->user->id,
							'created_datetime' => date("Y-m-d H:i:s"),				
						],"unit_id_pk='".$gmp_unit_id_pk."'")->execute();
					
				}else{
					$command = Yii::$app->db->createCommand()
						->update('bpr_unit', [
						'name' => $gmp_unit_name,
						'description' => $gmp_unit_description,
						'addedby_person_id_fk' => Yii::$app->user->id,
						'created_datetime' => date("Y-m-d H:i:s"),				
					],"unit_id_pk='".$gmp_unit_id_pk."'")->execute();
				}
				
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."unit/unit/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated unit record of id:"'.$id.'"',$addUrl);
			return $this->redirect(['index']);
		}else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of unit id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Unit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
		$model = Unit::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_unit','unit_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."unit/unit");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."unit/unit/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			$model = $this->findModel($id);
				
			$command = Yii::$app->db->createCommand()
							->update('bpr_unit', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason,'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'unit_id_pk='.$id)
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
		$model = Unit::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_unit','unit_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."unit/unit");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."unit/unit/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Units'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			$command = Yii::$app->db->createCommand()
							->update('bpr_unit', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'unit_id_pk='.$id)
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
     * Finds the Unit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Unit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */ 
	 
    protected function findModel($id)
    {
        if (($model = Unit::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
