<?php

namespace backend\modules\minstructions\models;

use Yii;

/**
 * This is the model class for table "bpr_manufacturing_instruction".
 *
 * @property integer $mi_id_pk
 * @property string $mi_step
 * @property string $mi_action
 * @property integer $unit_id_fk
 * @property string $mi_range
 * @property string $target
 * @property string $perfomer
 * @property string $verifier
 * @property integer $document_id_fk
 * @property string $isDeleted
 * @property integer $addedby_persona_id_fk
 * @property string $created_datetime
 */
class Minstructions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_manufacturing_instruction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mi_step'], 'required'],
            [['mi_step'], 'string', 'max' => 100],
			[['mi_action'], 'string', 'max' =>500],
			[['mi_range'], 'string', 'max' => 100],
			[['target'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mi_id_pk' => 'ID',
            'mi_step' => 'Step',
            'mi_action' => 'Action',
            'unit_id_fk' => 'Unit',
            'mi_range' => 'Range',
            'target' => 'Target',
            'perfomer' => 'Performer',
            'verifier' => 'Verifier',
            'document_id_fk' => 'Document ID',
            'isDeleted' => 'Is Deleted',
            'addedby_persona_id_fk' => 'Addedby Persona ID',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted' => 'Delete Reason',
        ];
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
}
