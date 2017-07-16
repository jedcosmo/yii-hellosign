<?php

namespace backend\modules\formulation\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\formulation\models\Formulation;
use backend\modules\formulation\models\FormulationSearch;
use backend\modules\formulation\models\FormulationSearchDel;
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
class FormulationController extends Controller
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
        $searchModel = new FormulationSearch();
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
		
		$searchModelDel = new FormulationSearchDel();
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


	
	public function actionFormulationunique()
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
						$count = Formulation::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_name' => trim($material_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("f_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Formulation::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_name' => trim($material_name),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->count();
					}
					return ($count > 0?"false":"true");
				}
			break;
			case 'materialPart':
				$material_part = trim($request->post('material_part',''));
				$edit_id = intval($request->post('edit_id'));
				$mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk'));
				$condition = '';
				if(strlen($material_part) > 0){
					$count = 0;			
					if($edit_id > 0){
						$count = Formulation::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_part' => trim($material_part),
										'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
										])->andWhere("f_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
					}else{
						$count = Formulation::find()->where([
										'mpr_defination_id_fk' => $mpr_defination_id_fk,
										'material_part' => trim($material_part),
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
       $model = new Formulation();
	   
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
	   $f_id_pk = intval($request->post('f_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   
	   $material_name = trim($request->post('material_name',''));
	   $material_part = trim($request->post('material_part',''));
	   $formulation_percentage = floatval($request->post('formulation_percentage',''));
	   
	   $rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		
       if(isset($_POST) && (strlen($material_name)>0 || strlen($material_part)>0 || $formulation_percentage>0) && $rflag!='Yes')
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_formulation WRITE,activity_log WRITE")->query();
			
			$fieldArray = array(
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
                    'material_name' => trim($material_name),
					'material_part' => trim($material_part),
					'formulation_percentage' => floatval($formulation_percentage),
                    'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk
                    );
		
			$is_BOM_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			
			if($is_BOM_exists=="No"){
			
				$command = Yii::$app->db->createCommand()
					->insert('bpr_formulation', [
					'mpr_defination_id_fk' => $mpr_defination_id_fk,
					'material_name' => trim($material_name),
					'material_part' => trim($material_part),
					'formulation_percentage' => $formulation_percentage,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,			
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."formulation/formulation/view?id=".$id;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['ADD'],'Added formulation with Ingredient:"'.$material_name.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=formulation");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add formulation screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
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
	   $f_id_pk = intval($request->post('f_id_pk',0));
	   $mpr_defination_id_fk = intval($request->post('mpr_defination_id_fk',0));
	   
	   $material_name = trim($request->post('material_name',''));
	   $material_part = trim($request->post('material_part',''));
	   $formulation_percentage = trim($request->post('formulation_percentage',''));
	   
	   $rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
	   
		if(isset($_POST) && (strlen($material_name)>0 || strlen($material_part)>0 || strlen($formulation_percentage)>0) && $rflag!='Yes')
		{
			
			$command = Yii::$app->db->createCommand()
				->update('bpr_formulation', [
				'material_name' => trim($material_name),
				'material_part' => trim($material_part),
				'formulation_percentage' => $formulation_percentage,			
				'addedby_person_id_fk' => Yii::$app->user->id,
				'created_datetime' => date("Y-m-d H:i:s"),				
			],"f_id_pk='".$id."'")->execute();
			
			$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."formulation/formulation/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated formulation record of id:"'.$id.'"',$addUrl);
			
			header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$_REQUEST['mpr_defination_id_fk']."&tab=formulation");
			exit;
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of formulation id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
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
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."formulation/formulation/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			 
			$command = Yii::$app->db->createCommand()
							->update('bpr_formulation', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted'=>$delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'f_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=formulation");
		exit;
    }
	
	public function actionRestore($id,$mprid,$delreason)
    {
		$rflag = checkCommonFunctions::check_If_MPR_Approved($mprid);
		if($rflag!='Yes')
		{
			if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."formulation/formulation/view?id=".$id;
			
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MPR_Formulation'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_formulation', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'f_id_pk='.$id)
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
		header("Location:".Yii::$app->homeUrl."mprdefinition/mprdefinition/view?id=".$mprid."&tab=formulation");
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
        if (($model = Formulation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
