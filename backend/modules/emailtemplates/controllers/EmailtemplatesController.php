<?php

namespace backend\modules\emailtemplates\controllers;

use Yii;
use backend\modules\emailtemplates\models\Emailtemplates;
use backend\modules\emailtemplates\models\EmailtemplatesSearch;
use backend\modules\activitylog\models\ActivityLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EmailtemplatesController implements the CRUD actions for Emailtemplates model.
 */
class EmailtemplatesController extends Controller
{
    public function behaviors()
    {
        return [
			 [
            'class' => \maxmirazh33\image\Behavior::className(),
            'savePathAlias' => '@web/uploads/',
            'urlPrefix' => '/uploads/',
            'crop' => true,
            'attributes' => [
                'avatar' => [
                    'savePathAlias' => '@web/uploads/',
                    'urlPrefix' => '/uploads/',
                    'width' => 100,
                    'height' => 100,
                ],
                'logo' => [
                    'crop' => false,
                    'thumbnails' => [
                        'mini' => [
                            'width' => 50,
                        ],
                    ],
                ],
            ],
        ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
					//'bulkactions' => ['post'],
                ],
            ],
			'access' => [
                        'class' => \yii\filters\AccessControl::className(),
                        'rules' => [
                            // allow authenticated users
                            [
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Emailtemplates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EmailtemplatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Emailtemplates model.
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
     * Creates a new Emailtemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Emailtemplates();

        if ($model->load(Yii::$app->request->post())) {
			if($model->validate())
			{
				$model->added_date = date("Y-m-d H:i:s");
				if($model->save())
				{
					$activityModel = new ActivityLog();
					$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Add New','Added new email template with subject "'.$model->subject.'"');
					return $this->redirect(['view', 'id' => $model->id]);
				}
			}
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Emailtemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			$activityModel = new ActivityLog();
			$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Update','Edited email template of subject "'.$model->subject.'"');
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Emailtemplates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
		$activityModel = new ActivityLog();
		$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Delete','Removed email template of subject "'.$model->subject.'"');
        $model->delete();
		return $this->redirect(['index']);
    }

	public function actionBulkactions()
	{
		$model = new Emailtemplates();
		if(isset($_POST['tAction']) && $_POST['tAction']!='')
		{
			$ids = '';
			if(isset($_POST['keylist']) && is_array($_POST['keylist']) && count($_POST['keylist'])>0)
			{
				$ids = implode(",",$_POST['keylist']);
			}
			switch($_POST['tAction'])
			{
				case 'Active':	$model->updateAll(array('is_active'=>1),'id in ('.$ids.')');
								$activityModel = new ActivityLog();
								$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Bulk Actions','Marked records as active of ids : "'.$ids.'"');
								break;
				case 'Inactive':$model->updateAll(array('is_active'=>0),'id in ('.$ids.')');
								$activityModel = new ActivityLog();
								$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Bulk Actions','Marked records as inactive of ids : "'.$ids.'"');
								break;
				case 'Delete':	$model->deleteAll(['and', ['in', 'id', $ids]]);
								$activityModel = new ActivityLog();
								$activityModel->logUserActivity(Yii::$app->user->id,'Email Templates','Bulk Actions','Removed records of ids : "'.$ids.'"');
								break;
				default: break;
			}
		}
	}
    /**
     * Finds the Emailtemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Emailtemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Emailtemplates::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
