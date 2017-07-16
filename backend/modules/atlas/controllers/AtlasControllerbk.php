<?php

namespace backend\modules\atlas\controllers;

use Yii;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\company\models\Atlas;
use backend\modules\company\models\AtlasSearch;
use backend\modules\company\models\AtlasSearchDel;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\Request;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class AtlasController extends Controller
{


    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {

        $this->layout = 'atlas-main';
		return $this->render('index');
    }

    public function actionClients(){
        
        if (Yii::$app->request->post()){
            $request = Yii::$app->request;
            $this->addClient($request);
        }

         

        $params = [':is_deleted'=> 0, ':country_id_fk' => 5];

        $result['states'] = Yii::$app->db->createCommand('SELECT name, state_id_pk  FROM bpr_state WHERE country_id_fk=:country_id_fk AND isDeleted=:is_deleted', $params)->queryAll();

        $result['clients'] = $this->getAllClients();

        $this->view->params['data'] = $result;

        $this->layout = 'atlas-clients';
        return $this->render('clients');
    }


     public function getAllClients(){
        $params = [':is_deleted'=> 0];
        
        return Yii::$app->db->createCommand('SELECT id, first_name, last_name, email FROM atlas_clients WHERE isDeleted=:is_deleted', $params)->queryAll();

    }

    public function addClient($request){


        $result = Yii::$app->db->createCommand()
            ->insert('atlas_clients', [
                'first_name' => $request->post('firstname'),
                'last_name' => $request->post('lastname'),
                'address' => $request->post('address'),
                'phone_number' => $request->post('phonenumber'),
                'state' => $request->post('state'),
                'city' => $request->post('city'),
                'zipcode' => $request->post('zipcode'),
                'email' => $request->post('email'),
                'username' => $request->post('username'),
                'password' => md5($request->post('password')),
                'created' => date('Y-m-d'),
                'updated' => date('Y-m-d')
                ])
            ->execute();

            if(!$result){
                print_r("Something went wrong"); die;
            }

            $this->actionCreateClientLinkEmail($request->post('email'));

            header("Location:".Yii::$app->homeUrl."atlas/atlas/clients");
            exit();


        // $result245 = Yii::$app->db->createCommand('UPDATE bpr_person SET password_person="'.md5($_POST['AdminResetPasswordForm']['password']).'", password_reset_token="",password_hash="'.$modelA->password_hash.'" WHERE person_id_pk="'.$modelA->person_id_pk.'" AND emailid="'.$modelA->emailid.'"')->execute();    
    }

	
    public function actionCity(){

        if(!Yii::$app->request->isAjax){
            return print_r('you dont have permission!');
        }
        $request = Yii::$app->request;
        $id = $request->get('id');

        $count = \backend\modules\city\models\City::find()->all();

          $params = [':is_deleted'=> 0, ':city_id_pk' => $id, ':country_id_fk' => 5];
          $queryResult = Yii::$app->db->createCommand('SELECT name, city_id_pk  FROM bpr_city WHERE country_id_fk=:country_id_fk AND city_id_pk=:city_id_pk AND isDeleted=:is_deleted', $params)->queryAll();

        // return Json    
        return \yii\helpers\Json::encode($queryResult);

    }

    public function actionView(){
        $request = Yii::$app->request;
        $id = $request->get('id'); 

        $params = [':id'=> $id];
        $result['info'] =  (new \yii\db\Query())->select(['*'])
                    ->from('atlas_clients')
                    //->leftJoin('bpr_city', 'bpr_city.city_id_pk = atlas_clients.city_id')
                   // ->leftJoin('bpr_state', 'bpr_state.state_id_pk = atlas_clients.state_id')
                    ->where('id = ' . $id)->one();

      
   
        $this->view->params['data'] = $result;

        $this->layout = 'atlas-clients-view';
        return $this->render('clients');
    }

    public function actionEdit(){

        $request = Yii::$app->request;
        //update client information
        if (Yii::$app->request->post()){
            $this->updateClient($request);
        }

          //current user id
        $id = $request->get('id'); 

        //get current client states
       $clientInfo = (new \yii\db\Query())->select('*')
                    ->from('atlas_clients')
                    ->where('id = ' . $id)->one();

                    //get current client city
        //$result['currState'] = $clientInfo['state_id'];
       // $result['currCity'] = $clientInfo['city_id'];



        //states
        $paramsState = [':is_deleted'=> 0, ':country_id_fk' => 5];
         $states = Yii::$app->db->createCommand('SELECT name, state_id_pk  FROM bpr_state WHERE country_id_fk=:country_id_fk AND isDeleted=:is_deleted', $paramsState)->queryAll();


         $paramsCity = [':is_deleted'=> 0, ':country_id_fk' => 5];
         $result['city'] = Yii::$app->db->createCommand('SELECT * FROM bpr_city WHERE country_id_fk=:country_id_fk AND isDeleted=:is_deleted', $paramsCity)->queryAll();
      
         // //city
         // $paramsCity = [':is_deleted'=> 0, ':country_id_fk' => 5, ':state_id_fk' => $userStateID['state_id']];
         //  $result['city'] = Yii::$app->db->createCommand('SELECT name, city_id_pk  FROM bpr_city WHERE country_id_fk=:country_id_fk AND isDeleted=:is_deleted', $paramsCity)->queryAll();


        $result['states'] = $states;

      

       // $params = [':id'=> $id];
        $result['info'] = (new \yii\db\Query())->select('*')
                    ->from('atlas_clients')
                    ->where('id = ' . $id)->one();
   
        $this->view->params['data'] = $result;

        $this->layout = 'atlas-clients-edit';
        return $this->render('clients');
    }

    public function updateClient($request){


        $array = [
                'first_name' => $request->post('firstname'),
                'last_name' => $request->post('lastname'),
                'address' => $request->post('address'),
                'phone_number' => $request->post('phonenumber'),
                'state' => $request->post('state'),
                'city' => $request->post('city'),
                'zipcode' => $request->post('zipcode'),
                'email' => $request->post('email'),
                'username' => $request->post('username'),
                'updated' => date('Y-m-d')
                ];

        if(!empty($request->post('password'))){

            $dataAr = ['password' => md5($request->post('password'))];
            $array = array_merge($array, $dataAr);
        }    

         $clientID = $request->post('client_id');

         $result = Yii::$app->db->createCommand()
            ->update('atlas_clients', $array, 'id = ' . $clientID)
            ->execute();

           

            header("Location:".Yii::$app->homeUrl."atlas/atlas/clients");
            exit();

    }

    public function actionDelete(){


         $request = Yii::$app->request;
        //update client information
        if (!Yii::$app->request->get()){
          print_r('permision denied');
        }

        //current user id
        $id = $request->get('id'); 

        $result = Yii::$app->db->createCommand()->update('atlas_clients', ['isDeleted' => 1, 'deleted_date' => date('Y-m-d')], 'id = ' . $id )
        ->execute();

        if($result){
            header("Location:".Yii::$app->homeUrl."atlas/atlas/clients");
            exit();
        }else{
            print_r($result);
        }
    }


    public function actionCreateClientLinkEmail($email){

                $getToken = rand(0, 99999);
                $getTime = date("H:i:s");
                $getModel->password_reset_token = md5($getToken.$getTime);
                
                $result245 = Yii::$app->db->createCommand('UPDATE atlas_clients SET password_reset_token = "'.$getModel-password_reset_token.'" WHERE emailid="'.$email.'"')->execute();
                
                $namaPengirim = Yii::$app->name." Admin"; 
                $emailadmin = Yii::$app->params['adminEmail'];
                $subjek = "Reset Password";
                $setpesan = "Hello ".$getModel->first_name." ".$getModel->last_name.",<br/><br/>Please reset your current password.<br/>
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
	
}
