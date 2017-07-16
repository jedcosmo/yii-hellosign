<?php

namespace backend\modules\formulation\models;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

use Yii;


class Formulation extends \yii\db\ActiveRecord
{
	public $weight_by_weight;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_formulation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['material_name', 'material_part','formulation_percentage', 'isDeleted', 'addedby_person_id_fk', 'created_datetime'], 'required'],
			[['material_name'], 'string', 'max' => 100],
			[['material_part'], 'string', 'max' => 30],
			[['formulation_percentage'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'f_id_pk' => 'Formulatiion ID',
            'material_name' => 'Ingredient Name',
            'material_part' => 'Part #',
            'formulation_percentage' => 'Formulation %',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'mpr_defination_id_fk' => 'MPR Definition ID',
			'reasonIsDeleted' => 'Delete Reason',
			'weight_by_weight' => 'W/W',
        ];
    }
	
	public static function fPercentageTotal($provider, $fieldName)
	{
		$total=0;
		foreach($provider as $item){
			$total+=$item[$fieldName];
		}
		if($total!=0 && ($total>100 || $total<100))
		{
			return '<span style="color:#FB3738;">'.$total.'  %</span>';
		}
		else if($total==100)
		{
			return '<span style="color:#85C08A;">'.$total.'  %</span>';
		}
		else
		{
			return $total.'  %';
		}
	}
	
	public static function kgWeightTotal($provider, $fieldName)
	{
		$total=0;
		foreach($provider as $item)
		{	
			$MPRDetails = getCommonFunctions::get_MPR_Definition_Details($item['mpr_defination_id_fk']);
			if(is_array($MPRDetails) && count($MPRDetails)>0)
			{
				$unitnm = getCommonFunctions::getFieldNameValue("bpr_unit","name","unit_id_pk",$MPRDetails['MPR_unit_id']);
				$batchSizeInKG = CommonFunctions::unitConversionToKG($unitnm, $MPRDetails['batch_size']);
			}
			$kgWeight = (($batchSizeInKG*floatval($item['formulation_percentage']))/100);
			$kgWeight = round($kgWeight,2);
			$total += $kgWeight;
		}
		$total = round($total,2);
		return $total.'  KG';
	}	
}
