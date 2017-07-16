<?php

namespace backend\modules\adminUser\controllers;

use Yii;
use backend\modules\adminUser\models\Admin;
use backend\modules\adminUser\models\AdminSearch;
use backend\modules\activitylog\models\ActivityLog;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\imagine\Image;
/**
 * AdminUserController implements the CRUD actions for Admin model.
 */
class AdminUserController extends Controller
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
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Admin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Admin model.
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
     * Creates a new Admin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Admin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
		$model = $this->findModel($id);
		
		
		$image_admin = $model->image;	
		if($model->load(Yii::$app->request->post()) )
		{
			
			$model->image =  UploadedFile::getInstance($model, 'image');
			if($model->image)
			{
				$size = getimagesize($_FILES['Admin']['tmp_name']['image']);
						
				$warr = explode(" ",$size[3]);
				$width = explode('"',$warr[0])[1];
				$height = explode('"',$warr[1])[1];
				$originalImage = $model->image;
			}
			
			
			if($model->image == '')
			{
				$model->image = $image_admin;
				
				if ($model->save()) {
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else
				{
					return $this->render('update', [
						'model' => $model,
						'error_message' => 'error',
					]);
				}
				
				
			}else
			{
				
				if ($model->save() && $model->upload()) {						
					
					$croppedImageName = 'c_'.$model->image->baseName .'_'.rand(10000,999999). '.' . $model->image->extension;
					Image::crop(Yii::getAlias('@webroot/uploads/'.$originalImage), $_POST['image-cropping']['width'], $_POST['image-cropping']['height'],[ $_POST['image-cropping']['x'], $_POST['image-cropping']['y']])->save(Yii::getAlias('@webroot/uploads/'.$croppedImageName), ['quality' => 80]);
					
					$command = Yii::$app->db->createCommand()
							->update('bpr_admin', ['image'=>$croppedImageName], 'id='.$model->id)
	            			->execute();
					return $this->redirect(['view', 'id' => $model->id]);
				}
				else
				{
				
					return $this->render('update', [
						'model' => $model,
						'error_message' => 'error',
					]);
				}
			}
			
			
		} else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Admin model.
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
     * Finds the Admin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

	/**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
	
}
