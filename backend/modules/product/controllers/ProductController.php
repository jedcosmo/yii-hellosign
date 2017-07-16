<?php

namespace backend\modules\product\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\product\models\Product;
use backend\modules\product\models\ProductSearch;
use backend\modules\product\models\ProductSearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Product');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of products',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on products',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new ProductSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of products',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new ProductSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Product model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Product::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_product','product_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."product/product");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ACCESS'],'Access details of product id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionCodeunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		
		switch($action){
			case "check_duplicate_code":
				$gmp_product_code = trim($request->post('gmp_product_code',''));
				$edit_id = trim($request->post('edit_id',0));
				$condition = '';
				if(strlen($gmp_product_code) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Product::find()->where([
										'code' => trim($gmp_product_code),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("product_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Product::find()->where([
										'code' => trim($gmp_product_code),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}
			break;
		}
	}
    /**
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new Product();
		
		$request = Yii::$app->request;
		$gmp_product_id_pk = intval($request->post('gmp_product_id_pk',0));
		$gmp_product_name = trim($request->post('gmp_product_name',''));
		$gmp_company = trim($request->post('gmp_company',''));
		$gmp_product_code = trim($request->post('gmp_product_code',''));
		$gmp_product_unit = trim($request->post('gmp_product_unit',''));

        if(isset($_POST) && strlen($gmp_product_name) > 0){
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_product WRITE,bpr_documents WRITE,activity_log WRITE")->query();
			$target_name = ''; $uploadedFile= '';
			$docid = 0;
			
			//$is_product_exists = checkCommonFunctions::check_if_product_exists($model,$gmp_product_code,$gmp_product_id_pk);
			$fieldArray = array('code' => trim($gmp_product_code),'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk);
			$is_product_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_product_exists=="No"){
				if(isset($_FILES['gmp_product_document']["name"]) && $_FILES['gmp_product_document']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/documents/';
					
					$gmp_product_document = $_FILES['gmp_product_document']["name"];
					$ext = pathinfo($gmp_product_document,PATHINFO_EXTENSION);
					
					$souce_file_name = $_FILES['gmp_product_document']["tmp_name"];
					$target_name = time().".".$ext;
					$target_file = $folderPath . basename($target_name);
					$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
					
					if($uploadSuccess)
					{
						$uploadedFile = $target_name;
						$command = Yii::$app->db->createCommand()
							->insert('bpr_documents', [
							'docname' => $target_name,
							'isDeleted' => '0',
							'addedby_person_id_fk' => Yii::$app->user->id,
							'created_datetime' => date("Y-m-d H:i:s"),	
							'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,			
						])->execute();
						 $docid = Yii::$app->db->getLastInsertID();
					}
					else 
						$uploadedFile = '';
				}
		
				$command = Yii::$app->db->createCommand()
					->insert('bpr_product', [
					'name' => trim($gmp_product_name),
					'company_id_fk' => trim($gmp_company),
					'code' => trim($gmp_product_code),
					'unit_id_fk' => trim($gmp_product_unit),
					'document_id_fk' => $docid,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,				
				])->execute();
				$productid = Yii::$app->db->getLastInsertID();
				if($productid)
				{
					$productPart = 100 + $productid;
					$command = Yii::$app->db->createCommand()
						->update('bpr_product', [
						'part'=> $productPart,	
					],"product_id_pk='".$productid."'")->execute();
				}
				
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."product/product/view?id=".$productid;
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ADD'],'Added product with name:"'.$gmp_product_name.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add product screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
			]);
		}
    }

    /**
     * Updates an existing Product model.
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
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_product','product_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."product/product");
			exit;
	   }
	   
		$request = Yii::$app->request;
		$gmp_product_id_pk = intval($request->post('gmp_product_id_pk',0));
		$gmp_product_name = trim($request->post('gmp_product_name',''));
		$gmp_company = trim($request->post('gmp_company',''));
		$gmp_product_code = trim($request->post('gmp_product_code',''));
		$gmp_product_unit = trim($request->post('gmp_product_unit',''));

        if(isset($_POST) && strlen($gmp_product_name) > 0){
			$productid = $id;	
			$docid = 0;	
			if(isset($_FILES['gmp_product_document']["name"]) && $_FILES['gmp_product_document']["name"]!=''){
				$folderPath = Yii::$app->basePath.'/web/uploads/documents/';
				
				$gmp_product_document = $_FILES['gmp_product_document']["name"];
				$ext = pathinfo($gmp_product_document,PATHINFO_EXTENSION);
				
				$souce_file_name = $_FILES['gmp_product_document']["tmp_name"];
				$target_name = time().".".$ext;
				$target_file = $folderPath . basename($target_name);
				$uploadSuccess = move_uploaded_file($souce_file_name,$target_file);
				
				if($uploadSuccess)
				{
					$uploadedFile = $target_name;
					$command = Yii::$app->db->createCommand()
						->insert('bpr_documents', [
						'docname' => $target_name,
						'isDeleted' => '0',
						'addedby_person_id_fk' => Yii::$app->user->id,
						'created_datetime' => date("Y-m-d H:i:s"),				
					])->execute();
					 $docid = Yii::$app->db->getLastInsertID();
				}
				else 
					$uploadedFile = '';
			}
			
			$document_id_fk = 0;	
			if($docid>0)
				$document_id_fk = $docid;
			else
				$document_id_fk = $model->document_id_fk;
			
			$command = Yii::$app->db->createCommand()
				->update('bpr_product', [
				'name' => trim($gmp_product_name),
				'company_id_fk' => trim($gmp_company),
				'code' => trim($gmp_product_code),
				'unit_id_fk' => trim($gmp_product_unit),
				'document_id_fk' => $document_id_fk,
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),		
			],"product_id_pk='".$productid."'")->execute();
	
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."product/product/view?id=".$productid;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated product record of id:"'.$id.'"',$addUrl);
			
			return $this->redirect(['index']);
		}else{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of product id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
    }

    /**
     * Deletes an existing Product model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
	   $model = Product::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_product','product_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."product/product");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."product/product/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_product', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'product_id_pk='.$id)
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
	   $model = Product::findOne($id);  
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_product','product_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."product/product");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."product/product/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Product'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_product', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'product_id_pk='.$id)
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
