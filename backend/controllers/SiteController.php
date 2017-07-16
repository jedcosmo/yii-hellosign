<?php
namespace backend\controllers;

use Yii;
use backend\modules\activitylog\models\ActivityLog;
use yii\filters\AccessControl;
use yii\web\Controller;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use common\models\AdminLoginForm;
use common\models\AdminForgotPasswordForm;
use common\models\AdminResetPasswordForm;
use backend\modules\adminUser\models\Admin;
use backend\modules\person\models\Person;
use backend\modules\bprrecords\models\Bprrecords;
use backend\modules\bprrecords\models\BprrecordsSearch;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\mprdefinition\models\MprdefinitionSearch;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\IdentityInterface;
use yii\web\Request;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error','forgot','resetpassword','expired','verifyemail', 'emailtest'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index','dashboard','getcompaniesbprcount','getcompaniesmprcount'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
		if (Yii::$app->user->isGuest) {
        	return $this->render('index');
		}
		else
		{
			$model = new AdminLoginForm();
			
			$BPRS = Bprrecords::find()->where(['isDeleted'=>'0','super_company_id_fk'=>Yii::$app->user->identity->super_company_id_fk])->orderby('bpr_id_pk DESC')->all();
			$MPRS = Mprdefination::find()->where(['isDeleted'=>'0','super_company_id_fk'=>Yii::$app->user->identity->super_company_id_fk])->orderby('mpr_defination_id_pk DESC')->all();
          
			return $this->render('welcome', [
                'model' => $model,
				'BPRS' => $BPRS,
            	'MPRS' => $MPRS,
            ]);
		}	
    }
	
	public function actionGetcompaniesbprcount()
	{
		$selectedDate = isset($_REQUEST['selectedDate'])?$_REQUEST['selectedDate']:'';
		$startDate = '';
		$endDate = '';
		$params = [':isDeleted' => '0'];
		$query = '';
		
		if($selectedDate)
		{
			 $selectedDateArr = array();
			 $selectedDateArr = explode("|", $selectedDate);
			 if(count($selectedDateArr)>1)
			 {
			 	$startDate = $selectedDateArr[0];
			 	$endDate = $selectedDateArr[1];
				$params = [':isDeleted' => '0',':startDate'=>$startDate,':endDate'=>$endDate];
				$query = 'SELECT company_id_pk,name,count(bpr_batch_processing_records.bpr_id_pk) as bcnt FROM bpr_person_company LEFT JOIN bpr_batch_processing_records ON bpr_batch_processing_records.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted AND (bpr_batch_processing_records.created_datetime BETWEEN :startDate AND :endDate) GROUP BY bpr_person_company.company_id_pk';
			 }
			 elseif(count($selectedDateArr)==1)
			 {
			 	$startDate = $selectedDateArr[0];
			 	$endDate = '';
				$params = [':isDeleted' => '0',':startDate'=>$startDate];
				$query = 'SELECT company_id_pk,name,count(bpr_batch_processing_records.bpr_id_pk) as bcnt FROM bpr_person_company LEFT JOIN bpr_batch_processing_records ON bpr_batch_processing_records.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted AND (bpr_batch_processing_records.created_datetime =:startDate) GROUP BY bpr_person_company.company_id_pk';
			 }
			 else
			 {
			 	$startDate = '';
			 	$endDate = '';
				$params = [':isDeleted' => '0'];
				$query = 'SELECT company_id_pk,name,count(bpr_batch_processing_records.bpr_id_pk) as bcnt FROM bpr_person_company LEFT JOIN bpr_batch_processing_records ON bpr_batch_processing_records.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk';
			 }
		}
		else
		{
			$query = 'SELECT company_id_pk,name,count(bpr_batch_processing_records.bpr_id_pk) as bcnt FROM bpr_person_company LEFT JOIN bpr_batch_processing_records ON bpr_batch_processing_records.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk';
		}
		
		$companiesBPR = Yii::$app->db->createCommand($query, $params)->queryAll();
		return $this->renderPartial('_companiesBPRcount', [
        	'companiesBPR' => $companiesBPR,
        ]);
	}
	
	public function actionGetcompaniesmprcount()
	{
		$selectedDate = isset($_REQUEST['selectedDate'])?$_REQUEST['selectedDate']:'';
		$startDate = '';
		$endDate = '';
		$params = [':isDeleted' => '0'];
		$query = '';
		
		if($selectedDate)
		{
			 $selectedDateArr = array();
			 $selectedDateArr = explode("|", $selectedDate);
			 if(count($selectedDateArr)>1)
			 {
			 	$startDate = $selectedDateArr[0];
			 	$endDate = $selectedDateArr[1];
				$params = [':isDeleted' => '0',':startDate'=>$startDate,':endDate'=>$endDate];
				
				$query = 'SELECT company_id_pk,name,count(bpr_mpr_defination.mpr_defination_id_pk) as mcnt FROM bpr_person_company LEFT JOIN bpr_mpr_defination ON bpr_mpr_defination.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted AND (bpr_mpr_defination.created_datetime BETWEEN :startDate AND :endDate) GROUP BY bpr_person_company.company_id_pk';
			 }
			 elseif(count($selectedDateArr)==1)
			 {
			 	$startDate = $selectedDateArr[0];
			 	$endDate = '';
				$params = [':isDeleted' => '0',':startDate'=>$startDate];
				$query = 'SELECT company_id_pk,name,count(bpr_mpr_defination.mpr_defination_id_pk) as mcnt FROM bpr_person_company LEFT JOIN bpr_mpr_defination ON bpr_mpr_defination.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted AND (bpr_mpr_defination.created_datetime =:startDate) GROUP BY bpr_person_company.company_id_pk';
			 }
			 else
			 {
			 	$startDate = '';
			 	$endDate = '';
				$params = [':isDeleted' => '0'];
				$query = 'SELECT company_id_pk,name,count(bpr_mpr_defination.mpr_defination_id_pk) as mcnt FROM bpr_person_company LEFT JOIN bpr_mpr_defination ON bpr_mpr_defination.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk';
			 }
		}
		else
		{
			$query = 'SELECT company_id_pk,name,count(bpr_mpr_defination.mpr_defination_id_pk) as mcnt FROM bpr_person_company LEFT JOIN bpr_mpr_defination ON bpr_mpr_defination.super_company_id_fk=bpr_person_company.company_id_pk WHERE bpr_person_company.isDeleted=:isDeleted GROUP BY bpr_person_company.company_id_pk';
		}
		
		$companiesMPR = Yii::$app->db->createCommand($query, $params)->queryAll();
		return $this->renderPartial('_companiesMPRcount', [
        	'companiesMPR' => $companiesMPR,
        ]);
	}

   public function actionLogin()
    {


        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new AdminLoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
        	
			ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Login'],Yii::$app->params['audit_log_action']['ACCESS'],'Logged into website',"");
			//check user browser and device
			$res = $this->getBrowser();
			$platform = $res['platform'];
			$version = $res['version'];
			$browser = $res['name'];
			//get client ip address
			$ipaddress = $this->get_client_ip();


			$this->securityCheckNotification($platform, $browser, $version, $ipaddress); 


			CommonFunctions::updateSessionUserId($platform, $browser, $version, $ipaddress);
			$request = Yii::$app->request->post();

			//check if atlas client or admin
			if(Yii::$app->user->identity->is_atlas_admin == 'yes'){
				$_SESSION['atlas_role'] = 'admin';
			}else{
				$_SESSION['atlas_role'] = 'client';
			}
			
			//redirect to the atlas page
			if(Yii::$app->user->identity->is_atlas_client == 'yes'){
				return $this->redirect(['atlas/atlas']);
			}else{
				return $this->goHome();
			}
            
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    private function securityCheckNotification($platform, $browser, $version, $ipaddress){
    	//get browser information 
		

		$params = [':ip_address'=>$ipaddress , ':browser' => $browser . ' - ' . $version, ':platform' => $platform, ':person_id_pk' => Yii::$app->user->id];
		$queryResult = Yii::$app->db->createCommand('SELECT COUNT(*) as count, emailid FROM bpr_person WHERE ip_address=:ip_address AND browser=:browser AND platform=:platform AND person_id_pk=:person_id_pk', $params)->queryAll();

		

		if($queryResult[0]['count'] == 0){
			$this->emailNotificationBrowser($platform, $browser . ' - ' . $version, $ipaddress, $queryResult[0]['emailid']);
		}
		

    }

    private function emailNotificationBrowser($platform, $browser, $ipaddress, $userEmail){
    	
		$to = $userEmail;
		$subject = "Security Notification - Cloud GMP";

		$message = "Hello!<br/><br/>";
		$message .= "Your account has been accessed from:" . "<br/><br/>";
		$message .= "Platform: " . $platform . "<br/>";
		$message .= "Browser: " . $browser . "<br/>";
		$message .= "IP Address: " . $ipaddress . "<br/><br/>";

		$message .= "Please check your account or contact Cloud GMP Support". "<br/><br/>";

		$message .= "Best Regards <br/> Cloud GMP". "\r\n";


		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <noreply@mindts.com>' . "\r\n";
		$headers .= 'Cc: marjunvillegas@xerosoft.com' . "\r\n";

		mail($to,$subject,$message,$headers);

		$this->refresh();
    }

    private function getBrowser() 
	{ 
	    $u_agent = $_SERVER['HTTP_USER_AGENT']; 
	    $bname = 'Unknown';
	    $platform = 'Unknown';
	    $version= "";
		
		 $bname = 'Internet Explorer'; 
	     $ub = "MSIE"; 

	    //First get the platform?
	    if (preg_match('/linux/i', $u_agent)) {
	        $platform = 'linux';
	    }
	    elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
	        $platform = 'mac';
	    }
	    elseif (preg_match('/windows|win32/i', $u_agent)) {
	        $platform = 'windows';
	    }

	    // Next get the name of the useragent yes seperately and for good reason
	    if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
	    { 
	        $bname = 'Internet Explorer'; 
	        $ub = "MSIE"; 
	    } 
	    elseif(preg_match('/Firefox/i',$u_agent)) 
	    { 
	        $bname = 'Mozilla Firefox'; 
	        $ub = "Firefox"; 
	    }
	    elseif(preg_match('/OPR/i',$u_agent)) 
	    { 
	        $bname = 'Opera'; 
	        $ub = "Opera"; 
	    } 
	    elseif(preg_match('/Chrome/i',$u_agent)) 
	    { 
	        $bname = 'Google Chrome'; 
	        $ub = "Chrome"; 
	    } 
	    elseif(preg_match('/Safari/i',$u_agent)) 
	    { 
	        $bname = 'Apple Safari'; 
	        $ub = "Safari"; 
	    } 
	    elseif(preg_match('/Netscape/i',$u_agent)) 
	    { 
	        $bname = 'Netscape'; 
	        $ub = "Netscape"; 
	    } 

	    // finally get the correct version number
	    $known = array('Version', $ub, 'other');
	    $pattern = '#(?<browser>' . join('|', $known) .
	    ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
	    if (!preg_match_all($pattern, $u_agent, $matches)) {
	        // we have no matching number just continue
	    }

	    // see how many we have
	    $i = count($matches['browser']);
	    if ($i != 1) {
	        //we will have two since we are not using 'other' argument yet
	        //see if version is before or after the name
	        if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
	            $version= $matches['version'][0];
	        }
	        else {
	            $version= $matches['version'][1];
	        }
	    }
	    else {
	        $version= $matches['version'][0];
	    }

	    // check if we have a number
	    if ($version==null || $version=="") {$version="?";}

	    return array(
	        'userAgent' => $u_agent,
	        'name'      => $bname,
	        'version'   => $version,
	        'platform'  => $platform,
	        'pattern'    => $pattern
	    );
	} 

	private function get_client_ip() {
	    $ipaddress = '';
	    if (getenv('HTTP_CLIENT_IP'))
	        $ipaddress = getenv('HTTP_CLIENT_IP');
	    else if(getenv('HTTP_X_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	    else if(getenv('HTTP_X_FORWARDED'))
	        $ipaddress = getenv('HTTP_X_FORWARDED');
	    else if(getenv('HTTP_FORWARDED_FOR'))
	        $ipaddress = getenv('HTTP_FORWARDED_FOR');
	    else if(getenv('HTTP_FORWARDED'))
	       $ipaddress = getenv('HTTP_FORWARDED');
	    else if(getenv('REMOTE_ADDR'))
	        $ipaddress = getenv('REMOTE_ADDR');
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

    public function actionLogout()
    {
		CommonFunctions::unlockTheRecord();
		ActivityLog::logUserActivity(Yii::$app->user->id,Yii::$app->params['audit_log_screen_name']['Logout'],Yii::$app->params['audit_log_action']['ACCESS'],'Logged out from website',"");
        Yii::$app->user->logout();		
        return $this->goHome();
    }
	
	public function getToken($token)
    {
		$getModel_all = Person::find()->where(['password_reset_token' => $token])->all();
		if(count($getModel_all)>0)
			$model = $getModel_all[0];
		else
			$model = null;
        if($model===null)
		{
				header("Location:".Yii::$app->homeUrl."site/expired");
				exit();
		}
        return $model;
    }
 
 
 	 public function actionExpired()
    {		
		$model = new AdminResetPasswordForm();
		return $this->render('expired', [
			'model' => $model,
		]);		
    }
	
	public function actionResetpassword($token)
	{
		$model = new AdminResetPasswordForm();
		$modelA = $this->getToken($token);
		
		if(isset($_POST['AdminResetPasswordForm']))
		{
			if($modelA->password_reset_token == $_POST['tokenhid'])
			{
				$modelA->password_hash = Yii::$app->security->generatePasswordHash($_POST['AdminResetPasswordForm']['password']);
				$modelA->password_reset_token = '';
				$result245 = Yii::$app->db->createCommand('UPDATE bpr_person SET password_person="'.md5($_POST['AdminResetPasswordForm']['password']).'", password_reset_token="",password_hash="'.$modelA->password_hash.'" WHERE person_id_pk="'.$modelA->person_id_pk.'" AND emailid="'.$modelA->emailid.'"')->execute();
								
				header("Location:".Yii::$app->homeUrl."site/login");
				exit();
			}
		}
		
		$this->render('verifikasi',array(
			'model'=>$model,'token'=>$token
		));
	}
	
	
	public function actionForgot()
	{
		 if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
		
		$error_msg = '';
		$success_msg = '';
		$model = new AdminForgotPasswordForm();
		$Adminmodel = new Admin();
		if(isset($_POST['AdminForgotPasswordForm']))
		{
			$getEmail = $_POST['AdminForgotPasswordForm']['email'];
			$getModel_all = Person::find()->where(['emailid' => $getEmail])->all();
			if(count($getModel_all)>0)
				$getModel = $getModel_all[0];
			else
				$getModel = null;
			
			if($getModel != null)
			{
				$getToken = rand(0, 99999);
				$getTime = date("H:i:s");
				$getModel->password_reset_token = md5($getToken.$getTime);
				
				$result245 = Yii::$app->db->createCommand('UPDATE bpr_person SET password_reset_token = "'.$getModel->password_reset_token.'" WHERE person_id_pk="'.$getModel->person_id_pk.'" AND emailid="'.$getEmail.'"')->execute();
				
				$namaPengirim = Yii::$app->name." Admin"; 
				$emailadmin = Yii::$app->params['adminEmail'];
				$subjek = "Reset Password";
				$setpesan = "Hello ".$getModel->first_name." ".$getModel->last_name.",<br/><br/>Your request for reset password has successfully processed.<br/>
					Click on below link to Reset Your Password.<br/><br/><a href='".Yii::$app->params['baseURLEmailTempPath']."/site/resetpassword/?token=".$getModel->password_reset_token."'>".Yii::$app->params['baseURLEmailTempPath']."/site/resetpassword/?token=".$getModel->password_reset_token."</a><br/><br/>Regards,<br/> ".Yii::$app->name." Services";	

					$email_msg = file_get_contents(Yii::$app->basePath."/web/emailtemplate/forgot-password-email.html");
					$email_msg = str_replace('{email_content}',$setpesan,$email_msg);
					$email_msg = str_replace('{base_url}',Yii::$app->params['baseURLEmailTempPath'],$email_msg);
					$email_msg = str_replace('{sitetitle}',Yii::$app->name,$email_msg);
					$email_msg = str_replace('{date}',date("Y"),$email_msg);
				
				
					$name = '=?UTF-8?B?'.base64_encode($namaPengirim).'?=';
					$subject = '=?UTF-8?B?'.base64_encode($subjek).'?=';
					$headers = "From: $namaPengirim <{$emailadmin}>\r\n".
						"Reply-To: {$emailadmin}\r\n".
						//"MIME-Version: 1.0\r\n".
						"Content-type: text/html; charset=UTF-8";
					
					$success_msg = "Reset password instructions are sent to your email, please check your inbox for same.";
					//echo $email_msg; exit;
					@mail($getEmail,$subject,$email_msg,$headers);

					$this->refresh();
				
			}
			else
			{
				 $error_msg = 'The requested email does not exist.';
				 $success_msg = '';
			} 
		}
		$this->render('forgot',[ 'model' => $model,'error_msg' => $error_msg, 'success_msg'=>$success_msg]);
	}
	
	public function actionVerifyemail($token)
	{
		Yii::$app->user->logout();
		Yii::$app->session->destroy();
		Yii::$app->session->close();
		@session_destroy();

		$getModel_all = Person::find()->where(['verification_token' => $token])->all();
		if(count($getModel_all)>0)
			$model = $getModel_all[0];
		else
			$model = null;
        if($model===null)
		{
				header("Location:".Yii::$app->homeUrl."site/expired");
				exit();
		}
		else{
			if($model->isDeleted == '0')
			{
				if($model->verification_token == $token)
				{
					$nwEmail = $model->new_emailid;
					$nwUsername = $model->new_username;
					$nwPassword = $model->new_password;
					
					$qryStr = '';
					if($nwEmail)
						$qryStr .= " emailid='".$nwEmail."', ";
					if($nwUsername)
						$qryStr .= " user_name_person='".$nwUsername."', ";
					if($nwPassword)
						$qryStr .= " password_person='".$nwPassword."', ";
					
					$result = Yii::$app->db->createCommand('UPDATE bpr_person SET '.$qryStr.' is_verified="1", verification_token="", new_emailid="", new_username="", new_password="" WHERE person_id_pk="'.$model->person_id_pk.'"')->execute();
					
					return $this->redirect(['site/login','verify'=>'success']);
				}
			}elseif($model->isDeleted == '1'){
				return $this->redirect(['site/login','verify'=>'error']);
			}else{
				return $this->redirect(['site/login']);
			}
		}
	}
	
	public function actionDashboard()
	{
		$request = Yii::$app->request;
		$mpr_definition_id_fk = intval($request->get('mpr_definition_id_fk',0));
		$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($mpr_definition_id_fk);
		$params = [':mpr_defination_id_fk'=>$mpr_definition_id_fk , ':isDeleted' => '0'];
		$BOMs = Yii::$app->db->createCommand('SELECT material_name,qty_branch,qb_unit_id_fk,composition,com_unit_id_fk,price_per_unit FROM bpr_bill_of_material WHERE mpr_defination_id_fk=:mpr_defination_id_fk AND isDeleted=:isDeleted ORDER BY bom_id_pk DESC', $params)->queryAll();
		
		return $this->renderPartial('_dashboard_bill_of_materials', [
           		'BOMs' => $BOMs,
				'mpr_definition_id_fk' => $mpr_definition_id_fk,
				'MPRDetails' => $MPRDetails,
        ]);
	}
	
	public function actionEmailtest()
	{
		$success_msg = '';
		if(isset($_POST['email']) && strlen($_POST['email'])>0)
		{
			$from = "Admin"; 
			$fromEmail = "pm@mindts.com";
			
			$hostname = $_SERVER['HTTP_HOST'];
			
			if(strstr($hostname,'wiseprm'))
			{
				$subject = "Cloud-wiseprm - Test Email - ".date("d-m-Y H:i:s");
			}
			elseif(strstr($hostname,'cloudgmp.com'))
			{
				$subject = "Cloudgmp.com - Test Email - ".date("d-m-Y H:i:s");
			}
			else
			{
				$subject = "Cloud-X30 - Test Email - ".date("d-m-Y H:i:s");
			}
			$emailContent = "Hello User,<br/><br/> This is test email to check delay in the email.<br/> Email sent from php script on ".date("d-m-Y H:i:s").".<br/><br/><br/><br/>Regards,<br/>  Cloudx30 Services";	
	
			$email_msg = file_get_contents(Yii::$app->basePath."/web/emailtemplate/verify-email.html");
			$email_msg = str_replace('{email_content}',$emailContent,$email_msg);
			$email_msg = str_replace('{subject}',$subject,$email_msg);
			$email_msg = str_replace('{base_url}',Yii::$app->params['baseURLEmailTempPath'],$email_msg);
			$email_msg = str_replace('{sitetitle}','CloudGMP',$email_msg);
			$email_msg = str_replace('{date}',date("Y"),$email_msg);
	
			$name = '=?UTF-8?B?'.base64_encode($from).'?=';
			$subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
			
			$headers = "From: $from <{$fromEmail}>\r\n";
			$headers.= "Reply-To: {$fromEmail}\r\n";
			$headers.= "MIME-Version: 1.0\r\n";
			//$headers.= "Content-type: text/html; charset=UTF-8";
			$headers .= "Content-Type: text/html; charset=ISO-8859-1\n";
			//echo $email_msg; exit;
			$success_msg = "Email sent successfully, please check your inbox for same.";
			@mail($_POST['email'],$subject,$email_msg,$headers);
			$this->refresh();
			
		}
		$this->render('emailtest',array('success_msg'=>$success_msg));
	}

}
