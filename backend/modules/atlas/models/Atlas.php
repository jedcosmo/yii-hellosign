<?php

namespace backend\modules\atlas\models;

use Yii;

/**
 * This is the model class for table "bpr_company".
 *
 * @property integer $company_id_pk
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $pobox
 * @property integer $city_id_fk
 * @property integer $state_id_fk
 * @property string $zip_postalcode
 * @property integer $country_id_fk
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Atlas extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_company';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'address1',], 'required'],
            [['name','address1', 'address2'], 'string'],
			[['name'], 'string', 'max' => 100],
			[['address1', 'address2'], 'string', 'max' => 120],
            [['pobox', 'zip_postalcode'], 'string', 'max' => 20, 'min'=>2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'company_id_pk' => 'Company ID',
            'name' => 'Name',
            'address1' => 'Address1',
            'address2' => 'Address2',
            'pobox' => 'PoBox',
            'city_id_fk' => 'City',
            'state_id_fk' => 'State',
            'zip_postalcode' => 'Zip Postalcode',
            'country_id_fk' => 'Country',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person Id Fk',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted'=>'Delete Reason',
        ];
    }
	
	public function showCityName($cityid)
	{
		$params = [':cityid' => $cityid];
		$State = Yii::$app->db->createCommand('SELECT name FROM bpr_city WHERE city_id_pk=:cityid', $params)->queryOne();
			if($State['name'])
    			$name = htmlentities($State['name']);
			else
				$name = '';
		return $name;
	}
	
	public function showStateName($stateid)
	{
		$params = [':stateid' => $stateid];
		$State = Yii::$app->db->createCommand('SELECT name FROM bpr_state WHERE state_id_pk=:stateid', $params)->queryOne();
			if($State['name'])
    			$name = htmlentities($State['name']);
			else
				$name = '';
		return $name;
	}
	
	public function showCountryName($countryid)
	{				
		$params = [':countryid' => $countryid];
		$Country = Yii::$app->db->createCommand('SELECT name FROM bpr_country WHERE country_id_pk=:countryid', $params)->queryOne();
			if($Country['name'])
    			$name = htmlentities($Country['name']);
			else
				$name = '';
		return $name;
	}
        
        /*
         * developer: jerome.dymosco
         * This will create record everytime clients reviews a form.
         */
        public static function atlasRecordSignatureRequest($data){
            return Yii::$app->db->createCommand()->insert('atlas_signature_request', $data)->execute();
        }
        
        /*
         * developer: jerome.dymosco
         * This will get record of specific client signed HelloSign signature request.
         */
        public static function atlasGetRecordSignatureRequest($client_id){
            $params = [':client_id' => $client_id];
            $result = Yii::$app->db->createCommand('SELECT signature_request_id FROM atlas_signature_request WHERE client_id=:client_id', $params)->queryOne();
            
            return (isset($result['signature_request_id']) ? $result['signature_request_id'] :  false);
        }
}
