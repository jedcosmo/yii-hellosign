<?php

namespace backend\modules\person\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\person\models\Person;
use backend\modules\person\models\PersonSearch;
use backend\modules\person\models\PersonSearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\Request;

/**
 * PersonController implements the CRUD actions for Person model.
 */
class PersonController extends Controller
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
								   return CommonFunctions::isAccessible('RM_Person');
								}
                            ],
                            // everything else is denied
                        ],
                    ],  
        ];
    }

    /**
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    {
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed listing page of persons',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
	public function actionExcel()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['EXPORT'],'Performed export on persons',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModel = new PersonSearch();
        $models = $searchModel->searchExcel(Yii::$app->request->queryParams);
		
		return $this->renderPartial('excel', [
			'models' => $models,
			'searchModel' => $searchModel,
		]);
	}

	public function actionDeletedlist()
	{
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed deleted records of persons',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
		
		$searchModelDel = new PersonSearchDel();
        $dataProviderDel = $searchModelDel->search(Yii::$app->request->queryParams);

        return $this->render('deletedlist', [
            'searchModel' => $searchModelDel,
            'dataProvider' => $dataProviderDel,
        ]);
	}
    /**
     * Displays a single Person model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
		if(!CommonFunctions::checkPersonCompanyAccess($this->findModel($id)))
		{	
			throw new HttpException(403,"You don't have access to perform this action.");
		}
		
	   $model = Person::findOne($id);
		   
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person','person_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."person/person");
			exit;
	   }
		
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed details of person id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
					
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionEmailunique()
	{
		$request = Yii::$app->request;
		$action = trim($request->post('action'));
		
		switch($action){
			case "check_duplicate_email":
				$emailid = trim($request->post('gmp_emailid',''));
				$edit_id = intval($request->post('edit_id',0));
				$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',0));
				$condition = '';
				if(strlen($emailid) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Person::find()->where('(emailid="'.trim($emailid).'" OR new_emailid="'.trim($emailid).'")')->andWhere("person_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
						/*$count = Person::find()->where([
										'emailid' => trim($emailid),
										])->andWhere("person_id_pk != :edit_id",[':edit_id' => $edit_id])->count();*/
					}else{
						$count = Person::find()->where('(emailid="'.trim($emailid).'" OR new_emailid="'.trim($emailid).'")')->count();
						/*$count = Person::find()->where([
										'emailid' => trim($emailid),
										])->count();*/
					}
					
					return ($count > 0?"false":"true");
				
				}
			break;
			case "check_duplicate_username":
				$emailid = trim($request->post('gmp_user_name_person',''));
				$edit_id = intval($request->post('edit_id',0));
				$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',0));
				$condition = '';
				if(strlen($emailid) > 0){
					$count = 0;
					if($edit_id > 0){
						$count = Person::find()->where('(user_name_person="'.trim($emailid).'" OR new_username="'.trim($emailid).'")')->andWhere("person_id_pk != :edit_id",[':edit_id' => $edit_id])->count();
						/*$count = Person::find()->where([
										'user_name_person' => trim($emailid),
										])->andWhere("person_id_pk != :edit_id",[':edit_id' => $edit_id])->count();*/
					}else{
						$count = Person::find()->where('(user_name_person="'.trim($emailid).'" OR new_username="'.trim($emailid).'")')->count();
						/*$count = Person::find()->where([
										'user_name_person' => trim($emailid),
										])->count();*/
					}
					
					return ($count > 0?"false":"true");
				}
			break;
		}
	}

	public function actionZipcodelookup()
	{
		$model = new Person();
		
		$zipcode = isset($_REQUEST['gmp_zip_pincode'])?$_REQUEST['gmp_zip_pincode']:''; 
		$zipcode = urlencode($zipcode);
		$gmp_city_id_fk = isset($_REQUEST['gmp_city_id_fk'])?$_REQUEST['gmp_city_id_fk']:0;
		$gmp_state_id_fk = isset($_REQUEST['gmp_state_id_fk'])?$_REQUEST['gmp_state_id_fk']:0;
		
		$cityName = $model->showCityName($gmp_city_id_fk);
		$stateName = $model->showStateName($gmp_state_id_fk);
		if($cityName!='' && $stateName!='' && $zipcode!='')
		{
			$ch = curl_init("http://maps.googleapis.com/maps/api/geocode/json?address=".$zipcode."&sensor=true");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);      
			curl_close($ch);
			
			$allAddress = array();
			$longname = array();
			$myflag = 'false';
			$outputarr = json_decode($output);
			if(is_object($outputarr) && $outputarr->status=='OK')
			{
				if(is_array($outputarr->results) && count($outputarr->results)>0)
				{
					$m = 0;
					foreach($outputarr->results as $key=>$val)
					{
						if(is_array($val->address_components) && count($val->address_components)>0)
						{
							foreach($val->address_components as $kkc => $vvc)
							{
								$longname [] = $vvc->long_name;
								if($vvc->types[0] == 'locality')
									$allAddress[$m]['locality'] = $vvc->long_name;
								if($vvc->types[0] == 'administrative_area_level_2')
									$allAddress[$m]['city'] = $vvc->long_name;
								if($vvc->types[0] == 'administrative_area_level_1')
									$allAddress[$m]['state'] = $vvc->long_name;
							}
							$m++;
						}
					}
					if(is_array($longname) && count($longname)>0)
					{
						if(in_array($cityName,$longname))
							$myflag = 'true';
					}
				}
			}
			echo $myflag;
			exit;
		}
		else
		{
			echo 'true';
			exit;
		}
	}

    /**
     * Creates a new Person model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
				
        $model = new Person();
		$request = Yii::$app->request;
		$gmp_first_name = trim($request->post('gmp_first_name',''));
		$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',0));
		$gmp_emailid = trim($request->post('gmp_emailid',''));
		$gmp_person_id_pk = intval($request->post('gmp_person_id_pk',0));
		$gmp_user_name_person = trim($request->post('gmp_user_name_person',''));
		$gmp_password_person = trim($request->post('gmp_password_person',''));
		$gmp_last_name = trim($request->post('gmp_last_name',''));
		$gmp_phone = trim($request->post('gmp_phone',''));
		$gmp_fax = trim($request->post('gmp_fax',''));
		$gmp_address = trim($request->post('gmp_address',''));
		$gmp_city_id_fk = intval($request->post('gmp_city_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_pobox = trim($request->post('gmp_pobox',''));
		$gmp_zip_pincode = trim($request->post('gmp_zip_pincode',''));
		$gmp_role_id_fk = intval($request->post('gmp_role_id_fk',0));
		$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',Yii::$app->user->identity->super_company_id_fk));
		
		if(isset($_POST) && strlen($gmp_first_name)>0)
		{
			$mis = Yii::$app->db->createCommand("LOCK TABLES bpr_person WRITE,activity_log WRITE")->query();
			
			$fieldArray = array('user_name_person' => trim($gmp_user_name_person));
			$is_person_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			$fieldArray = array('emailid' => trim($gmp_emailid));
			$is_person_username_exists = checkCommonFunctions::check_If_Record_Exists($model,$fieldArray);
			
			$getToken = rand(0, 99);
			$getTime = date("H:i:s");
			$verification_token = md5($getToken.$getTime);
			
			if($is_person_exists == "No" && $is_person_username_exists == 'No'){
				$command = Yii::$app->db->createCommand()
					->insert('bpr_person', [
					'first_name' => trim($gmp_first_name),
					'last_name' => trim($gmp_last_name),
					'phone'=> trim($gmp_phone),
					'fax' => trim($gmp_fax),
					'address' => trim($gmp_address),
					'city_id_fk' => $gmp_city_id_fk,
					'state_id_fk' => $gmp_state_id_fk,
					'country_id_fk' => $gmp_country_id_fk,
					'pobox'=> trim($gmp_pobox),
					'zip_pincode' => trim($gmp_zip_pincode),
					'emailid' => trim($gmp_emailid),
					'user_name_person' => trim($gmp_user_name_person),
					'password_person' => md5($gmp_password_person),
					'role_id_fk' => $gmp_role_id_fk,
					'isDeleted' => '0',
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),
					'super_company_id_fk' => $gmp_super_company_id_fk,
					'is_verified' => '0',
					'verification_token' => $verification_token,
					'new_emailid' => trim($gmp_emailid),
					'new_username' => trim($gmp_user_name_person),
					'new_password' => md5($gmp_password_person),
				])->execute();
				
				$id = Yii::$app->db->getLastInsertID();
				$pUserID = base64_encode($id);
				/*----Send verification email to person------------------------*/
				$toEmail = trim($gmp_emailid);
				$subject = "Account verification";
				$emailContent = "Hello ".trim($gmp_first_name)." ".trim($gmp_last_name).",<br/><br/>Your account created successfully on ".Yii::$app->name.". Below are your account details:<br/>Email: ".trim($gmp_emailid)."<br/>Username: ".trim($gmp_user_name_person)."<br/>Please click on below link to verify your account.<br/><br/><a href='".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."'>".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."</a><br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
				CommonFunctions::sendAccountUpdateEmail($subject,$emailContent,$toEmail);
				/*-------------------------------------------------------------------*/

				
				$addUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."person/person/view?id=".$id;
				
				ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ADD'],'Added person with username:"'.$gmp_user_name_person.'"',$addUrl);
			}
			$mis = Yii::$app->db->createCommand("UNLOCK TABLES")->query();
			$session = Yii::$app->session;
			$session->set('successDeleteRestore', 'Person added successfully, before login user need to verify his account. Please check user emails for same.');
			return $this->redirect(['index']);
		}else{ 
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed add person screen',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			 return $this->render('create', [
				'model' => $model,
			]);
		} 
    }

	
    /**
     * Updates an existing Person model.
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
		
		if(checkCommonFunctions::check_if_role_is_administrator($model->role_id_fk)=='Yes')
		{	
			throw new HttpException(403,"You are not authorized to perform this action.");
		}
		   
		if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person','person_id_pk'))
		{
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."person/person");
			exit;
		}
		
		$emailFlag = false; 
		$request = Yii::$app->request;
		$gmp_first_name = trim($request->post('gmp_first_name',''));
		$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',0));
		$gmp_emailid = trim($request->post('gmp_emailid',''));
		$gmp_person_id_pk = intval($request->post('gmp_person_id_pk',0));
		$gmp_user_name_person = trim($request->post('gmp_user_name_person',''));
		$gmp_password_person = trim($request->post('gmp_password_person',''));
		$gmp_last_name = trim($request->post('gmp_last_name',''));
		$gmp_phone = trim($request->post('gmp_phone',''));
		$gmp_fax = trim($request->post('gmp_fax',''));
		$gmp_address = trim($request->post('gmp_address',''));
		$gmp_city_id_fk = intval($request->post('gmp_city_id_fk',0));
		$gmp_state_id_fk = intval($request->post('gmp_state_id_fk',0));
		$gmp_country_id_fk = intval($request->post('gmp_country_id_fk',0));
		$gmp_pobox = trim($request->post('gmp_pobox',''));
		$gmp_zip_pincode = trim($request->post('gmp_zip_pincode',''));
		$gmp_role_id_fk = intval($request->post('gmp_role_id_fk',0));
		$gmp_super_company_id_fk = intval($request->post('gmp_super_company_id_fk',Yii::$app->user->identity->super_company_id_fk));
       	$frmtoken = trim($request->post('frmtoken',''));
		
     	if(isset($_POST) && strlen($gmp_first_name)>0)
		{
			$personid = $id;
			if($model->frmtoken!=$frmtoken){
			
			if($model->person_id_pk!=Yii::$app->user->id)
				$roleid = $gmp_role_id_fk;
			else
				$roleid = $model->role_id_fk;
			$recordUpdateArr = array();	
			$recordUpdateArr = array('first_name' => trim($gmp_first_name),
					'last_name' => trim($gmp_last_name),
					'phone'=> trim($gmp_phone),
					'fax' => trim($gmp_fax),
					'address' => trim($gmp_address),
					'city_id_fk' => $gmp_city_id_fk,
					'state_id_fk' => $gmp_state_id_fk,
					'country_id_fk' => $gmp_country_id_fk,
					'pobox'=> trim($gmp_pobox),
					'zip_pincode' => trim($gmp_zip_pincode),
					'emailid' => trim($model->emailid),
					'user_name_person' => trim($model->user_name_person),
					'role_id_fk' => $roleid,
					'addedby_person_id_fk' => Yii::$app->user->id,
					'created_datetime' => date("Y-m-d H:i:s"),	
					'super_company_id_fk' => $gmp_super_company_id_fk,
					'frmtoken' => $frmtoken );

			$getToken = rand(0, 99);
			$getTime = date("H:i:s");
			$verification_token = md5($getToken.$getTime);
			
			$emMsg = "";
			/*-----Check if email changed--------------------------------------------*/
			if($model->emailid != trim($gmp_emailid))
			{
				$recordUpdateArr['new_emailid'] = trim($gmp_emailid);
				$recordUpdateArr['verification_token'] = $verification_token;
				$emailFlag = true;
				$emMsg .= "Email : ".trim($gmp_emailid)."<br/>";
				/*----Send notification email to old email id------------------------*/
				$toEmail = $model->emailid;
				$subject = "Account email updated";
				$emailContent = "Hello ".trim($gmp_first_name)." ".trim($gmp_last_name).",<br/><br/> Your account emailid is changed from ".$model->emailid." to ".trim($gmp_emailid)." <br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
				CommonFunctions::sendAccountUpdateEmail($subject,$emailContent,$toEmail);
				/*-------------------------------------------------------------------*/
			}
			/*-----Check if username changed-----------------------------------------*/
			if($model->user_name_person != trim($gmp_user_name_person))
			{
				$recordUpdateArr['new_username'] = trim($gmp_user_name_person);
				$recordUpdateArr['verification_token'] = $verification_token;
				$emailFlag = true;
				$emMsg .= "Username : ".trim($gmp_user_name_person)."<br/>";
			}
			/*-----Check if password changed--------------------------------------------*/
			if(strlen($gmp_password_person)>0)
			{
				$emailFlag = true;
				$recordUpdateArr['new_password'] = md5($gmp_password_person);
				$recordUpdateArr['verification_token'] = $verification_token;
			}
			
			/*----Update Persons Data into DB-------------------------------------------*/
			$command = Yii::$app->db->createCommand()->update('bpr_person', $recordUpdateArr, "person_id_pk='".$personid."'")->execute();
			
			/*-----Send email to verify account update/changes---------------------------*/
			if($emailFlag == true)
			{
				$toEmail = trim($gmp_emailid);
				$subject = "Account details updated";
				$emailContent = "Hello ".trim($gmp_first_name)." ".trim($gmp_last_name).",<br/><br/> Your account credentials are changed.<br/>".$emMsg." Please click on below link to activate your changes.<br/><br/><a href='".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."'>".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."</a><br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
				CommonFunctions::sendAccountUpdateEmail($subject,$emailContent,$toEmail);
			}
			/*-------------------------------------------------------------------------*/
			$updtUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."person/person/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated person record of id:"'.$id.'"',$updtUrl);
			}
			if($model->emailid != trim($gmp_emailid) || $model->user_name_person != trim($gmp_user_name_person) || strlen($gmp_password_person)>0)
			{
				$session = Yii::$app->session;
				$session->set('successDeleteRestore', 'Record updated successfully, changes will reflect after confirmation by user through email.');
			}
			return $this->redirect(['index']);
		}else{
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['ACCESS'],'Accessed update screen of person id:"'.$id.'"',"http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
    }
	
	public function actionSendverificationemail($id)
	{
		$model = Person::findOne($id);
		
		$getToken = rand(0, 99);
		$getTime = date("H:i:s");
		$verification_token = md5($getToken.$getTime);
		
		$command = Yii::$app->db->createCommand()
						->update('bpr_person', [
						'verification_token' => $verification_token,
					],"person_id_pk='".$id."'")->execute();
					
		/*---Resend verification email to person -------*/
		$toEmail = trim($model->emailid);
		$subject = "Account verification";
		$emailContent = "Hello ".$model->first_name." ".$model->last_name.",<br/><br/>Your ".Yii::$app->name." account details are as below:<br/>Email: ".$model->emailid."<br/>Username: ".$model->user_name_person."<br/>Please click on below link to verify your account.<br/><br/><a href='".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."'>".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."</a><br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
		CommonFunctions::sendAccountUpdateEmail($subject,$emailContent,$toEmail);	
		/*-------------------------------------------*/	

		$session = Yii::$app->session;
		$session->set('successDeleteRestore', 'Account verification email sent successfully.');
		return $this->redirect(['index']);
	}

    /**
     * Deletes an existing Person model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id,$delreason)
    {
	   $model = Person::findOne($id);
		   
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person','person_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."person/person");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."person/person/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['DELETE'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_person', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'1','reasonIsDeleted' => $delreason, 'isRestored' => '0', 'deleted_datetime' => date("Y-m-d H:i:s")], 'person_id_pk='.$id)
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
	   $model = Person::findOne($id);
		   
	   if(!CommonFunctions::lockThisRecord($id,$model,'bpr_person','person_id_pk'))
	   {
			$session = Yii::$app->session;
			$session->set('LockError', 'This record is locked for you, because another user is accessing same record.');
			header('Location:'.Yii::$app->homeUrl."person/person");
			exit;
	   }
	   
		if(strlen(trim($delreason)) > 0){
			$delUrl = "http://".$_SERVER['HTTP_HOST'].Yii::$app->homeUrl."person/person/view?id=".$id;
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Person'],Yii::$app->params['audit_log_action']['RESTORED'],$delreason,$delUrl);
			
			$model = $this->findModel($id);
			
			$command = Yii::$app->db->createCommand()
							->update('bpr_person', ['addedby_person_id_fk'=>Yii::$app->user->id,'isDeleted'=>'0','reasonIsRestored' => $delreason , 'isRestored' => '1', 'restore_datetime' => date("Y-m-d H:i:s")], 'person_id_pk='.$id)
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
			
	
		echo "<option value=''>--- Select One ---</option>";
        if($count>0){
			
            foreach($states as $k=>$v){
                echo "<option value='".$v->state_id_pk."'>".$v->name."</option>";
            }
        }
	}
	
	/*
	* Get State combobox
	*/
	public function actionCitylists($id)
    {
	
        $count = \backend\modules\city\models\City::find()
				->where(['bpr_city.state_id_fk' => $id, 'bpr_city.isDeleted' => '0'])
				->orderby('name ASC')
				->count();
	 
		$states = \backend\modules\city\models\City::find()
				->where(['bpr_city.state_id_fk' => $id, 'bpr_city.isDeleted' => '0'])
				->orderby('name ASC')
				->all();
			
	
		echo "<option value=''>--- Select One ---</option>";
        if($count>0){
			
            foreach($states as $k=>$v){
                echo "<option value='".$v->city_id_pk."'>".$v->name."</option>";
            }
        }
	}
    /**
     * Finds the Person model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Person the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Person::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
