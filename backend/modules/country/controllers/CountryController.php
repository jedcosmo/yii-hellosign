<?php

namespace backend\modules\country\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\country\models\Country;
use backend\modules\country\models\CountrySearch;
use backend\modules\country\models\CountrySearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * CountryController implements the CRUD actions for Country model.
 */
class CountryController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Country');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }


	public function actionCountryunique()
	{
		$request = Yii::$app->request;
		$gmp_country_name = trim($request->post('gmp_country_name'),'');
		$edit_id = intval($request->post('edit_id'),0);
		
		$condition = '';
		if(strlen($gmp_country_name) > 0){
			$count = 0;
			if($edit_id > 0){
				$count = Country::find()->where([
								'name' => trim($gmp_country_name),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->andWhere("country_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
			}else{
				$count = Country::find()->where([
								'name' => trim($gmp_country_name),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->count();
			}
			return ($count > 0?"false":"true");
		}
	}
    /**
     * Lists all Country models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of countries',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new CountrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on countries',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new CountrySearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}
	
	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of countries',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new CountrySearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Country model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$model = Country::findOne($id);  
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_country','country_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."country/country");
			exit;
	    }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Access details of country id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Country model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
		$model = new Country();
		
		$request = Yii::$app->request;
		$gmp_country_name = trim($request->post('gmp_country_name',''));
		
		if(isset($_POST) && strlen($gmp_country_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_country WRITE,activity_log WRITE")->query();
			//$is_country_exists = checkCommonFunctions::check_if_country_exists($model,trim($gmp_country_name));
			
			$fieldArray = array('name' => trim($gmp_country_name),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_country_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_country_exists=="No")
			{
				$model->name = trim($gmp_country_name);
				$model->isDeleted = '0';
				$model->created_datetime = date("Y-m-d H:i:s");
				$model->addedby_person_id_fk = Yii::$app->user->id;
				$model->super_company_id_fk = Yii::$app->user->identity->super_company_id_fk;
        		if ($model->save()) {
					$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."country/country/view?id=".$model->country_id_pk;
			
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ADD'],'Added country with name:"'.$model->name.'"',$addUrl);
				}
			}
			return $this->redirect(['index']);
		}
        else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add country screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Country model.
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_country','country_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."country/country");
			exit;
	    }

		$request = Yii::$app->request;
		$gmp_country_name = trim($request->post('gmp_country_name',''));
		
       if(isset($_POST) && strlen($gmp_country_name) > 0){
			$model->created_datetime = date("Y-m-d H:i:s");
			$model->addedby_person_id_fk = Yii::$app->user->id;
			$model->name = trim($gmp_country_name);
			if($model->save())
			{
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."country/country/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated country record of id:"'.$id.'"',$addUrl);
				
            	return $this->redirect(['index']);
			}
			else
			{
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of country id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				 return $this->render('update', [
                	'model' => $model,
            		]);
			}
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of country id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Country model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
		$model = $this->findModel($id);
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_country','country_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."country/country");
			exit;
	    }
		
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."country/country/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			$command = Yii::$app->db->createCommand()
							->update('bpr_country', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'country_id_pk='.$id)
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
		$model = $this->findModel($id);
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_country','country_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."country/country");
			exit;
	    }
		
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."country/country/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Country'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_country', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'country_id_pk='.$id)
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
     * Finds the Country model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Country the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Country::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
