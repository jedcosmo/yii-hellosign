<?php

namespace backend\modules\personcompany\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\personcompany\models\Personcompany;
use backend\modules\personcompany\models\PersoncompanySearch;
use backend\modules\personcompany\models\PersoncompanySearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class PersoncompanyController extends Controller
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
								   return CommonFunctions::isAccessibleMaster();
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
        $searchModel = new PersoncompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed person company listing page',"");

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionExcel()
	{		
		$searchModel = new PersoncompanySearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['EXPORT'],'Exported person companies',"");
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{		
		$searchModelDel = new PersoncompanySearchDel();
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
	   $model = Personcompany::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."personcompany/personcompany");
			exit;
	   }
	   
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
						$count = Personcompany::find()->where([
										'name' => trim($gmp_company_name)
										])->andWhere("company_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Personcompany::find()->where([
										'name' => trim($gmp_company_name)
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
        $model = new Personcompany();
		
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
		
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_person_company WRITE,bpr_role WRITE,bpr_unit WRITE,bpr_unit_conversion WRITE,bpr_country_dump WRITE, bpr_state_dump WRITE, bpr_city_dump WRITE, bpr_country WRITE, bpr_state WRITE, bpr_city WRITE")->query();
			
			$fieldArray = array('name' => trim($gmp_company_name));
			$is_company_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_company_exists=="No"){
				$command = Yii::$app->db->createCommand()
					->insert('bpr_person_company', [
					'name' => $gmp_company_name,
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
				])->execute();
				
				$supercompid = Yii::$app->db->getLastInsertID();
				$command = Yii::$app->db->createCommand()
					->insert('bpr_role', [
					'is_administrator' => '1',
					'name' => 'Administrator',
					'modules' => '__ALL__',
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => $supercompid,				
				])->execute();
				
				if($supercompid>0)
				{
					CommonFunctions::dumpDefaultUnitsForCompany($supercompid);
					CommonFunctions::dumpDefaultGeographicsForCompany($supercompid);
				}
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['ADD'],'Added new person company',"");
			return $this->redirect(['index']);
		}else{ 			
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
		$model = $this->findModel($id);

	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."personcompany/personcompany");
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
				->update('bpr_person_company', [
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
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated person company',"");
			return $this->redirect(['index']);
		}else{
						
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
	   $model = Personcompany::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."personcompany/personcompany");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_person_company', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1', 'reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'company_id_pk='.$id)
							->execute();
							
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['DELETE'],'Deleted person company',"");
			
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
	   $model = Personcompany::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person_company','company_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."personcompany/personcompany");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_person_company', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'company_id_pk='.$id)
							->execute();
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['PersonCompany'],Yii::$app->params['audit_log_action']['RESTORED'],'Restored person company',"");
						
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record restored successfully.');
		}else{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'Please enter valid reason.');
		}
        return $this->redirect(['index']);
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
        if (($model = Personcompany::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
