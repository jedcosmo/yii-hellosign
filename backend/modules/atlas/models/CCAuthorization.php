<?php
/*
 * developer: jerome.dymosco
 */
namespace backend\modules\atlas\models;

use Yii;

class CCAuthorization extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'atlas_ccauthorization';
    }    
    
    public function rules()
    {
        return array(
            array( 
              array('ccauthorization_premiunm_amount', 'ccauthorization_premiunm_amount_text', 'ccauthorization_security_code', 'ccauthorization_card_type', 'ccauthorization_card_name', 'ccauthorization_card_number',
              'ccauthorization_card_expiration', 'ccauthorization_billing_address', 'ccauthorization_billing_city', 'ccauthorization_state',
              'ccauthorization_zip_code', 'ccauthorization_date_signed'), 'required'),              
              array('date_created', 'default', 'value' => date('Y-m-d h:i:s'))
        );
    }
        
    public static function findCCAuthorizationByPersonFKId($Id){
        $ccauthorization = self::find()->where("`client_id_fk` = $Id")->one();
        return $ccauthorization;
    }
    
    /*
     * method that will handle and return all required field keys and coordinate x,y values for PDF setup.
     */
    public static function getRequiredPDFfields(){
        $fields = array(
              'ccauthorization_premiunm_amount_text' => array('x' => 62, 'y' => 51),
              'ccauthorization_premiunm_amount' => array('x' => 88, 'y' => 61),
              'ccauthorization_security_code' => array('x' => 45, 'y' => 125), 
              'ccauthorization_card_type' => array( 
                                                    'mastercard' => array('x' => 113, 'y' => 125),
                                                    'visa' => array('x' => 140, 'y' => 125),
                                                    'amex' => array('x' => 156, 'y' => 125),
                                                    'discover' => array('x' => 175, 'y' => 125),
                                                   
                                                  ),
              'ccauthorization_card_name' => array('x' => 45, 'y' => 138), 
              'ccauthorization_card_number' => array('x' => 45, 'y' => 145),              
              'ccauthorization_card_expiration' => array('x' => 155, 'y' => 145),
              'ccauthorization_billing_address' => array('x' => 48, 'y' => 154),  
              'ccauthorization_billing_city' => array('x' => 38, 'y' => 162), 
              'ccauthorization_state' => array('x' => 105, 'y' => 162),
              'ccauthorization_zip_code' => array('x' => 150, 'y' => 162),
              'ccauthorization_date_signed' => array('x' => 165, 'y' => 198)              
            );
        
        return $fields;
    }
}

