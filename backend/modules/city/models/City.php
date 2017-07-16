<?php

namespace backend\modules\city\models;

use Yii;

/**
 * This is the model class for table "bpr_city".
 *
 * @property integer $city_id_pk
 * @property string $name
 * @property integer $state_id_fk
 * @property integer $country_id_fk
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'state_id_fk', 'country_id_fk'], 'required'],
            [['state_id_fk', 'country_id_fk'], 'integer','message'=>'State cannot be blank'],
			[['country_id_fk'], 'integer','message'=>'Country cannot be blank'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'city_id_pk' => 'City ID',
            'name' => 'City Name',
            'state_id_fk' => 'State',
            'country_id_fk' => 'Country',
        ];
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
}
