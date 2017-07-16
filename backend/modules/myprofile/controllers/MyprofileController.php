<?php

namespace backend\modules\myprofile\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\myprofile\models\Myprofile;
use backend\modules\myprofile\models\MyprofileSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\HttpException;
use yii\filters\VerbFilter;

/**
 * PersonController implements the CRUD actions for Person model.
 */
class MyprofileController extends Controller
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
     * Lists all Person models.
     * @return mixed
     */
    public function actionIndex()
    {		
        $searchModel = new PersonSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	
    /**
     * Displays a single Person model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {		
		if($id!=Yii::$app->user->identity->person_id_pk)
		{	
			throw new HttpException(403,"You are not authorized to perform this action.");
		}
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MyProfile'],Yii::$app->params['audit_log_action']['ACCESS'],'Viewed profile',"");
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

	public function actionEmailunique()
	{
		$action = $_REQUEST['action'];
		switch($action){
			case "check_duplicate_email":
				$emailid = trim($_REQUEST['gmp_emailid']);
				$edit_id = (int)$_REQUEST['edit_id'];
				$condition = '';
				if(strlen($emailid) > 0){
					if($edit_id > 0){
						$condition = " and person_id_pk != ".$edit_id;
					}
					$sql = "SELECT COUNT(*) as persons from bpr_person where emailid= '". $emailid ."' ". $condition;
					$users = Yii::$app->db->createCommand($sql)->queryAll();
					$numClients = (int)$users[0]["persons"];
				
					if($numClients > 0){
						echo "false";
					} else {
						echo "true";
					}
				}
			break;
			case "check_duplicate_username":
				$emailid = trim($_REQUEST['gmp_user_name_person']);
				$edit_id = (int)$_REQUEST['edit_id'];
				$condition = '';
				if(strlen($emailid) > 0){
					if($edit_id > 0){
						$condition = " and person_id_pk != ".$edit_id;
					}
					$sql = "SELECT COUNT(*) as persons from bpr_person where user_name_person= '". $emailid ."'  ". $condition;
					$users = Yii::$app->db->createCommand($sql)->queryAll();
					$numClients = (int)$users[0]["persons"];
				
					if($numClients > 0){
						echo "false";
					} else {
						echo "true";
					}
				}
			break;
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
		if($id!=Yii::$app->user->identity->person_id_pk)
		{	
			throw new HttpException(403,"You are not authorized to perform this action.");
		}
		
		$model = $this->findModel($id);
		$emailFlag = false;
		$usernameFlag = false;
		$passwordFlag = false;     
     	if(isset($_POST) && isset($_POST['gmp_first_name']) && $_POST['gmp_first_name']!='')
		{
			$personid = $id;
			$frmtoken = isset($_POST['frmtoken'])?$_POST['frmtoken']:'';
			if($model->frmtoken!=$frmtoken)
			{
			
			$recordUpdateArr = array();	
			$recordUpdateArr = array('first_name' => trim($_POST['gmp_first_name']),
					'last_name' => trim($_POST['gmp_last_name']),
					'phone'=> trim($_POST['gmp_phone']),
					'fax' => trim($_POST['gmp_fax']),
					'address' => trim($_POST['gmp_address']),
					'city_id_fk' => $_POST['gmp_city_id_fk'],
					'state_id_fk' => $_POST['gmp_state_id_fk'],
					'country_id_fk' => $_POST['gmp_country_id_fk'],
					'pobox'=> trim($_POST['gmp_pobox']),
					'zip_pincode' => trim($_POST['gmp_zip_pincode']),
					'emailid' => trim($model->emailid),
					'user_name_person' => trim($model->user_name_person),
					'frmtoken' => $frmtoken);
			
			$getToken = rand(0, 99);
			$getTime = date("H:i:s");
			$verification_token = md5($getToken.$getTime);
			$emMsg = "";
			$session = Yii::$app->session;
			
			/*-----Check if email changed--------------------------------------------*/
			if($model->emailid != trim($_POST['gmp_emailid']) && $session->get('userProfileEmail')!=trim($_POST['gmp_emailid']))
			{
				$recordUpdateArr['new_emailid'] = trim($_POST['gmp_emailid']);
				$recordUpdateArr['verification_token'] = $verification_token;
				$emailFlag = true;
				$emMsg .= "Email : ".trim($_POST['gmp_emailid'])."<br/>";
				/*----Send notification email to old email id------------------------*/
				$toEmailE = $model->emailid;
				$subjectE = "Account email updated";
				$emailContentE = "Hello ".trim($_POST['gmp_first_name'])." ".trim($_POST['gmp_last_name']).",<br/><br/> Your account emailid is changed from ".$model->emailid." to ".trim($_POST['gmp_emailid'])." <br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
				CommonFunctions::sendAccountUpdateEmail($subjectE,$emailContentE,$toEmailE);
				$session->set('userProfileEmail', trim($_POST['gmp_emailid']));
				/*-------------------------------------------------------------------*/
			}
			/*-----Check if username changed-----------------------------------------*/
			if($model->user_name_person != trim($_POST['gmp_user_name_person'])  && $session->get('userProfileUsername')!=trim($_POST['gmp_user_name_person']))
			{
				$recordUpdateArr['new_username'] = trim($_POST['gmp_user_name_person']);
				$recordUpdateArr['verification_token'] = $verification_token;
				$usernameFlag = true;
				$emMsg .= "Username : ".trim($_POST['gmp_user_name_person'])."<br/>";
				$session->set('userProfileUsername', trim($_POST['gmp_user_name_person']));
			}
			/*-----Check if password changed--------------------------------------------*/
			if(strlen($_POST['gmp_password_person'])>0 && $session->get('userProfileUsername')!=md5($_POST['gmp_password_person']))
			{
				$passwordFlag = true;
				$recordUpdateArr['new_password'] = md5($_POST['gmp_password_person']);
				$recordUpdateArr['verification_token'] = $verification_token;
				$session->set('userProfilePw', md5($_POST['gmp_password_person']));
				//$emMsg .= "Password : ".trim($_POST['gmp_password_person'])."<br/>";
			}
					
			/*----Update Persons Data into DB-------------------------------------------*/
			$command = Yii::$app->db->createCommand()->update('bpr_person', $recordUpdateArr, "person_id_pk='".$personid."'")->execute();
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['MyProfile'],Yii::$app->params['audit_log_action']['UPDATE'],'Updated profile',"");
			
			/*-----Send email to verify account update/changes---------------------------*/
			if($emailFlag == true || $usernameFlag == true || $passwordFlag == true)
			{
				$toEmail = trim($_POST['gmp_emailid']);
				$subject = "Account details updated";
				$emailContent = "Hello ".trim($_POST['gmp_first_name'])." ".trim($_POST['gmp_last_name']).",<br/><br/> Your account credentials are changed.<br/>".$emMsg." Please click on below link to activate your changes.<br/><br/><a href='".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."'>".Yii::$app->params['baseURLEmailTempPath']."/site/verifyemail/?token=".$verification_token."</a><br/><br/>Regards,<br/> ".Yii::$app->name." Services";	
				CommonFunctions::sendAccountUpdateEmail($subject,$emailContent,$toEmail);
				
				$session->remove('userProfileEmail');
				$session->remove('userProfileUsername');
				$session->remove('userProfilePw');
				header("Location:".Yii::$app->homeUrl."/site/logout");
				exit;
			}
			 /*-------------------------------------------------------------------------*/
			}
			return $this->redirect(['view','id' => $personid ]);
		}else{
			
			return $this->render('update', [
				'model' => $model,
			]);
		}
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
        if (($model = Myprofile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
