<?php

namespace backend\modules\mprdefinition\controllers;

use Yii;
use backend\modules\activitylog\models\ActivityLog;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\mprdefinition\models\MprdefinitionSearch;
use backend\modules\mprdefinition\models\MprdefinitionSearchDel;
use backend\modules\billofmaterial\models\Billofmaterial;
use backend\modules\billofmaterial\models\BillofmaterialSearch;
use backend\modules\formulation\models\Formulation;
use backend\modules\formulation\models\FormulationSearch;
use backend\modules\equipmentmap\models\Equipmentmap;
use backend\modules\equipmentmap\models\EquipmentmapSearch;
use backend\modules\minstructions\models\Minstructions;
use backend\modules\minstructions\models\MinstructionsSearch;
use backend\modules\mprapprovals\models\Mprapprovals;
use backend\modules\mprapprovals\models\MprapprovalsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * MprdefinitionController implements the CRUD actions for Mprdefination model.
 */
class MprdefinitionController extends Controller
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
								   return CommonFunctions::isAccessible('RM_MPR');
								}
                            ],
                            // everything else is denied
                        ],
                    ], 
        ];
    }

    /**
     * Lists all Mprdefination models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of MPR Definitions',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		CommonFunctions::unlockTheRecord();
		
        $searchModel = new MprdefinitionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on MPR Definitions',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new MprdefinitionSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of MPR Definitions',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new MprdefinitionSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Mprdefination model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id,$tab='')
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$model = $this->findModel($id);
		if(!CommonFunctions::lockThisRecord($id,$model,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
		
		switch($tab){
			case 'coverpage':
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Cover_Page'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed cover page of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'billofmaterials':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed bill of materials of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'formulation':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed formulation of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'equipments':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Equipments'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed equipments of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'manufacturingInst':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Manufacturing_Instructions'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed manufacturing instructions of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			case 'mprApprovals':
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Approvals'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed approvals of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;
			default:
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Cover_Page'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed cover page of MPR Definition ID:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				break;	
		}
		$searchModel = new BillofmaterialSearch();
		$searchModel->mpr_defination_id_fk = $id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		
		$formulasearchModel = new FormulationSearch();
		$formulasearchModel->mpr_defination_id_fk = $id;
        $formuladataProvider = $formulasearchModel->search(Yii::$app->request->queryParams);
		
		$eqpsearchModel = new EquipmentmapSearch();
		$eqpsearchModel->mpr_defination_id_fk = $id;
        $eqpdataProvider = $eqpsearchModel->search(Yii::$app->request->queryParams);
		
		$minstsearchModel = new MinstructionsSearch();
		$minstsearchModel->mpr_defination_id_fk = $id;
        $minstdataProvider = $minstsearchModel->search(Yii::$app->request->queryParams);
		
		$mapprsearchModel = new MprapprovalsSearch();
		$mapprsearchModel->mpr_defination_id_fk = $id;
        $mapprdataProvider = $mapprsearchModel->search(Yii::$app->request->queryParams);
	
        return $this->render('view', [
            'model' => $model,
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			'activetab' => $tab,
			'eqpsearchModel' => $eqpsearchModel,
			'eqpdataProvider' => $eqpdataProvider,
			'minstsearchModel' => $minstsearchModel,
			'minstdataProvider' => $minstdataProvider,
			'mapprsearchModel' => $mapprsearchModel,
			'mapprdataProvider' => $mapprdataProvider,
			'formulasearchModel' => $formulasearchModel,
			'formuladataProvider' => $formuladataProvider,
        ]);
    }

    /**
     * Creates a new Mprdefination model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
        $model = new Mprdefination();
		$mytoken = time();
		
		$request = Yii::$app->request;
		$mpr_defination_id_pk = intval($request->post('mpr_defination_id_pk',0));
		$product_code = intval($request->post('product_code',0));
		$product_code_hid = trim($request->post('product_code_hid',''));
		$product_part = trim($request->post('product_part',''));
		$author = intval($request->post('author',0));
		$product_name = trim($request->post('product_name',''));
		$formulation_id = trim($request->post('formulation_id',''));
		$product_strength = trim($request->post('product_strength',''));
		$batch_size = trim($request->post('batch_size',''));
		$MPR_unit_id = intval($request->post('MPR_unit_id',0));
		$theoritical_yield = trim($request->post('theoritical_yield',''));
		$gmp_company = intval($request->post('gmp_company',0));
		$purpose = trim($request->post('purpose',''));
		$scope = trim($request->post('scope',''));
		$mytoken = trim($request->post('mytoken',''));

        if(isset($_POST) && strlen($product_part) > 0)
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_mpr_defination WRITE,bpr_mpr_approval WRITE, activity_log WRITE")->query();
			$is_MPR_exists = checkCommonFunctions::check_if_MPR_exists_with_same($model,$mpr_defination_id_pk,$product_code,$product_code_hid,$product_part,$author,$product_name,$formulation_id,$product_strength,$batch_size,$MPR_unit_id,$theoritical_yield,$gmp_company,$purpose,$scope,$mytoken);
		
			if($is_MPR_exists=="No")
			{
				$mprVer = CommonFunctions::generateMPRVersionNo($product_code_hid);
				if($mprVer>0)
				{
					$command = Yii::$app->db->createCommand()
						->insert('bpr_mpr_defination', [
						'product_id_fk' =>$product_code,
						'MPR_version'=> $mprVer,
						'product_code' => $product_code_hid,
						'product_part' => $product_part,
						'author' => $author,
						'product_name' => $product_name,
						'formulation_id' => $formulation_id,
						'product_strength' => $product_strength,
						'batch_size'=> $batch_size,
						'MPR_unit_id' => $MPR_unit_id,
						'theoritical_yield' => $theoritical_yield,
						'company_id_fk' => $gmp_company,
						'purpose' => $purpose,
						'scope' => $scope,
						'isDeleted' => '0',
						'addedby_person_id_fk' => Yii::$app->user->id,
						'created_datetime' => date("Y-m-d H:i:s"),		
						'mpr_token' => $mytoken,	
						'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,			
					])->execute();
					
					$id = Yii::$app->db->getLastInsertID();
					$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$id;
					
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ADD'],'Added MPR Definition with product code:"'.$product_code_hid.'"',$addUrl);
				}else{
					$session = Yii::$app->session;
					$session->set('mprversionError', "MPR version limit reached.You can't add more than 10 versions of same product.");
				}
			}
			else
			{
				$session = Yii::$app->session;
				$session->set('mprversionError', "You can't add versions of same product without approval of MPR.");
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add MPR Definition screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
				'mytoken' => $mytoken,
			]);
		} 
    }
	
	public function actionAddcopy($id)
    {		
		$eflag = checkCommonFunctions::checkIfEquipmentExpiredOrDeleted($id);
		$pflag = checkCommonFunctions::checkIfProductDeletedofMPR($id);
		if($pflag=="Yes")
		{
			$session = Yii::$app->session;
			$session->set('mprversionError', "You can't copy this MPR because it contains deleted product.");
			return $this->redirect(['index']);
		}	
		else if($eflag=="Yes")
		{
			$session = Yii::$app->session;
			$session->set('mprversionError', "You can't copy this MPR because it contains expired equipment.");
			return $this->redirect(['index']);
		}		
				
		$prev_mpr_def_id = $id;
		$new_mpr_def_id = 0;
		
     	$mprDefinition = \backend\modules\mprdefinition\models\Mprdefination::find()
				->where(['bpr_mpr_defination.mpr_defination_id_pk' => $id])
				->one();
		if($mprDefinition->product_code)
		{
			$mprVer = CommonFunctions::generateMPRVersionNo($mprDefinition->product_code);
			if($mprVer>0)
			{
				$command = Yii::$app->db->createCommand()
						->insert('bpr_mpr_defination', [
						'product_id_fk' => $mprDefinition->product_id_fk,
						'MPR_version'=> $mprVer,
						'product_code' => $mprDefinition->product_code,
						'product_part' => $mprDefinition->product_part,
						'author' => $mprDefinition->author,
						'product_name' => $mprDefinition->product_name,
						'formulation_id' => $mprDefinition->formulation_id,
						'product_strength' => $mprDefinition->product_strength,
						'batch_size'=> $mprDefinition->batch_size,
						'MPR_unit_id' => $mprDefinition->MPR_unit_id,
						'theoritical_yield' => $mprDefinition->theoritical_yield,
						'company_id_fk' => $mprDefinition->company_id_fk,
						'purpose' => $mprDefinition->purpose,
						'scope' => $mprDefinition->scope,
						'isDeleted' => '0',
						'addedby_person_id_fk' => Yii::$app->user->id,
						'created_datetime' => date("Y-m-d H:i:s"),			
						'isCopied' => 1,
						'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,	
					])->execute();	
					$mprdefid = Yii::$app->db->getLastInsertID();
					$new_mpr_def_id = $mprdefid; 
					if($mprdefid)
					{				
						CommonFunctions::copyBillofMaterials($prev_mpr_def_id,$new_mpr_def_id);
						CommonFunctions::copyFormulations($prev_mpr_def_id,$new_mpr_def_id);
						CommonFunctions::copyEquipmentsMap($prev_mpr_def_id,$new_mpr_def_id);
						CommonFunctions::copyManufacturingInstructions($prev_mpr_def_id,$new_mpr_def_id);
						
						$session = Yii::$app->session;
						$session->set('successMPRCopy', 'MPR definition copied successfully.');
					}
					
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprdefid;
				
					ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ADD'],'Added copy of MPR Definition of ID:"'.$id.'"',$addUrl);
			}else{
				$session = Yii::$app->session;
				$session->set('mprversionError', "MPR version limit reached. You can't add more than 10 versions of same product.");
			}
		}	
		return $this->redirect(['index']);
	}

	public function actionProductdetails($id)
	{
		$product = \backend\modules\product\models\Product::find()
				->where(['bpr_product.product_id_pk' => $id, 'bpr_product.isDeleted' => '0'])
				->one();
				
		$output = array('product_name'=>$product->name,'product_part'=>$product->part,'product_code'=>$product->code);
		return json_encode($output);
	}
    /**
     * Updates an existing Mprdefination model.
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
		$prevProduct = $model->product_id_fk;
		$newProduct = '';
		$mytoken = time();
		
		$request = Yii::$app->request;
		$mpr_defination_id_pk = intval($request->post('mpr_defination_id_pk',0));
		$product_code = intval($request->post('product_code',0));
		$product_code_hid = trim($request->post('product_code_hid',''));
		$product_part = trim($request->post('product_part',''));
		$author = intval($request->post('author',0));
		$product_name = trim($request->post('product_name',''));
		$formulation_id = trim($request->post('formulation_id',''));
		$product_strength = trim($request->post('product_strength',''));
		$batch_size = trim($request->post('batch_size',''));
		$MPR_unit_id = intval($request->post('MPR_unit_id',0));
		$theoritical_yield = trim($request->post('theoritical_yield',''));
		$gmp_company = intval($request->post('gmp_company',0));
		$purpose = trim($request->post('purpose',''));
		$scope = trim($request->post('scope',''));
		$mytoken = trim($request->post('mytoken',''));

       	if(isset($_POST) && strlen($product_part) > 0)
		{
			if(isset($_POST['product_code']) && $_POST['product_code']!='')
				$newProduct = $_POST['product_code'];
			else
				$newProduct =  $model->product_id_fk;
				
			if($newProduct!=$prevProduct)
				$mprVer = CommonFunctions::generateMPRVersionNo($product_code_hid);	
			else
				$mprVer = $model->MPR_version;
			
			if($mprVer>0)
			{
				$command = Yii::$app->db->createCommand()
					->update('bpr_mpr_defination', [
					'product_id_fk' => $newProduct,
					'MPR_version'=> $mprVer,
					'product_code' => $product_code_hid,
					'product_part' => $product_part,
					'author' => $author,
					'product_name' => $product_name,
					'formulation_id' => $formulation_id,
					'product_strength' => $product_strength,
					'batch_size'=> $batch_size,
					'MPR_unit_id' => $MPR_unit_id,
					'theoritical_yield' => $theoritical_yield,
					'company_id_fk' => $gmp_company,
					'purpose' => $purpose,
					'scope' => $scope,
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),				
				],"mpr_defination_id_pk='".$id."'")->execute();	
				
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$id;
				
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated MPR Definition record of id:"'.$id.'"',$addUrl);
			}else{
				$session = Yii::$app->session;
				$session->set('mprversionError', "MPR version limit reached.You can't add more than 10 versions of same product.");
			}	
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of MPR Definition id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('update', [
				'model' => $model,
				'mytoken' => $mytoken,
			]);
		}
    }

    /**
     * Deletes an existing Mprdefination model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id, $delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($id);
		if($rflag=='Yes')
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', "This MPR is already approved, so can't delete it.");
		}
		else
		{ 
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			 
			$command = Yii::$app->db->createCommand()
							->update('bpr_mpr_defination', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'mpr_defination_id_pk='.$id)
							->execute();
							
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Record deleted successfully.');
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
        return $this->redirect(['index']);
    }

	public function actionRestore($id, $delreason)
    {
		if(strlen(trim($delreason)) > 0){
		$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$id;
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
		
		$model = $this->findModel($id);
		
		$command = Yii::$app->db->createCommand()
						->update('bpr_mpr_defination', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'mpr_defination_id_pk='.$id)
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
     * Finds the Mprdefination model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Mprdefination the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Mprdefination::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
