<?php
/*
 * developer: jerome.dymosco
 */
namespace backend\modules\atlas\models;

use Yii;

class Applications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'altas_applications';
    }    
    
    public function rules()
    {
        return array(
            array( 
              array('defendant_name', 'defendant_dob', 'defendant_us_citizen', 'defendant_dl',
              'defendant_dob', 'defendant_ssn', 'defendant_phone', 'applicant_name',
              'applicant_dob', 'applicant_ssn', 'applicant_phone','applicant_current_address',
              'applicant_current_city', 'applicant_current_state', 'applicant_current_zip_code', 'applicant_current_homeownership',
              'applicant_current_monthly_payment', 'applicant_current_how_long', 'applicant_previous_address', 'applicant_previous_city',
              'applicant_previous_state', 'applicant_previous_zip_code', 'applicant_previous_homeownership', 'applicant_previous_monthly_payment',
              'applicant_previous_how_long', 'employment_current_employer', 'employment_employer_address', 'employment_how_long',
              'employment_email_address', 'employment_city','employment_state', 'employment_zip_code',
              'employment_position', 'employment_salary_type', 'employment_annual_income', 'personal_reference_name',
              'personal_reference_address', 'personal_reference_city', 'personal_reference_state', 'personal_reference_zip_code',
              'personal_reference_phone', 'personal_reference_relationship', 'references_name', 'references_address',
              'references_phone', 'signature_date'), 'required'),
              array('employment_email_address', 'email'),
              array('date_created', 'default', 'value' => date('Y-m-d h:i:s'))
        );
    }
        
    public static function findApplicationByPersonFKId($Id){
        $application = self::find()->where("`client_id_fk` = $Id")->one();
        return $application;
    }
    
    /*
     * method that will handle and return all required field keys and coordinate x,y values for PDF setup.
     */
    public static function getRequiredPDFfields(){
        $fields = array(
              'defendant_name' => array('x' => 35, 'y' => 34),               
              'defendant_us_citizen' => array('x' => 80, 'y' => 40), 
              'defendant_dl' => array('x' => 120, 'y' => 40),
              'defendant_dob' => array('x' => 45, 'y' => 46), 
              'defendant_ssn' => array('x' => 90, 'y' => 46), 
              'defendant_phone' => array('x' => 155, 'y' => 46), 
              'applicant_name' => array('x' => 34, 'y' => 59),
              'applicant_dob' => array('x' => 45, 'y' => 67), 
              'applicant_ssn' => array('x' => 95, 'y' => 67), 
              'applicant_phone' => array('x' => 158, 'y' => 67),
              'applicant_current_address' => array('x' => 45, 'y' => 75),
              'applicant_current_city' => array('x' => 35, 'y' => 83), 
              'applicant_current_state' => array('x' => 95, 'y' => 83), 
              'applicant_current_zip_code' => array('x' => 158, 'y' => 83), 
              'applicant_current_homeownership' => array('x' => 35, 'y' => 92),
              'applicant_current_monthly_payment' => array('x' => 110, 'y' => 91), 
              'applicant_current_how_long' => array('x' => 175, 'y' => 91), 
              'applicant_previous_address' => array('x' => 45, 'y' => 99), 
              'applicant_previous_city' => array('x' => 35, 'y' => 107),
              'applicant_previous_state' => array('x' => 95, 'y' => 107), 
              'applicant_previous_zip_code' => array('x' => 158, 'y' => 107), 
              'applicant_previous_homeownership' => array('x' => 35, 'y' => 116), 
              'applicant_previous_monthly_payment' => array('x' => 110, 'y' => 115),
              'applicant_previous_how_long' => array('x' => 175, 'y' => 115), 
              'employment_current_employer' => array('x' => 45, 'y' => 127), 
              'employment_employer_address' => array('x' => 45, 'y' => 136), 
              'employment_how_long' => array('x' => 175, 'y' => 136),
              'employment_phone' => array('x' => 45, 'y' => 144),
              'employment_email_address' => array('x' => 95, 'y' => 144),
              'employment_fax' => array('x' => 158, 'y' => 144),
              'employment_city' => array('x' => 35, 'y' => 152),
              'employment_state' => array('x' => 95, 'y' => 152), 
              'employment_zip_code' => array('x' => 158, 'y' => 152),
              'employment_position' => array('x' => 35, 'y' => 160), 
              'employment_salary_type' => array('x' => 110, 'y' => 160), 
              'employment_annual_income' => array('x' => 160, 'y' => 160), 
              'personal_reference_name' => array('x' => 75, 'y' => 174),
              'personal_reference_address' => array('x' => 35, 'y' => 182), 
              'personal_reference_city' => array('x' => 35, 'y' => 190), 
              'personal_reference_state' => array('x' => 95, 'y' => 190), 
              'personal_reference_zip_code' => array('x' => 145, 'y' => 190),
              'personal_reference_phone' => array('x' => 175, 'y' => 190), 
              'personal_reference_relationship' => array('x' => 42, 'y' => 198), 
              'references_name' => array('x' => 35, 'y' => 220), 
              'references_address' => array('x' => 95, 'y' => 220),
              'references_phone' => array('x' => 175, 'y' => 220),
              'signature_date' => array('x' => 175, 'y' => 246)
            );
        
        return $fields;
    }
}

