<?php

namespace backend\modules\mprdefinition\models;

use Yii;

use backend\modules\product\models\Product;
/**
 * This is the model class for table "bpr_mpr_defination".
 *
 * @property integer $mpr_defination_id_pk
 * @property integer $product_id_fk
 * @property string $product_code
 * @property string $MPR_version
 * @property string $product_part
 * @property string $author
 * @property string $product_name
 * @property string $formulation_id
 * @property double $product_strength
 * @property double $batch_size
 * @property string $MPR_unit_id
 * @property string $theoritical_yield
 * @property integer $company_id_fk
 * @property string $purpose
 * @property string $scope
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Mprdefination extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_mpr_defination';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_code',  'author', 'formulation_id', 'product_strength', 'batch_size', 'MPR_unit_id', 'theoritical_yield', 'company_id_fk', 'purpose', 'scope'], 'required'],
            [['formulation_id',], 'string', 'max' => 20],
			[['theoritical_yield','scope','purpose'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mpr_defination_id_pk' => 'MPR Defination ID',
            'product_id_fk' => 'Product ID',
            'product_code' => 'Product Code',
            'MPR_version' => 'MPR Version',
            'product_part' => 'Product Part',
            'author' => 'Author',
            'product_name' => 'Product Name',
            'formulation_id' => 'Formulation ID',
            'product_strength' => 'Product Strength',
            'batch_size' => 'Batch Size',
            'MPR_unit_id' => 'MPR Unit',
            'theoritical_yield' => 'Theoritical Yield',
            'company_id_fk' => 'Company',
            'purpose' => 'Purpose',
            'scope' => 'Scope',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
        ];
    }	
	
	public function showCompanyName($companyid)
	{
		$params = [':company_id_pk' => $companyid];
		$Company = Yii::$app->db->createCommand('SELECT name FROM bpr_company WHERE company_id_pk=:company_id_pk', $params)->queryOne();
			if($Company['name'])
    			$name = $Company['name'];
			else
				$name = '';
		return $name;
	}
	
	public function showUnitName($unitid)
	{
		$params = [':unit_id_pk' => $unitid];
		$Company = Yii::$app->db->createCommand('SELECT name FROM bpr_unit WHERE unit_id_pk=:unit_id_pk', $params)->queryOne();
			if($Company['name'])
    			$name = $Company['name'];
			else
				$name = '';
		return $name;
	}
	
	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['product_id_pk' => 'product_id_fk']);
	}
}
