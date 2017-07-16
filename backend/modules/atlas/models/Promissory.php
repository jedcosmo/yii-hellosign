<?php
/*
 * developer: jerome.dymosco
 */
namespace backend\modules\atlas\models;

use Yii;

class Promissory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'atlas_promissory';
    }    
    
    public function rules()
    {
        return array(
            array( 
              array('promissory_date', 'promissory_defendant_name', 'promissory_note_amount', 'promissory_city', 'promissory_state',
              'promissory_principal_sum_text', 'promissory_principal_sum_numbers', 'promissory_defendant_address', 'promissory_payment_amount', 'promissory_weekly_payment_start_date',
              'promissory_debtor_name', 'promissory_debtor_date', 'promissory_witness_name','promissory_witness_date'), 'required'),              
              array('date_created', 'default', 'value' => date('Y-m-d h:i:s'))
        );
    }
        
    public static function findPromissoryByPersonFKId($Id){
        $application = self::find()->where("`client_id_fk` = $Id")->one();
        return $application;
    }
    
    /*
     * method that will handle and return all required field keys and coordinate x,y values for PDF setup.
     */
    public static function getRequiredPDFfields(){
        $fields = array(
              'promissory_date' => array('x' => 18, 'y' => 26), 
              'promissory_note_amount' => array('x' => 108, 'y' => 26),
              'promissory_city' => array('x' => 140, 'y' => 26), 
              'promissory_state' => array('x' => 170, 'y' => 26),              
              'promissory_principal_sum_text' => array('x' => 60, 'y' => 45, 'duplicate' => array('x' => 48, 'y' => 51)),
              'promissory_principal_sum_numbers' => array('x' => 108, 'y' => 45, 'duplicate' => array('x' => 100, 'y' => 52)),  
              'promissory_defendant_name' => array('x' => 75, 'y' => 58, 'duplicate' => array('x' => 78, 'y' => 68)), 
              'promissory_defendant_address' => array('x' => 56, 'y' => 61, 'duplicate' => array('x' => 54, 'y' => 71)),
              'promissory_payment_amount' => array('x' => 40, 'y' => 81),
              'promissory_weekly_payment_start_date' => array('x' => 148, 'y' => 81),
              'promissory_debtor_name' => array('x' => 102, 'y' => 231),
              'promissory_debtor_date' => array('x' => 155, 'y' => 231),
              'promissory_witness_name' => array('x' => 112, 'y' => 250),
              'promissory_witness_date' => array('x' => 165, 'y' => 250),
            );
        
        return $fields;
    }
}

