<?php

namespace backend\modules\city\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\city\models\City;
use backend\modules\city\models\CitySearch;
use backend\modules\city\models\CitySearchDel;
use backend\modules\state\models\State;
use backend\modules\state\models\StateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

@ini_set('memory_limit', '256M');
/**
 * CityController implements the CRUD actions for City model.
 */
class CityController extends Controller
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
								   return CommonFunctions::isAccessible('RM_City');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all City models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of cities',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new CitySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on cities',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		$searchModel = new CitySearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}
	
	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of cities',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new CitySearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single City model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {		
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$model = $this->findModel($id);  
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_city','city_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."city/city");
			exit;
	    }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of city id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionCityunique()
	{
		$request = Yii::$app->request;
		$gmp_city_name = trim($request->post('gmp_city_name'),'');
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk'),0);
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk'),0);
		$edit_id = intval($request->post('edit_id'),0);
		
		$condition = '';
		if(strlen($gmp_city_name) > 0){
			$count = 0;
			if($edit_id > 0){
				$count = City::find()->where([
								'name' => trim($gmp_city_name),
								'country_id_fk' => intval($gmp_country_id_fk),
								'state_id_fk' => intval($gmp_state_id_fk),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->andWhere("city_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
			}else{
				$count = City::find()->where([
								'name' => trim($gmp_city_name),
								'country_id_fk' => intval($gmp_country_id_fk),
								'state_id_fk' => intval($gmp_state_id_fk),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->count();
			}
			return ($count > 0?"false":"true");
		}
		
	}
    /**
     * Creates a new City model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new City();
		
		$request = Yii::$app->request;
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_city_name = trim($request->post('gmp_city_name',''));

        if(isset($_POST) && strlen($gmp_city_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_city WRITE,activity_log WRITE")->query();
			$fieldArray = array('name' => trim($gmp_city_name),'country_id_fk' => intval($gmp_country_id_fk),'state_id_fk' => intval($gmp_state_id_fk),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_city_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			if($is_city_exists=="No")
			{
				$model->name = trim($gmp_city_name);
				$model->country_id_fk = $gmp_country_id_fk;
				$model->state_id_fk = $gmp_state_id_fk;
				$model->isDeleted = '0';
				$model->created_datetime = date("Y-m-d H:i:s");
				$model->addedby_person_id_fk = Yii::$app->user->id;
				$model->super_company_id_fk = Yii::$app->user->identity->super_company_id_fk;
				if($model->save())
				{
					$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."city/city/view?id=".$model->city_id_pk;
					
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ADD'],'Added city with name:"'.$model->name.'"',$addUrl);
				}
			}
			return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add city screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing City model.
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_city','city_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."city/city");
			exit;
	    }
		
        $model = $this->findModel($id);

		$request = Yii::$app->request;
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_city_name = trim($request->post('gmp_city_name',''));
		
		if(isset($_POST) && strlen($gmp_city_name) > 0){
			$model->name = trim($gmp_city_name);
			$model->country_id_fk = $gmp_country_id_fk;
			$model->state_id_fk = $gmp_state_id_fk;

			if($model->save())
			{
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."city/city/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated city record of id:"'.$id.'"',$addUrl);
            	return $this->redirect(['index']);
			}
			else
			{
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of city id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				return $this->render('update', [
                	'model' => $model,
            	]);
			}
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of city id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing City model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
		$model = $this->findModel($id);  
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_city','city_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."city/city");
			exit;
	    }
		
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."city/city/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
		   $command = Yii::$app->db->createCommand()
							->update('bpr_city', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'city_id_pk='.$id)
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_city','city_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."city/city");
			exit;
	    }
		
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."city/city/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['City'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			$command = Yii::$app->db->createCommand()
							->update('bpr_city', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'city_id_pk='.$id)
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
			
	
		echo "<option>---- Select One ----</option>";
        if($count>0){
			
            foreach($states as $k=>$v){
                echo "<option value='".$v->state_id_pk."'>".$v->name."</option>";
            }
        }
	}


    /**
     * Finds the City model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return City the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = City::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
