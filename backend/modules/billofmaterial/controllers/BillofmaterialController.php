<?php

namespace backend\modules\billofmaterial\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\billofmaterial\models\Billofmaterial;
use backend\modules\billofmaterial\models\BillofmaterialSearch;
use backend\modules\billofmaterial\models\BillofmaterialSearchDel;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\bprrecords\models\Bprrecords;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * BillofmaterialController implements the CRUD actions for Billofmaterial model.
 */
class BillofmaterialController extends Controller
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
								   return CommonFunctions::isAccessibleMPRorBPR('RM_MPR', 'RM_BPR');
								}
                            ],
                            // everything else is denied
                        ],
                    ], 
        ];
    }

    /**
     * Lists all Billofmaterial models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of bill of materials',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
        $searchModel = new BillofmaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

	public function actionDeletedlist($mprid)
	{	
	   $mprDef = Mprdefination::findOne($mprid);
		   
	   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
	   }
		   
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of bill of materials',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new BillofmaterialSearchDel();
		$searchModelDel->mpr_defination_id_fk = $mprid;
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Billofmaterial model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
		$mode = (isset($_REQUEST['mode']))?$_REQUEST['mode']:'';
		if($mode=='bpr')
		{
		   $bprid = intval(isset($_REQUEST['bprid'])?$_REQUEST['bprid']:0);
		   $bprDet = Bprrecords::findOne($bprid);
		   if(!CommonFunctions::lockThisRecord($bprid,$bprDet,'bpr_batch_processing_records','bpr_id_pk'))
		   {
				$session = Yii::$app->session;
				$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
				header('Location:'.Yii::$app->homeUrl."bprrecords/bprrecords");
				exit;
		   }
	   
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['BPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of bill of material id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		}
		else
		{
		   $mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
		   $mprDef = Mprdefination::findOne($mprid);
		   
		   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		   {
				$session = Yii::$app->session;
				$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
				header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
				exit;
		   }
		   
		   ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of bill of material id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		   
		}
		
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


	
	public function actionBomunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action',''));
		try{
			switch($action){
			case 'materialName':
				$material_name = trim($request->post('material_name',''));
				$edit_id = intval($request->post('edit_id'));
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk'));
				$condition = '';
				if(strlen($material_name) > 0){
					$count = 0;			
					if($edit_id > 0){
						$count = Billofmaterial::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_name' => trim($material_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("bom_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Billofmaterial::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_name' => trim($material_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}
			break;
		case 'materialId':
			$material_id = trim($request->post('material_id',''));
			$edit_id = intval($request->post('edit_id'));
			$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk'));
			$condition = '';
			if(strlen($material_id) > 0){
				$count = 0;			
				if($edit_id > 0){
					$count = Billofmaterial::find()->where([
									'mpr_defination_id_fk' => $mpr_defination_id_fk,
									'material_id' => trim($material_id),
									'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
									])->andWhere("bom_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
				}else{
					$count = Billofmaterial::find()->where([
									'mpr_defination_id_fk' => $mpr_defination_id_fk,
									'material_id' => trim($material_id),
									'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
									])->count();
				}
				return ($count > 0?"false":"true");
			}
			break;
		}
		}catch(Exception $e){ return false; }
	}
	
    /**
     * Creates a new Billofmaterial model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {		
       $model = new Billofmaterial();
	   
	   $mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
	   $mprDef = Mprdefination::findOne($mprid);
	   
	   if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
	   
	   $request = Yii::$app->request;
	   $bom_id_pk = intval($request->post('bom_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   
	   $material_name = trim($request->post('material_name',''));
	   $material_id = trim($request->post('material_id',''));
	   $material_type_id_fk = intval($request->post('material_type_id_fk',0));
	   $product_part = trim($request->post('product_part',''));
	   $vendor_id = trim($request->post('vendor_id',''));
	   $vendor_name = trim($request->post('vendor_name',''));
	   $vendor_lot = trim($request->post('vendor_lot',''));
	   
	   $qty_branch = trim($request->post('qty_branch',''));
	   $price_per_unit = floatval($request->post('price_per_unit',0));
	   $qb_unit_id_fk = intval($request->post('qb_unit_id_fk',0));
	   $maximum_qty = intval($request->post('maximum_qty',0));
	   $composition = trim($request->post('composition',''));
	   $com_unit_id_fk = intval($request->post('com_unit_id_fk',0));
	   
	   $storage_condition = trim($request->post('storage_condition',''));
	   $temperature_condition = trim($request->post('temperature_condition',''));
	   
	   $gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
	   $total_shelf_life = trim($request->post('total_shelf_life',''));
	   $material_test_status = trim($request->post('material_test_status',''));
	   
	   $CAS_Number = trim($request->post('CAS_Number',''));
	   $Control_Number = trim($request->post('Control_Number',''));
	   
	   $environmental_protection_agency = intval($request->post('environmental_protection_agency',0));
	   
	   $rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		
       if(isset($_POST) && strlen($material_name) > 0 && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_bill_of_material WRITE,activity_log WRITE")->query();
			
			//$is_BOM_exists = checkCommonFunctions::check_if_BOM_exists_in_MPR($model,$material_name,$bom_id_pk,$mpr_defination_id_fk);
			
			$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
                    'material_name' => trim($material_name),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
			$is_BOM_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			if($is_BOM_exists=="No"){
				$target_name1 = ''; $uploadedFile1= '';
				if(isset($_FILES['material_safety_data_sheet']["name"]) && $_FILES['material_safety_data_sheet']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/billofmaterials/';
					
					$material_safety_data_sheet = $_FILES['material_safety_data_sheet']["name"];
					$ext1 = pathinfo($material_safety_data_sheet,PATHINFO_EXTENSION);
					
					$souce_file_name1 = $_FILES['material_safety_data_sheet']["tmp_name"];
					$target_name1 = time()."_m.".$ext1;
					$target_file1 = $folderPath . basename($target_name1);
					$uploadSuccess1 = move_uploaded_file($souce_file_name1,$target_file1);
					
					if($uploadSuccess1)
						$uploadedFile1 = $target_name1;
					else 
						$uploadedFile1 = '';
				}
			
				$target_name2 = ''; $uploadedFile2= '';
				if(isset($_FILES['select_a_file']["name"]) && $_FILES['select_a_file']["name"]!=''){
					$folderPath = Yii::$app->basePath.'/web/uploads/billofmaterials/';
					
					$select_a_file = $_FILES['select_a_file']["name"];
					$ext2 = pathinfo($select_a_file,PATHINFO_EXTENSION);
					
					$souce_file_name2 = $_FILES['select_a_file']["tmp_name"];
					$target_name2 = time()."_s.".$ext2;
					$target_file2 = $folderPath . basename($target_name2);
					$uploadSuccess2 = move_uploaded_file($souce_file_name2,$target_file2);
					
					if($uploadSuccess2)
						$uploadedFile2 = $target_name2;
					else 
						$uploadedFile2 = '';
				}
			
				$command = Yii::$app->db->createCommand()
					->insert('bpr_bill_of_material', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'material_name' => trim($material_name),
					'qty_branch' => trim($qty_branch),
					'qb_unit_id_fk' => $qb_unit_id_fk,
					'composition' => trim($composition),
					'com_unit_id_fk' => $com_unit_id_fk,
					'material_id' => $material_id,
					'material_type_id_fk' => $material_type_id_fk,
					'product_part' => $product_part,
					'vendor_id' => $vendor_id,
					'vendor_name' => $vendor_name,
					'vendor_lot' => $vendor_lot,
					'price_per_unit' => $price_per_unit,
					'maximum_qty' => $maximum_qty,
					'storage_condition' => $storage_condition,
					'temperature_condition' => $temperature_condition,
					'country_id_fk' => $gmp_country_id_fk,
					'total_shelf_life' => $total_shelf_life,
					'material_test_status' => $material_test_status,
					'material_safety_data_sheet' => $uploadedFile1,
					'environmental_protection_agency' => $environmental_protection_agency,
					'select_a_file' => $uploadedFile2,
					'CAS_Number' => $CAS_Number,
					'Control_Number' => $Control_Number,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,			
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."billofmaterial/billofmaterial/view?id=".$id;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ADD'],'Added bill of material with name:"'.$material_name.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mpr_defination_id_fk."&tab=billofmaterials");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add bill of material screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
				'mprDef' => $mprDef,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Updates an existing Billofmaterial model.
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
	   
	   $PreviousMaterialDataSheet = $model->material_safety_data_sheet;
	   $PreviousFile = $model->select_a_file;
	   
	   $mprid = intval(isset($_REQUEST['mprid'])?$_REQUEST['mprid']:0);
	   $mprDef = Mprdefination::findOne($mprid);
	   
	    if(!CommonFunctions::lockThisRecord($mprid,$mprDef,'bpr_mpr_defination','mpr_defination_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."mprdefinition/mprdefinition");
			exit;
		}
	   
	   
	   $request = Yii::$app->request;
	   $bom_id_pk = intval($request->post('bom_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   
	   $material_name = trim($request->post('material_name',''));
	   $material_id = trim($request->post('material_id',''));
	   $material_type_id_fk = intval($request->post('material_type_id_fk',0));
	   $product_part = trim($request->post('product_part',''));
	   $vendor_id = trim($request->post('vendor_id',''));
	   $vendor_name = trim($request->post('vendor_name',''));
	   $vendor_lot = trim($request->post('vendor_lot',''));
	   
	   $qty_branch = trim($request->post('qty_branch',''));
	   $price_per_unit = trim($request->post('price_per_unit',''));
	   $qb_unit_id_fk = intval($request->post('qb_unit_id_fk',0));
	   $maximum_qty = intval($request->post('maximum_qty',0));
	   $composition = trim($request->post('composition',''));
	   $com_unit_id_fk = intval($request->post('com_unit_id_fk',0));
	   
	   $storage_condition = trim($request->post('storage_condition',''));
	   $temperature_condition = trim($request->post('temperature_condition',''));
	   
	   $gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
	   $total_shelf_life = trim($request->post('total_shelf_life',''));
	   $material_test_status = trim($request->post('material_test_status',''));
	   
	   $CAS_Number = trim($request->post('CAS_Number',''));
	   $Control_Number = trim($request->post('Control_Number',''));
	   
	   $environmental_protection_agency = intval($request->post('environmental_protection_agency',0));
	   
	   $rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
	   
		if(isset($_POST) && strlen($material_name) > 0 && $rflag!='Yes')
		{
			$target_name1 = ''; $uploadedFile1= $PreviousMaterialDataSheet;
			if(isset($_FILES['material_safety_data_sheet']["name"]) && $_FILES['material_safety_data_sheet']["name"]!=''){
				$folderPath = Yii::$app->basePath.'/web/uploads/billofmaterials/';
				
				$material_safety_data_sheet = $_FILES['material_safety_data_sheet']["name"];
				$ext1 = pathinfo($material_safety_data_sheet,PATHINFO_EXTENSION);
				
				$souce_file_name1 = $_FILES['material_safety_data_sheet']["tmp_name"];
				$target_name1 = time()."_m.".$ext1;
				$target_file1 = $folderPath . basename($target_name1);
				$uploadSuccess1 = move_uploaded_file($souce_file_name1,$target_file1);
				
				if($uploadSuccess1)
					$uploadedFile1 = $target_name1;
				else 
					$uploadedFile1 = '';
			}
		
			$target_name2 = ''; $uploadedFile2= $PreviousFile;
			if(isset($_FILES['select_a_file']["name"]) && $_FILES['select_a_file']["name"]!=''){
				$folderPath = Yii::$app->basePath.'/web/uploads/billofmaterials/';
				
				$select_a_file = $_FILES['select_a_file']["name"];
				$ext2 = pathinfo($select_a_file,PATHINFO_EXTENSION);
				
				$souce_file_name2 = $_FILES['select_a_file']["tmp_name"];
				$target_name2 = time()."_s.".$ext2;
				$target_file2 = $folderPath . basename($target_name2);
				$uploadSuccess2 = move_uploaded_file($souce_file_name2,$target_file2);
				
				if($uploadSuccess2)
					$uploadedFile2 = $target_name2;
				else 
					$uploadedFile2 = '';
			}
			
			
			$command = Yii::$app->db->createCommand()
				->update('bpr_bill_of_material', [
				'material_name' => trim($material_name),
				'qty_branch' => trim($qty_branch),
				'qb_unit_id_fk' => $qb_unit_id_fk,
				'composition' => trim($composition),
				'com_unit_id_fk' => $com_unit_id_fk,
				'material_id' => $material_id,
				'material_type_id_fk' => $material_type_id_fk,
				'product_part' => $product_part,
				'vendor_id' => $vendor_id,
				'vendor_name' => $vendor_name,
				'vendor_lot' => $vendor_lot,
				'price_per_unit' => $price_per_unit,
				'maximum_qty' => $maximum_qty,
				'storage_condition' => $storage_condition,
				'temperature_condition' => $temperature_condition,
				'country_id_fk' => $gmp_country_id_fk,
				'total_shelf_life' => $total_shelf_life,
				'material_test_status' => $material_test_status,
				'material_safety_data_sheet' => $uploadedFile1,
				'environmental_protection_agency' => $environmental_protection_agency,
				'select_a_file' => $uploadedFile2,
				'CAS_Number' => $CAS_Number,
				'Control_Number' => $Control_Number,
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],"bom_id_pk='".$id."'")->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."billofmaterial/billofmaterial/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated bill of material record of id:"'.$id.'"',$addUrl);
			
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_REQUEST['mpr_defination_id_fk']."&tab=billofmaterials");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of bill of material id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			 return $this->render('update', [
				'model' => $model,
				'mprDef' => $mprDef,
				'rflag' => $rflag,
			]);
		}
    }

    /**
     * Deletes an existing Billofmaterial model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."billofmaterial/billofmaterial/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			 
			$command = Yii::$app->db->createCommand()
							->update('bpr_bill_of_material', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'bom_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This MPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=billofmaterials");
		exit;
    }
	
	public function actionRestore($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."billofmaterial/billofmaterial/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Bill_of_Materials'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_bill_of_material', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'bom_id_pk='.$id)
							->execute();
			}else{
				$session = Yii::$app->session;
				$session->set('errorDeleteRestore', 'Please enter valid reason.');
			}
		}
		else
		{
			$session = Yii::$app->session;
			$session->set('errorDeleteRestore', 'This MPR is approved. So it is sealed & in readonly mode.');
		}
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=billofmaterials");
		exit;
    }

    /**
     * Finds the Billofmaterial model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Billofmaterial the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Billofmaterial::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
