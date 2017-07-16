<?php

namespace backend\modules\company\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\company\models\Company;
use backend\modules\company\models\CompanySearch;
use backend\modules\company\models\CompanySearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class CompanyController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Company');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of companies',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new CompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on companies',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new CompanySearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of companies',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new CompanySearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Company model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Company::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."company/company");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ACCESS'],'Access details of company id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


	public function actionCompanyunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		$gmp_company_name = trim($request->post('gmp_company_name',''));
		$edit_id = intval($request->post('edit_id',0));
		
		switch($action){
			case "check_duplicate_company":
				$condition = '';
				if(strlen($gmp_company_name) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Company::find()->where([
										'name' => trim($gmp_company_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("company_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Company::find()->where([
										'name' => trim($gmp_company_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}
			break;
		}
	}
    /**
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new Company();
		
		$request = Yii::$app->request;
		$gmp_company_name = trim($request->post('gmp_company_name',''));
		$gmp_address1 = trim($request->post('gmp_address1',''));
		$gmp_address2 = trim($request->post('gmp_address2',''));
		$gmp_city_id_fk = intval($request->post('gmp_city_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_pobox = trim($request->post('gmp_pobox',''));
		$gmp_zip_pincode = trim($request->post('gmp_zip_pincode',''));
		
		$gmp_company_id_pk = intval($request->post('gmp_company_id_pk',0));
		
		if(isset($_POST) && strlen($gmp_company_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_company WRITE,activity_log WRITE")->query();
			
			//$is_company_exists = checkCommonFunctions::check_if_company_exists($model,$gmp_company_name,$gmp_company_id_pk);
			$fieldArray = array('name' => trim($gmp_company_name),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_company_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_company_exists == "No"){
				$command = Yii::$app->db->createCommand()
					->insert('bpr_company', [
					'name' => trim($gmp_company_name),
					'address1' => trim($gmp_address1),
					'address2' => trim($gmp_address2),
					'city_id_fk' => $gmp_city_id_fk,
					'state_id_fk' => $gmp_state_id_fk,
					'country_id_fk' => $gmp_country_id_fk,
					'pobox'=> trim($gmp_pobox),
					'zip_postalcode' => trim($gmp_zip_pincode),
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),		
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,		
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."company/company/view?id=".$id;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ADD'],'Added company with name:"'.$gmp_company_name.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add company screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
			]);
		} 
      
    }

    /**
     * Updates an existing Company model.
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
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."company/company");
			exit;
	   }
	   
		$request = Yii::$app->request;
		$gmp_company_name = trim($request->post('gmp_company_name',''));
		$gmp_address1 = trim($request->post('gmp_address1',''));
		$gmp_address2 = trim($request->post('gmp_address2',''));
		$gmp_city_id_fk = intval($request->post('gmp_city_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_pobox = trim($request->post('gmp_pobox',''));
		$gmp_zip_pincode = trim($request->post('gmp_zip_pincode',''));
		
		$gmp_company_id_pk = intval($request->post('gmp_company_id_pk',0));

		if(isset($_POST) && strlen($gmp_company_name) > 0){
			$command = Yii::$app->db->createCommand()
				->update('bpr_company', [
				'name' => trim($gmp_company_name),
				'address1' => trim($gmp_address1),
				'address2' => trim($gmp_address2),
				'city_id_fk' => $gmp_city_id_fk,
				'state_id_fk' => $gmp_state_id_fk,
				'country_id_fk' => $gmp_country_id_fk,
				'pobox'=> trim($gmp_pobox),
				'zip_postalcode' => trim($gmp_zip_pincode),
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],"company_id_pk='".$id."'")->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."company/company/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated company record of id:"'.$id.'"',$addUrl);
			return $this->redirect(['index']);
		}else{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of company id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
	   $model = Company::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."company/company");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."company/company/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_company', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'company_id_pk='.$id)
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
	   $model = Company::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."company/company");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."company/company/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Company'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_company', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'company_id_pk='.$id)
							->execute();
							
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record restored successfully.');
		}else{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'Please enter valid reason.');
		}
        return $this->redirect(['index']);
    }
	
	/*
	* Get State combobox
	*/
	public function actionLists($id)
    {
	
        $count = \backend\modules\state\models\State::find()
				->where(['bpr_state.country_id_fk' => $id, 'bpr_state.isDeleted' => '0'])
				->orderby('name ASC')
				->count();
	 
		$states = \backend\modules\state\models\state::find()
				->where(['bpr_state.country_id_fk' => $id, 'bpr_state.isDeleted' => '0'])
				->orderby('name ASC')
				->all();
			
	
		echo "<option value=''>--- Select One ---</option>";
        if($count>0){
			
            foreach($states as $k=>$v){
                echo "<option value='".$v->state_id_pk."'>".$v->name."</option>";
            }
        }
	}
	
	/*
	* Get City combobox
	*/
	public function actionCitylists($id)
    {
	
        $count = \backend\modules\city\models\City::find()
				->where(['bpr_city.state_id_fk' => $id, 'bpr_city.isDeleted' => '0'])
				->orderby('name ASC')
				->count();
	 
		$states = \backend\modules\city\models\City::find()
				->where(['bpr_city.state_id_fk' => $id, 'bpr_city.isDeleted' => '0'])
				->orderby('name ASC')
				->all();
			
	
		echo "<option value=''>--- Select One ---</option>";
        if($count>0){
			
            foreach($states as $k=>$v){
                echo "<option value='".$v->city_id_pk."'>".$v->name."</option>";
            }
        }
	}
    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
