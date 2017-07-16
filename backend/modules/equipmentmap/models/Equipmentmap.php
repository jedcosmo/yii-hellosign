<?php

namespace backend\modules\equipmentmap\models;

use Yii;
use backend\modules\equipment\models\Equipment;
/**
 * This is the model class for table "bpr_equipment_map".
 *
 * @property integer $equipment_map_id_pk
 * @property integer $mpr_defination_id_fk
 * @property integer $product_id_fk
 * @property string $product_code
 * @property integer $equipment_id_fk
 */
class Equipmentmap extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	public $eqp_name;
	 
    public static function tableName()
    {
        return 'bpr_equipment_map';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_id_fk'], 'required'],
			[['activity','dept_assigned_to','cleaning_agent'], 'string', 'max'=>255],
			[['comments'], 'string', 'max'=>500]
			
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equipment_map_id_pk' => 'Equipment ID',
            'mpr_defination_id_fk' => 'Mpr Defination ID',
            'product_id_fk' => 'Product ID',
            'product_code' => 'Product Code',
            'equipment_id_fk' => 'Equipment',
			'equipment_name' => 'Equipment Name',
			'equipment_model' => 'Equipment Model',
			'calibration_due_date' => 'Calibration Due Date',
			'preventive_m_due_date' => 'Preventive Maintenance Due Date',
			'activity' => 'Activity',
			'start_date_time' => 'Start Date & Time',
			'end_date_time' => 'End Date & Time',
			'dept_assigned_to' => 'Dept. Assigned To',
			'cleaning_agent' => 'Cleaning Agent',
			'batch' => 'Batch#',
			'product_name' => 'Product Name',
			'product_part' => 'Product Part',
			'attachment' => 'Attachment',
			'comments' => 'Comments',
			'operator_signature' => 'Operator Signature',
			'reasonIsDeleted' => 'Delete Reason',
        ];
    }
	
	public function showEquipmentName($eqpid)
	{
		$params = [':equipment_id_pk' => $eqpid];
		$Equipments = Yii::$app->db->createCommand('SELECT name FROM bpr_equipment WHERE equipment_id_pk=:equipment_id_pk', $params)->queryOne();
			if($Equipments['name'])
    			$name = $Equipments['name'];
			else
				$name = '';
		return $name;
	}
	
	public function getEquipment()
	{
		return $this->hasOne(Equipment::className(), ['equipment_id_pk' => 'equipment_id_fk']);
	}
	
	public function showAttachment($symbol)
	{
		if($symbol)
		{
			return "<a title='View File' href='".Yii::$app->urlManager->baseUrl."/uploads/equipmentmap/".$symbol."' target='_blank'><span class='glyphicon glyphicon-file'></span>&nbsp; View File</a>";
		}
		else
		{
			return '';
		}
	}
}
