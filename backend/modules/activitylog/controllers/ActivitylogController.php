<?php

namespace backend\modules\activitylog\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\activitylog\models\ActivityLogSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;

/**
 * ActivitylogController implements the CRUD actions for ActivityLog model.
 */
class ActivitylogController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Audit_Log');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all ActivityLog models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ActivityLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$availableScreens = array();
		$availableActions = array();
		$availablePersons = array();
		$auditlogs = Yii::$app->db->createCommand('SELECT id, userid, type, action, message, urltext, added_date, super_company_id_fk FROM activity_log WHERE super_company_id_fk=:super_company_id_fk', [':super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk])->queryAll();
		if(is_array($auditlogs) && count($auditlogs)>0)
		{
			foreach($auditlogs as $k=>$v)
			{
				$availablePersons[] = $v['userid'];
				$availableActions[] = $v['action'];
				$availableScreens[] = $v['type'];
			}
		}
		
		/*if(Yii::$app->user->identity->is_super_admin!=1)
			$availablePersons = array(Yii::$app->user->identity->person_id_pk);*/
		
		if(is_array($availableScreens) && count($availableScreens)>0)
			$availableScreens = array_unique($availableScreens);
			
		if(is_array($availableActions) && count($availableActions)>0)
			$availableActions = array_unique($availableActions);
			
		if(is_array($availablePersons) && count($availablePersons)>0)
			$availablePersons = array_unique($availablePersons);
			
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'availableScreens' => $availableScreens,
			'availableActions' => $availableActions,
			'availablePersons' => $availablePersons,
        ]);
    }
 
    /**
     * Displays a single ActivityLog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ActivityLog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ActivityLog();
		$model->added_date = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ActivityLog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ActivityLog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ActivityLog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActivityLog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ActivityLog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
