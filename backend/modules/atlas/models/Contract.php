<?php
/*
 * developer: jerome.dymosco
 */
namespace backend\modules\atlas\models;

use Yii;

class Contract extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'atlas_contract';
    }    
    
    public function rules()
    {
        return array(
            array( 
              array('contract_jail_from', 'contract_person_name', 'contract_bond_sum', 'contract_date',
              'contract_current_address'), 'required'),              
              array('date_created', 'default', 'value' => date('Y-m-d h:i:s'))
        );
    }
        
    public static function findContractByPersonFKId($Id){
        $contract = self::find()->where("`client_id_fk` = $Id")->one();
        return $contract;
    }
    
    /*
     * method that will handle and return all required field keys and coordinate x,y values for PDF setup.
     */
    public static function getRequiredPDFfields(){
        $fields = array(
              'contract_jail_from' => array('x' => 12, 'y' => 36, 'duplicate' => array('x' => 12, 'y' => 62)),               
              'contract_person_name' => array('x' => 148, 'y' => 36, 'duplicate' => array('x' => 12, 'y' => 66)), 
              'contract_bond_sum' => array('x' => 154, 'y' => 41, 'duplicate' => array('x' => 30, 'y' => 70)),
              'contract_date' => array( array('x' => 158, 'y' => 212), array('x' => 120, 'y' => 212), array('x' => 65, 'y' => 212)), /* Y-m-d array key coordinated values order */
              'contract_current_address' => array('x' => 78, 'y' => 236)              
            );
        
        return $fields;
    }
}

