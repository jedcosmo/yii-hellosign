<?php

namespace backend\modules\product\models;

use Yii;

/**
 * This is the model class for table "bpr_product".
 *
 * @property integer $product_id_pk
 * @property string $name
 * @property integer $company_id_fk
 * @property string $part
 * @property string $code
 * @property integer $unit_id_fk
 * @property integer $qty
 * @property integer $document_id_fk
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'company_id_fk', 'code', 'unit_id_fk'], 'required'],
			[['name'], 'string', 'max' => 150],
            [['code'], 'string', 'max' => 20],
            [['code'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'product_id_pk' => 'Product ID',
            'name' => 'Name',
            'company_id_fk' => 'Company',
            'part' => 'Part',
            'code' => 'Product Code',
            'unit_id_fk' => 'Unit',
            'qty' => 'Qty',
            'document_id_fk' => 'Document',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted'=>'Delete Reason',
        ];
    }
	
	public function getProductDocument($docid)
	{
		$params = [':document_id_pk' => $docid];
		$Docuements = Yii::$app->db->createCommand('SELECT docname FROM bpr_documents WHERE document_id_pk=:document_id_pk', $params)->queryOne();
			if($Docuements['docname'])
    			$name = $Docuements['docname'];
			else
				$name = '';
		return $name;
	}
	
	public function showCompanyName($companyid)
	{
		$params = [':company_id_pk' => $companyid];
		$Company = Yii::$app->db->createCommand('SELECT name FROM bpr_company WHERE company_id_pk=:company_id_pk', $params)->queryOne();
			if($Company['name'])
    			$name = htmlentities($Company['name']);
			else
				$name = '';
		return $name;
	}
	
	public function showUnitName($unitid)
	{
		$params = [':unit_id_pk' => $unitid];
		$Company = Yii::$app->db->createCommand('SELECT name FROM bpr_unit WHERE unit_id_pk=:unit_id_pk', $params)->queryOne();
			if($Company['name'])
    			$name = htmlentities($Company['name']);
			else
				$name = '';
		return $name;
	}
	
	public function showDocument($docid)
	{
		if($docid>0)
		{
			return "<a title='View Document' href='".Yii::$app->urlManager->baseUrl."/uploads/documents/".$this->getProductDocument($docid)."' target='_blank'><span class='glyphicon glyphicon-file'></span>&nbsp; View Document</a>";
		}
		else
		{
			return '';
		}
	}
}
