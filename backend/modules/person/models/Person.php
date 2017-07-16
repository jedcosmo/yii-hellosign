<?php

namespace backend\modules\person\models;
use backend\modules\rolemanagement\models\Rolemanagement;

use Yii; 

/**
 * This is the model class for table "bpr_person".
 *
 * @property integer $person_id_pk
 * @property integer $role_id_fk
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property string $fax
 * @property string $address
 * @property integer $city_id_fk
 * @property integer $state_id_fk
 * @property string $pobox
 * @property string $zip_pincode
 * @property integer $country_id_fk
 * @property string $emailid
 * @property string $user_name
 * @property string $password
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Person extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone', 'address','country_id_fk', 'state_id_fk','city_id_fk','zip_pincode', 'emailid', 'user_name_person','role_id_fk'], 'required'],
			[['address'], 'string', 'max' => 120],
            [['first_name', 'last_name'], 'string', 'max' => 50],
			[['first_name', 'last_name'], 'match','pattern'=>'/^[a-zA-Z]*$/', 'message' => "Only alphabets are allowed"],
			[['pobox'], 'string', 'max' => 20, 'min' => 2],
			[['zip_pincode'], 'string', 'max' => 20, 'min' => 2],
			[['pobox','zip_pincode'], 'match','pattern'=>'/^[a-zA-Z0-9]*$/', 'message' => "Only aplhanumeric characters are allowed"],
			[['phone','fax'], 'string', 'max' => 20, 'min' => 6],
			[['phone','fax'], 'match','pattern'=>'/^((\+)?[1-9]{1,2})?([-\s\.])?((\(\d{1,4}\))|\d{1,4})(([-\s\.])?[0-9]{1,12}){1,2}$/','message' => "Only numbers with +, ( , ) are allowed"],
			[['emailid'], 'email'],
            [['emailid'], 'string', 'max' => 60],
			[['user_name_person'], 'string', 'max' => 16, 'min' => 5],
			[['emailid'],'unique'],
			[['user_name_person'],'unique'],
        ];
    }
	
    public function custom_required_validation($attribute, $params)
    {		
		if($this->$attribute=='' || $this->$attribute == ' ')
			$this->addError($attribute,$this->$attribute.' can not be blank');
		else
			return true;
    }
	
	public function custom_required($attribute, $params)
    {		
		if(!$this->isNewRecord)
		{
			if($this->password_person=='' && $this->password_person==' ')
				$this->addError($attribute,$this->$attribute.' can not be blank');
			else
				return true;
		}
		else
		{
			return true;
		}
		
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'person_id_pk' => 'ID',
            'role_id_fk' => 'Role',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'address' => 'Address',
            'city_id_fk' => 'City',
            'state_id_fk' => 'State',
            'pobox' => 'PO Box',
            'zip_pincode' => 'Zip/Pincode',
            'country_id_fk' => 'Country',
            'emailid' => 'Email ID',
            'user_name_person' => 'Username',
            'password_person' => 'Password',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person',
			'reasonIsDeleted'=>'Delete Reason',
            'created_datetime' => 'Added Datetime',
			'super_company_id_fk' => 'Company',
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
	
	public function showRoleName($roleid)
	{				
		$params = [':roleid' => $roleid];
		$Role = Yii::$app->db->createCommand('SELECT name FROM bpr_role WHERE role_id_pk=:roleid', $params)->queryOne();
			if($Role['name'])
    			$name = htmlentities($Role['name']);
			else
				$name = '';
		return $name;
	}
	
	public function getRolemanagement()
	{
		return $this->hasOne(Rolemanagement::className(), ['role_id_pk' => 'role_id_fk']);
	}
}
