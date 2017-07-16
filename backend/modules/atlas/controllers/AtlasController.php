<?php

namespace backend\modules\atlas\controllers;

use Yii;
use HelloSign;
use FPDF;
use FPDI;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\activitylog\models\ActivityLog;
use backend\modules\atlas\models\Atlas;
use backend\modules\atlas\models\Applications;
use backend\modules\atlas\models\Contract;
use backend\modules\atlas\models\Promissory;
use backend\modules\atlas\models\CCAuthorization;
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

    protected $pdfLayoutsFilePath; 
    protected $pdfClientsFilePath;
    protected $client_id = '8b8c5ac8b1088d91da8c20276f7df143';
    protected $api_key = '55be0e73afd21781ab262a36ead8e568c9371e0b28e180a5ae039316de42281b';
    
    
    public function __construct($id, $module, $config = array()) {
        parent::__construct($id, $module, $config);
        
        $this->view->params['sidebar_settings'] = self::_getSidebarFormMenuSettings();
    }
    
    /**
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex()
    {

        $this->layout = 'atlas-main';
		return $this->render('index');
    }


    public function actionVerification(){
        
        $request = Yii::$app->request;
        $token = $request->get('token');

         $result = Yii::$app->db->createCommand()
        ->update('bpr_person', ['is_verified' => 1], 'verification_token = "' . $token . '"')
        ->execute();

        $this->layout = 'atlas-clients-verification';
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


        $transaction = Yii::$app->db->beginTransaction();
        try {

        $verification_token = md5($request->post('username'));
            //username and password
          Yii::$app->db->createCommand()->insert('bpr_person', [
                'first_name' => $request->post('firstname'),
                'last_name' => $request->post('lastname'),
                'emailid'   =>  $request->post('email'),
                'super_company_id_fk'   => 14,
                'verification_token'   =>  $verification_token,
                'user_name_person'   =>  $request->post('username'),
                'password_person'   =>  md5($request->post('password')),
                'is_atlas_client'   =>  'yes'
               ])
            ->execute();

          $insert_id =  Yii::$app->db->getLastInsertID();


          Yii::$app->db->createCommand()
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

      
                'person_id_fk' => $insert_id,

                'created' => date('Y-m-d'),
                'updated' => date('Y-m-d')
                ])
            ->execute();


          $this->sendEmailVerification($request->post('firstname'), $request->post('email'), $verification_token);

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }


            header("Location:".Yii::$app->homeUrl."atlas/atlas/clients");
            exit();

   
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
    
    /*
     * developer: jerome.dymosco
     * Shows Atlas application form.
     */
    public function actionApplication(){ 
        $this->_restrictClientAccessOther(); //let's restrict client to access their own profile only.
        
        $current_user_id = $this->_getCurrentUserId();
        
        $model = new Applications();
        $applications = $model->findApplicationByPersonFKId($current_user_id);
        $model->attributes = Yii::$app->request->post();
        
        if( $applications ){
            $applications->attributes = Yii::$app->request->post();
        }
        
        $errors = '';
                
        if ( Yii::$app->request->post() ) {                        
            if ( $model->validate() ) {
                //this will save new record.
                if(!$applications) {
                    $model->client_id_fk = $current_user_id;
                    $model->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully created.');
                }else{ //let's just update the existing record.
                    $applications->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully updated.');
                }                
                
                $this->redirect(Yii::$app->homeUrl.'atlas/atlas/application'.$this->_concatParams());
                return;
            } else {
                // validation failed: $errors is an array containing error messages
                $errors = $model->errors;                
            }
        }
        
        $this->view->params['application'] = $applications;
        $this->view->params['model'] = $model; 
        $this->view->params['errors'] = $errors;
        
        $this->layout = 'atlas-clients-application';
        
        return $this->render('index');
    }
    
    /*
     * developer: jerome.dymosco
     * Shows Atlas Contract to Indemnify form.
     */
    public function actionContract(){
        $this->_restrictClientAccessOther(); //let's restrict client to access their own profile only.
        
        $current_user_id = $this->_getCurrentUserId();
        $model = new Contract();
        $contract = $model->findContractByPersonFKId($current_user_id);
        $model->attributes = Yii::$app->request->post();        
        $errors = '';        
        
        $year = Yii::$app->request->post('contract_date_year');
        $month = Yii::$app->request->post('contract_date_month');
        $day = Yii::$app->request->post('contract_date_day');
        
        if( $contract ){
            $contract->attributes = Yii::$app->request->post();            
        }
        
        if ( Yii::$app->request->post() ) {                                          
            if( $contract ){
               $contract->contract_date = "$year-$month-$day"; 
               $model_validate = $contract;               
            }else{
               $model->contract_date = "$year-$month-$day";
               $model_validate = $model;               
            }
                        
            if ( $model_validate->validate() ) {
                //this will save new record.
                if(!$contract) {
                    $model->client_id_fk = $current_user_id;                      
                    $model->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully created.');
                }else{ //let's just update the existing record.                    
                    $contract->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully updated.');
                }                
                
                $this->redirect(Yii::$app->homeUrl.'atlas/atlas/contract'.$this->_concatParams());
                return;
            } else {
                // validation failed: $errors is an array containing error messages
                $errors = $model_validate->errors;                
            }
        }
        
        $this->view->params['contract'] = $contract;
        $this->view->params['model'] = $model; 
        $this->view->params['errors'] = $errors;
        
        $this->layout = 'atlas-clients-contract';
        return $this->render('index'); 
    }
    
    /*
     * developer: jerome.dymosco
     * Shows Atlas Promissory Note form.
     */
    public function actionPromissory(){
        $this->_restrictClientAccessOther(); //let's restrict client to access their own profile only.
        
        $current_user_id = $this->_getCurrentUserId();
        $model = new Promissory();
        $promissory = $model->findPromissoryByPersonFKId($current_user_id);
        $model->attributes = Yii::$app->request->post();

        if( $promissory ){
            $promissory->attributes = Yii::$app->request->post();
        }

        $errors = '';

        if ( Yii::$app->request->post() ) {                        
            if ( $model->validate() ) {
                //this will save new record.
                if(!$promissory) {
                    $model->client_id_fk = $current_user_id;
                    $model->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully created.');
                }else{ //let's just update the existing record.
                    $promissory->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully updated.');
                }                

                $this->redirect(Yii::$app->homeUrl.'atlas/atlas/promissory'.$this->_concatParams());
                return;
            } else {
                // validation failed: $errors is an array containing error messages
                $errors = $model->errors;                
            }
        }
        
        $this->view->params['promissory'] = $promissory;
        $this->view->params['model'] = $model; 
        $this->view->params['errors'] = $errors;
        
        $this->layout = 'atlas-clients-promissory';
        return $this->render('index');
    }


    public function sendEmailVerification($name, $email, $verification){
        $to = $email;
        $subject = "Verification - Cloud GMP";

        $link = $_SERVER['SERVER_NAME'] . '/backend/web/atlas/atlas/verification?token=' . $verification;

        $message = "Hello " . $name . "!<br/><br/>";

        $message .= "Your account has been created". "<br/><br/>";

        $message .= "You're on your way! Let's confirm your email address:" . "<br/><br/>";

        $message .= "<a href='" . $link . "'>Click Here!</a>" . "<br/><br/>";
    

        $message .= "Best Regards <br/> Cloud GMP". "\r\n";


        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        // More headers
        $headers .= 'From: <noreply@mindts.com>' . "\r\n";
        $headers .= 'Cc: marjunvillegas@xerosoft.com' . "\r\n";

        mail($to,$subject,$message,$headers);

        $this->refresh();
    }


    public function actionCheckmail(){
        $request = Yii::$app->request;
        $email = $request->post('email');

        $result =  (new \yii\db\Query())->select(['COUNT(*) as count'])
                    ->from('bpr_person')
                    ->where('emailid = "' . $email . '"')->one();

        if($result['count']){
            return 'false';
        }else{
            return 'true';
        }
    }

     public function actionCheckusername(){
        $request = Yii::$app->request;
        $username = $request->post('username');

         $result =  (new \yii\db\Query())->select(['COUNT(*) as count'])
                    ->from('bpr_person')
                    ->where('user_name_person = "' . $username . '"')->one();

         if($result['count']){
            return 'false';
        }else{
            return 'true';
        }
    }

 
    
    /*
     * Shows Atlas AAA Atlas Bail Bonds/CC Authorization form.
     */
    public function actionCcauthorization(){
        $this->_restrictClientAccessOther(); //let's restrict client to access their own profile only.
        
        $current_user_id = $this->_getCurrentUserId();
        $model = new CCAuthorization();
        $ccauthorization = $model->findCCAuthorizationByPersonFKId($current_user_id);
        $model->attributes = Yii::$app->request->post();

        if( $ccauthorization ){
            $ccauthorization->attributes = Yii::$app->request->post();
        }

        $errors = '';

        if ( Yii::$app->request->post() ) {                        
            if ( $model->validate() ) {
                //this will save new record.
                if(!$ccauthorization) {
                    $model->client_id_fk = $current_user_id;
                    $model->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully created.');
                }else{ //let's just update the existing record.
                    $ccauthorization->save();
                    Yii::$app->session->set('notification_msg', 'Details was successfully updated.');
                }                

                $this->redirect(Yii::$app->homeUrl.'atlas/atlas/ccauthorization'.$this->_concatParams());
                return;
            } else {
                // validation failed: $errors is an array containing error messages
                $errors = $model->errors;                
            }
        }

        $this->view->params['ccauthorization'] = $ccauthorization;
        $this->view->params['model'] = $model; 
        $this->view->params['errors'] = $errors;
        
        $this->layout = 'atlas-clients-ccauthorization';
        return $this->render('index'); 
    }
    
    /* developer: jerome.dymosco */
    public function actionReview(){                    
        $current_user_id = Yii::$app->user->identity->person_id_pk;
        $signed_confirm_id = Atlas::atlasGetRecordSignatureRequest($current_user_id);
        
        $this->pdfLayoutsFilePath = Yii::$app->params['dirRootPath'].'/backend/web/uploads/atlas_pdfs/layouts/';
        $this->pdfClientsFilePath = Yii::$app->params['dirRootPath'].'/backend/web/uploads/atlas_pdfs/clients/';
                        
        //Let's do now the sending of request now using HelloSign API
        if( Yii::$app->request->get('do_sign') && !$signed_confirm_id ){
            $client = new HelloSign\Client($this->api_key);

            $request = new HelloSign\SignatureRequest;
            $request->enableTestMode();
            $request->setTitle('Atlas Bails Paperwork');
            $request->setSubject('Atlas Bails - Signature Request');
            $request->setMessage('Please sign this document and then we can discuss more. Let me know if you have any questions.');
            $request->addSigner('jerome@xerosoft.com', 'Jerome');        
            $request->addFile($this->pdfClientsFilePath.$current_user_id."-AtlasBailForms.pdf");
                        
            $request->setFormFieldsPerDocument(
                array( //everything
                    array( //document 1
                        array( //component 1
                            "api_id"=> "client_application_signature_".$current_user_id,
                            "name"=> "",
                            "type"=> "signature",
                            "x"=> 275,
                            "y"=> 763,
                            "width"=> 250,
                            "height"=> 25,
                            "required"=> true,
                            "signer"=> 0
                        ),       
                        array( //component 1
                            "api_id"=> "client_contract_signature_".$current_user_id,
                            "name"=> "",
                            "type"=> "signature",
                            "x"=> 460,
                            "y"=> 1560,
                            "width"=> 250,
                            "height"=> 25,
                            "required"=> true,
                            "signer"=> 0
                        ),
                        array( //component 1
                            "api_id"=> "client_promissory_signature_".$current_user_id,
                            "name"=> "",
                            "type"=> "signature",
                            "x"=> 200,
                            "y"=> 2470,
                            "width"=> 200,
                            "height"=> 25,
                            "required"=> true,
                            "signer"=> 0
                        ),
                        array( //component 1
                            "api_id"=> "client_ccauthorization_signature_".$current_user_id,
                            "name"=> "",
                            "type"=> "signature",
                            "x"=> 275,
                            "y"=> 3250,
                            "width"=> 200,
                            "height"=> 25,
                            "required"=> true,
                            "signer"=> 0
                        ),
                    ),
                )
            );

    //        $response = $client->sendSignatureRequest($request);        

            // Turn it into an embedded request
            $embedded_request = new HelloSign\EmbeddedSignatureRequest($request, $this->client_id);

            // Send it to HelloSign
            $response = $client->createEmbeddedSignatureRequest($embedded_request);
            $signature_request_id = $response->signature_request_id;
            // Grab the signature ID for the signature page that will be embedded in the
            // page (for the demo, we'll just use the first one)
            $signatures   = $response->getSignatures();
            $signature_id = $signatures[0]->getId();

            //echo $signature_id;exit;

            // Retrieve the URL to sign the document
            $response = $client->getEmbeddedSignUrl($signature_id);

            // Store it to use with the embedded.js HelloSign.open() call
            $sign_url = $response->getSignUrl();

            $this->view->params['response'] = $response;
            $this->view->params['sign_url'] = $sign_url;
            $this->view->params['signature_request_id'] = $signature_request_id;        
            $this->view->params['client_id'] = $this->client_id; //HellSign API client id.
            $this->view->params['current_client_id'] = $current_user_id; //this is the current logged in client
            $this->view->params['signature_id'] = $signature_id;
            $this->view->params['do_sign'] = true;
        }else{
            //Write submitted form values in PDF layouts of Atlas forms.                
            self::_doHelloSignAtlasClientPDFApplication($current_user_id);
            self::_doHelloSignAtlasClientPDFContract($current_user_id);
            self::_doHelloSignAtlasClientPDFPromissory($current_user_id);
            self::_doHelloSignAtlasClientPDFCCAuthorization($current_user_id);

            //Merge all PDF forms.
            self::_doHelloSignPDFConsolidation($current_user_id);
            
            $this->view->params['do_sign'] = false;
        }
                        
        $this->view->params['signed_confirm_id'] = $signed_confirm_id;        
        $this->view->params['merge_pdf_path'] = Yii::$app->homeUrl.'uploads/atlas_pdfs/clients/'.$current_user_id."-AtlasBailForms.pdf";
        
        if($signed_confirm_id){
            $client = new HelloSign\Client($this->api_key);
            $response = $client->getFiles($signed_confirm_id);                
            $this->view->params['merge_pdf_path'] = $response->file_url;
        }
        
        $this->layout = 'atlas-hellosign';
        return $this->render('index');
    }
    
    /*
     * developer: jerome.dymosco
     * Action that will handle successful signed documents from HelloSign API portal.
     */
    public function actionSign(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $save_details = Yii::$app->request->post();
        unset($save_details['_backendCSRF']);
        
        Atlas::atlasRecordSignatureRequest( $save_details );
        $this->_sendHelloSignSuccessEmail(Yii::$app->user->identity->emailid);
        
        echo 'success';
        die();
    }
    
    /*
     * developer: jerome.dymosco
     * This will show the successful signed document via HelloSign
     */
    public function actionViewdocument(){ 
        $signature_request_id = Yii::$app->request->get('rid'); //correct value should be here.
        $client = new HelloSign\Client($this->api_key);
        $response = $client->getFiles($signature_request_id);        
        
        $this->view->params['file_url'] = $response->file_url;
        
        $this->layout = 'atlas-signed-document';
        return $this->render('index');        
    }
    
    /* 
     * developer: jerome.dymosco 
     * This will do the writing of inserted backend values in Atlas Application PDF form.
     */
    private function _doHelloSignAtlasClientPDFApplication($current_user_id){        
        $pdf_obj = new FPDI();         
        $applications = Applications::findApplicationByPersonFKId($current_user_id);
        $applications_fields = Applications::getRequiredPDFfields();
        
        $pdf_obj->AddPage();
        $pdf_obj->setSourceFile($this->pdfLayoutsFilePath."AtlasApplicationNew.pdf");
        
        $tplIdx = $pdf_obj->importPage(1);         
        $pdf_obj->useTemplate($tplIdx, null, null, 0, 0, true);
        
        // now write some text above the imported page
        $pdf_obj->SetTextColor(0,0,0);
        $pdf_obj->SetFont('Arial','B',8);
        
        //this will loop in the whole required fields and put it in the proper PDF layout fields.
        if($applications) {
            foreach($applications_fields as $key => $val){
                $pdf_obj->SetXY($val['x'], $val['y']);  
                $pdf_obj->Write(0, $applications->attributes[$key]);
            }   
                
            if(file_exists($this->pdfClientsFilePath.$current_user_id."-AtlasApplicationNew.pdf")) {
              unlink($this->pdfClientsFilePath.$current_user_id."-AtlasApplicationNew.pdf");  
            }

            $pdf_obj->Output($this->pdfClientsFilePath.$current_user_id."-AtlasApplicationNew.pdf", 'F'); 
        }
    }
    
    /* 
     * developer: jerome.dymosco 
     * This will do the writing of inserted backend values in Atlas Contract PDF form.
     */
    private function _doHelloSignAtlasClientPDFContract($current_user_id){        
        $pdf_obj = new FPDI();        
        $contract = Contract::findContractByPersonFKId($current_user_id);
        $contract_fields = Contract::getRequiredPDFfields();
        
        $pdf_obj->AddPage();
        $pdf_obj->setSourceFile($this->pdfLayoutsFilePath."AtlasCONTRACTTOINDEMNIFY.pdf");
        
        $tplIdx = $pdf_obj->importPage(1);         
        $pdf_obj->useTemplate($tplIdx, null, null, 0, 0, true);
        
        // now write some text above the imported page
        $pdf_obj->SetTextColor(0,0,0);
        $pdf_obj->SetFont('Arial','B',8);
        
        //this will loop in the whole required fields and put it in the proper PDF layout fields.
        if($contract) {
            foreach($contract_fields as $key => $val){
                
                if($key == 'contract_date'){
                    $date_str = date('y-F-jS', strtotime($contract->attributes[$key]));
                    $date = @explode('-', $date_str);
                    foreach($date as $k => $d){
                        $pdf_obj->SetXY($val[$k]['x'], $val[$k]['y']);  
                        $pdf_obj->Write(0, $d);
                    }
                }else{
                    $pdf_obj->SetXY($val['x'], $val['y']);  
                    $pdf_obj->Write(0, $contract->attributes[$key]);
                }
                
                if(isset($val['duplicate'])) {
                    $pdf_obj->SetXY($val['duplicate']['x'], $val['duplicate']['y']);  
                    $pdf_obj->Write(0, $contract->attributes[$key]);
                }
            }
                
            if(file_exists($this->pdfClientsFilePath.$current_user_id."-AtlasCONTRACTTOINDEMNIFY.pdf")) {
              unlink($this->pdfClientsFilePath.$current_user_id."-AtlasCONTRACTTOINDEMNIFY.pdf");  
            }

            $pdf_obj->Output($this->pdfClientsFilePath.$current_user_id."-AtlasCONTRACTTOINDEMNIFY.pdf", 'F'); 
        }
    }
    
    /* 
     * developer: jerome.dymosco 
     * This will do the writing of inserted backend values in Atlas Promissory PDF form.
     */
    private function _doHelloSignAtlasClientPDFPromissory($current_user_id){        
        $pdf_obj = new FPDI();         
        $promissory = Promissory::findPromissoryByPersonFKId($current_user_id);
        $promissory_fields = Promissory::getRequiredPDFfields();
        
        $pdf_obj->AddPage();
        $pdf_obj->setSourceFile($this->pdfLayoutsFilePath."PROMISSORYNOTEWEBSITE.pdf");
        
        $tplIdx = $pdf_obj->importPage(1);         
        $pdf_obj->useTemplate($tplIdx, null, null, 0, 0, true);
        
        // now write some text above the imported page
        $pdf_obj->SetTextColor(0,0,0);
        $pdf_obj->SetFont('Arial','B',8);
        
        //this will loop in the whole required fields and put it in the proper PDF layout fields.
        if($promissory) {
            foreach($promissory_fields as $key => $val){                
                $pdf_obj->SetXY($val['x'], $val['y']);  
                $pdf_obj->Write(0, $promissory->attributes[$key]);
                
                if(isset($val['duplicate'])) {
                    $pdf_obj->SetXY($val['duplicate']['x'], $val['duplicate']['y']);  
                    $pdf_obj->Write(0, $promissory->attributes[$key]);
                }
            }        
               
            if(file_exists($this->pdfClientsFilePath.$current_user_id."-PROMISSORYNOTEWEBSITE.pdf")) {
              unlink($this->pdfClientsFilePath.$current_user_id."-PROMISSORYNOTEWEBSITE.pdf");  
            }

            $pdf_obj->Output($this->pdfClientsFilePath.$current_user_id."-PROMISSORYNOTEWEBSITE.pdf", 'F'); 
        }
    }
    
    /* 
     * developer: jerome.dymosco 
     * This will do the writing of inserted backend values in Atlas CC Authorization PDF form.
     */
    private function _doHelloSignAtlasClientPDFCCAuthorization($current_user_id){        
        $pdf_obj = new FPDI();         
        $ccauthorization = CCAuthorization::findCCAuthorizationByPersonFKId($current_user_id);
        $ccauthorization_fields = CCAuthorization::getRequiredPDFfields();
        
        $pdf_obj->AddPage();
        $pdf_obj->setSourceFile($this->pdfLayoutsFilePath."AtlasCC-Authorization.pdf");
        
        $tplIdx = $pdf_obj->importPage(1);         
        $pdf_obj->useTemplate($tplIdx, null, null, 0, 0, true);
        
        // now write some text above the imported page
        $pdf_obj->SetTextColor(0,0,0);
        $pdf_obj->SetFont('Arial','B',8);
        
        //this will loop in the whole required fields and put it in the proper PDF layout fields.
        if($ccauthorization) {
            foreach($ccauthorization_fields as $key => $val){
                if($key == 'ccauthorization_card_type'){
                    $pdf_obj->SetXY($val[$ccauthorization->attributes[$key]]['x'], $val[$ccauthorization->attributes[$key]]['y']);  
                    $pdf_obj->Write(0, 'x');
                }else{
                    $pdf_obj->SetXY($val['x'], $val['y']);  
                    $pdf_obj->Write(0, $ccauthorization->attributes[$key]); 
                }
            }       
               
            if(file_exists($this->pdfClientsFilePath.$current_user_id."-AtlasCC-Authorization.pdf")) {
              unlink($this->pdfClientsFilePath.$current_user_id."-AtlasCC-Authorization.pdf");  
            }

            $pdf_obj->Output($this->pdfClientsFilePath.$current_user_id."-AtlasCC-Authorization.pdf", 'F');
        }
    }
    
    /* 
     * developer: jerome.dymosco 
     * This will do the consolidation of all Atlas PDF paperworks form.
     */
    private function _doHelloSignPDFConsolidation($current_user_id){
        $clientPDFFiles = array($this->pdfClientsFilePath.$current_user_id."-AtlasApplicationNew.pdf", 
                          $this->pdfClientsFilePath.$current_user_id."-AtlasCONTRACTTOINDEMNIFY.pdf",
                          $this->pdfClientsFilePath.$current_user_id."-PROMISSORYNOTEWEBSITE.pdf", 
                          $this->pdfClientsFilePath.$current_user_id."-AtlasCC-Authorization.pdf");
        
        $mergePDFFilename = $this->pdfClientsFilePath.$current_user_id."-AtlasBailForms.pdf";

        $cmd = "gs -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=$mergePDFFilename ";
        //Add each pdf file to the end of the command
        foreach($clientPDFFiles as $file) {
            $cmd .= $file." ";
        }
        
        $result = shell_exec($cmd);
        
        return $result;
    }


    /*
     * developer: jerome.dymosco
     */
    private function _getCurrentUserId(){
        $client_id = Yii::$app->user->identity->person_id_pk;
        $get_client_id = Yii::$app->request->get('id');
        $is_atlas_admin = Yii::$app->user->identity->is_atlas_admin;
                
        if($is_atlas_admin == 'yes' && !empty($get_client_id)){
            return Yii::$app->request->get('id');
        }
        
        return $client_id;
    }
    
    /*
     * developer: jerome.dymosco
     */
    private function _concatParams(){
        if(Yii::$app->request->get('id')){
            return '?id='.Yii::$app->request->get('id');
        }
    }
    
    /*
     * developer: jerome.dymosco
     * This can be improve later, let's just do it this way for now.
     */
    private function _restrictClientAccessOther(){
        $is_atlas_admin = Yii::$app->user->identity->is_atlas_admin;
        $get_client_id = Yii::$app->request->get('id');
        
        if(($is_atlas_admin == 'no' || $is_atlas_admin != 'yes') && !empty($get_client_id)){            
            die("You don't have access to perform this action.");
        }                
    }
    
    
    /*
     * developer: jerome.dymosco    
     */
    private function _sendHelloSignSuccessEmail($client_email){        
        Yii::$app->mailer->compose()
            ->setFrom('noreply@mindts.com')
            ->setTo($client_email)
            ->setCc('dani@xerosoft.com')
            ->setSubject('Atlas Bail Bonds - Paperworks - Signed')
            ->setTextBody('Client just signed Atlas Bail Bonds required paperworks.')            
            ->send();
    }
    
    private static function _getSidebarFormMenuSettings(){
        $settings = array();
        $current_user_id = Yii::$app->user->identity->person_id_pk;
        $is_atlas_admin = Yii::$app->user->identity->is_atlas_admin;
        
        if($is_atlas_admin == 'no'){
            $settings['ccauthorization'] = CCAuthorization::findCCAuthorizationByPersonFKId($current_user_id);
            $settings['promissory'] = Promissory::findPromissoryByPersonFKId($current_user_id);
            $settings['contract'] = Contract::findContractByPersonFKId($current_user_id);
            $settings['application'] = Applications::findApplicationByPersonFKId($current_user_id);
            
            return $settings;
        }
                
        return false;
    }
    
}
