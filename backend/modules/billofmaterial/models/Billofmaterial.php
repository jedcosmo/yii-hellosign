<?php

namespace backend\modules\billofmaterial\models;

use Yii;

/**
 * This is the model class for table "bpr_bill_of_material".
 *
 * @property integer $bom_id_pk
 * @property string $material_name
 * @property double $qty_branch
 * @property integer $qb_unit_id_fk
 * @property string $composition
 * @property integer $com_unit_id_fk
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 * @popperty integer $mpr_defination_id_fk
 */
class Billofmaterial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_bill_of_material';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_name', 'qty_branch', 'qb_unit_id_fk', 'composition', 'com_unit_id_fk', 'isDeleted', 'addedby_person_id_fk', 'created_datetime'], 'required'],
            [['qty_branch'], 'number'],
            [['qb_unit_id_fk', 'com_unit_id_fk', 'addedby_person_id_fk'], 'integer'],
            [['material_name','material_id'], 'string', 'max' => 100],
			[['composition'], 'string', 'max' => 255],
			[['CAS_Number','Control_Number'], 'string', 'max' => 20],
			[['vendor_id','vendor_name','vendor_lot','storage_condition','temperature_condition','total_shelf_life'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bom_id_pk' => 'Material ID',
            'material_name' => 'Material Name',
            'qty_branch' => 'Unit Size',
            'qb_unit_id_fk' => 'Unit',
            'composition' => 'Composition',
            'com_unit_id_fk' => 'Composition Unit',
			'material_id' => 'Material ID',
			'material_type_id_fk' => 'Material Type',
			'product_part' => 'Part#',
			'vendor_id' => 'Vendor ID',
			'vendor_name' => 'Vendor Name',
			'vendor_lot' => 'Vendor Lot',
			'price_per_unit' => 'Price Per Unit',
			'maximum_qty' => 'Maximum Qty',
			'storage_condition' => 'Storage Condition',
			'temperature_condition' => 'Temperature Condition',
			'country_id_fk' =>'Country of Origin',
			'total_shelf_life' => 'Total Shelf Life',
			'material_test_status' => 'Material Test Status',
			'material_safety_data_sheet' => 'Material Safety Data Sheet',
			'environmental_protection_agency' => 'Environmental Protection Agency',
			'select_a_file' => 'File',
			'CAS_Number' => 'Chemical Abstracts Service(CAS) number #',
			'Control_Number' => 'Control Number #',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'mpr_defination_id_fk' => 'MPR Definition ID',
			'reasonIsDeleted' => 'Delete Reason',
        ];
    }
	
	public function showUnitName($unitid)
	{
		$params = [':unit_id_pk' => $unitid];
		$Company = Yii::$app->db->createCommand('SELECT unit_id_pk, name, description, symbols, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_unit WHERE unit_id_pk=:unit_id_pk', $params)->queryOne();
			if($Company['name'])
    			$name = $Company['name'];
			else
				$name = '';
		return $name;
	}
	
	public function showDataSheet($symbol)
	{
		if($symbol)
		{
			return "<a title='View File' href='".Yii::$app->urlManager->baseUrl."/uploads/billofmaterials/".$symbol."' target='_blank'><span class='glyphicon glyphicon-file'></span>&nbsp; View File</a>";
		}
		else
		{
			return '';
		}
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
	
	public function showMaterialType($material_type_id_fk)
	{				
		$params = [':material_type_id_pk' => $material_type_id_fk];
		$materialType = Yii::$app->db->createCommand('SELECT type_name FROM bpr_material_type WHERE material_type_id_pk=:material_type_id_pk', $params)->queryOne();
			if($materialType['type_name'])
    			$name = htmlentities($materialType['type_name']);
			else
				$name = '';
		return $name;
	}
}
