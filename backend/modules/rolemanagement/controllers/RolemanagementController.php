<?php

namespace backend\modules\rolemanagement\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\rolemanagement\models\Rolemanagement;
use backend\modules\rolemanagement\models\RolemanagementSearch;
use backend\modules\rolemanagement\models\RolemanagementSearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * RolemanagementController implements the CRUD actions for Rolemanagement model.
 */
class RolemanagementController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Role_Management');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

	public function actionRoleunique()
	{
		$request = Yii::$app->request;
		$gmp_role = trim($request->post('gmp_role',''));
		$edit_id = intval($request->post('edit_id',0));
		
		$condition = '';
		if(strlen($gmp_role) > 0){
			$count = 0;
			if($edit_id > 0){
				$count = Rolemanagement::find()->where([
								'name' => trim($gmp_role),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
								])->andWhere("role_id_pk != :edit_id AND is_administrator=0",[':edit_id' => $edit_id])->count();
			}else{
				$count = Rolemanagement::find()->where([
								'name' => trim($gmp_role),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								'is_administrator' => "0",
								])->count();
			}
			return ($count > 0?"false":"true");
		}
	}
    /**
     * Lists all Rolemanagement models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of role management',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new RolemanagementSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on role management',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new RolemanagementSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of role management',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new RolemanagementSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}

    /**
     * Displays a single Rolemanagement model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Rolemanagement::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_role','role_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."rolemanagement/rolemanagement");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of role id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Rolemanagement model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Rolemanagement();
		
		$request = Yii::$app->request;
		$gmp_role_id_pk = intval($request->post('gmp_role_id_pk',0));
		$gmp_role = trim($request->post('gmp_role',''));

        if(isset($_POST) && strlen($gmp_role) > 0){	
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_role WRITE,activity_log WRITE")->query();
			
			//$is_role_exists = checkCommonFunctions::check_if_role_exists($model,$gmp_role,$gmp_role_id_pk);
			$fieldArray = array('name' => trim($gmp_role),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,'is_administrator'=>'0');
			$is_role_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_role_exists == "No"){
				if(isset($_POST['all_chk']) && $_POST['all_chk']!=''){
					$model->modules = $_POST['all_chk'];
				}elseif(isset($_POST['roleModules']) && count($_POST['roleModules']) > 0){
					$model->modules = implode(",",$_POST['roleModules']);
				}
				$model->name = trim($gmp_role);
				$model->isDeleted = '0' ;
				$model->addedby_person_id_fk = Yii::$app->user->id;
				$model->created_datetime = date("Y-m-d H:i:s");
				$model->super_company_id_fk = Yii::$app->user->identity->super_company_id_fk;	
				if($model->save()) 
				{
					$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."rolemanagement/rolemanagement/view?id=".$model->role_id_pk;
					
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ADD'],'Added role with name:"'.$model->name.'"',$addUrl);
				}
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
            return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add role screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Rolemanagement model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id))){	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Rolemanagement::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_role','role_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."rolemanagement/rolemanagement");
			exit;
	   }
	   
		$request = Yii::$app->request;
		$gmp_role_id_pk = intval($request->post('gmp_role_id_pk',0));
		$gmp_role = trim($request->post('gmp_role',''));
		
        $model = $this->findModel($id);
		
		if(checkCommonFunctions::check_if_role_is_administrator($model->role_id_pk)=='Yes')
		{	
			throw new HttpException(403,"You are not authorized to perform this action.");
		}

		if(isset($_POST) && strlen($gmp_role) > 0){
			if(isset($_POST['all_chk']) && $_POST['all_chk']!='')
			{
				$model->modules = $_POST['all_chk'];
			}
			elseif(isset($_POST['roleModules']) && count($_POST['roleModules'])>0)
			{
				$model->modules = implode(",",$_POST['roleModules']);
			}
			$model->name = trim($gmp_role);
	
        if ($model->save()) {
			$updtUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."rolemanagement/rolemanagement/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated role record of id:"'.$id.'"',$updtUrl);
			}

            return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of role id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Rolemanagement model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
		$model = Rolemanagement::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_role','role_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."rolemanagement/rolemanagement");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."rolemanagement/rolemanagement/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_role', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'role_id_pk='.$id)
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
		$model = Rolemanagement::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_role','role_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."rolemanagement/rolemanagement");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."rolemanagement/rolemanagement/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['RoleManagement'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_role', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'role_id_pk='.$id)
							->execute();
							
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record restored successfully.');
		}else{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'Please enter valid reason.');
		}
        return $this->redirect(['index']);
    }


	public function actionLists($id)
    {
	
        $count = Rolemanagement::find()
				->where(['super_company_id_fk' => $id, 'isDeleted' => '0'])
				->count();
	 
		$roles = Rolemanagement::find()
				->where(['super_company_id_fk' => $id, 'isDeleted' => '0'])
				->all();
			
	
		echo "<option value=''>--- Select One ---</option>";
        if($count>0){
			
            foreach($roles as $k=>$v){
                echo "<option value='".$v->role_id_pk."'>".$v->name."</option>";
            }
        }
	}
	
    /**
     * Finds the Rolemanagement model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rolemanagement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rolemanagement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
