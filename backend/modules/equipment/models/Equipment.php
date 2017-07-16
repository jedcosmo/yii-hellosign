<?php

namespace backend\modules\equipment\models;

use Yii;

/**
 * This is the model class for table "bpr_equipment".
 *
 * @property integer $equipment_id_pk
 * @property string $name
 * @property string $model
 * @property string $serial
 * @property string $caliberation_due_date
 * @property string $preventive_m_due_date
 * @property integer $document_id_fk
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Equipment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_equipment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'model', 'serial', 'caliberation_due_date', 'preventive_m_due_date'], 'required'],
			[['name'], 'string', 'max' => 200],
			[['model','serial'], 'string', 'max' => 50],
            [['name', 'model', 'serial'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'equipment_id_pk' => 'Equipment ID',
            'name' => 'Name',
            'model' => 'Model',
            'serial' => 'Serial',
            'caliberation_due_date' => 'Calibration Date',
            'preventive_m_due_date' => 'Preventive Maintenance Date',
            'document_id_fk' => 'Document',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted'=>'Delete Reason',
        ];
    }
	
	public function getEquipmentDocument($docid)
	{
		$params = [':document_id_pk' => $docid];
		$Docuements = Yii::$app->db->createCommand('SELECT docname FROM bpr_documents WHERE document_id_pk=:document_id_pk', $params)->queryOne();
			if($Docuements['docname'])
    			$name = $Docuements['docname'];
			else
				$name = '';
		return $name;
	}
	
	public function showDocument($docid)
	{
		if($docid>0)
		{
			return "<a title='View Document' href='".Yii::$app->urlManager->baseUrl."/uploads/documents/".$this->getEquipmentDocument($docid)."' target='_blank'><span class='glyphicon glyphicon-file'></span>&nbsp; View Document</a>";
		}
		else
		{
			return '';
		}
	}
}
