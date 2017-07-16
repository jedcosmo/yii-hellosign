<?php

namespace backend\modules\state\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\state\models\State;
use backend\modules\state\models\StateSearch;
use backend\modules\state\models\StateSearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * StateController implements the CRUD actions for State model.
 */
class StateController extends Controller
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
								   return CommonFunctions::isAccessible('RM_State');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all State models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of states',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new StateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on states',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new StateSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}
	
	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of states',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new StateSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single State model.
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_state','state_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."state/state");
			exit;
	    }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of state id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionStateunique()
	{
		$request = Yii::$app->request;
		$gmp_state_name = trim($request->post('gmp_state_name'),'');
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk'),0);
		$edit_id = intval($request->post('edit_id'),0);
		
		$condition = '';
		if(strlen($gmp_state_name) > 0){
			$count = 0;
			if($edit_id > 0){
				$count = state::find()->where([
								'name' => trim($gmp_state_name),
								'country_id_fk' => intval($gmp_country_id_fk),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->andWhere("state_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
			}else{
				$count = state::find()->where([
								'name' => trim($gmp_state_name),
								'country_id_fk' => intval($gmp_country_id_fk),
								'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
								])->count();
			}
			return ($count > 0?"false":"true");
		}
	}
    /**
     * Creates a new State model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new State();
		
		$request = Yii::$app->request;
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_state_name = trim($request->post('gmp_state_name',''));

        if(isset($_POST) && strlen($gmp_state_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_state WRITE,activity_log WRITE")->query();
			
			$fieldArray = array('name' => trim($gmp_state_name),'country_id_fk' => intval($gmp_country_id_fk),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_state_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_state_exists=="No")
			{
				$model->name = trim($gmp_state_name);
				$model->country_id_fk = trim($gmp_country_id_fk);
				$model->isDeleted = '0';
				$model->created_datetime = date("Y-m-d H:i:s");
				$model->addedby_person_id_fk = Yii::$app->user->id;
				$model->super_company_id_fk = Yii::$app->user->identity->super_company_id_fk;
				if($model->save())
				{
					$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."state/state/view?id=".$model->state_id_pk;
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ADD'],'Added state with name:"'.$model->name.'"',$addUrl);
					
				}
			}
			return $this->redirect(['index']);
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add state screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing State model.
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_state','state_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."state/state");
			exit;
	    }
		$model = $this->findModel($id);

		$request = Yii::$app->request;
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_state_name = trim($request->post('gmp_state_name',''));

        if(isset($_POST) && strlen($gmp_state_name) > 0){
			$model->name = trim($gmp_state_name);
			$model->country_id_fk = trim($gmp_country_id_fk);
			if($model->save())
			{
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."state/state/view?id=".$id;
				
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated state record of id:"'.$id.'"',$addUrl);
            	return $this->redirect(['index']);
			}
			else
			{
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of state id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				return $this->render('update', [
                	'model' => $model,
            	]);
			}
        } else {
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of state id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing State model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
		$model = $this->findModel($id);  
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_state','state_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."state/state");
			exit;
	    }
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."state/state/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_state', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'state_id_pk='.$id)
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
	    if(!CommonFunctions::lockThisRecord($id,$model,'bpr_state','state_id_pk'))
	    {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."state/state");
			exit;
	    }
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."state/state/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['States'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_state', ['addedby_person_id_fk' => Yii::$app->user->id,'isDeleted' => '0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'state_id_pk='.$id)
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
     * Finds the State model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return State the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = State::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
