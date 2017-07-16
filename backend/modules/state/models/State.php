<?php

namespace backend\modules\state\models;

use Yii;


/**
 * This is the model class for table "bpr_state".
 *
 * @property integer $state_id_pk
 * @property string $name
 * @property integer $country_id_fk
 */

class State extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_state';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'country_id_fk'], 'required'],
            [['country_id_fk'], 'integer'],
            [['name'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'state_id_pk' => 'State ID',
            'name' => 'Name',
            'country_id_fk' => 'Country',
			'reasonIsDeleted' => 'Delete Reason',
        ];
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
