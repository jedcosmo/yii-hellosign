<?php

namespace backend\modules\bprrecords\models;
use Yii;
use backend\modules\mprdefinition\models\Mprdefination;
use backend\modules\product\models\Product;

/**
 * This is the model class for table "bpr_batch_processing_records".
 *
 * @property integer $bpr_id_pk
 * @property string $batch
 * @property string $manufacturing_date
 * @property integer $product_id_fk
 * @property string $product_code
 * @property string $mpr_version
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Bprrecords extends \yii\db\ActiveRecord
{
	public $status;
	public $status_approver;
	public $time_stamp;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_batch_processing_records';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
          
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bpr_id_pk' => 'Bpr ID',
            'batch' => 'Batch',
            'manufacturing_date' => 'Manufacturing Date',
            'mpr_definition_id_fk' => 'MPR Definition ID',
            'product_code' => 'Product Code',
            'mpr_version' => 'Mpr Version',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
        ];
    }
	
	public function getMprdefination()
	{
		return $this->hasOne(Mprdefination::className(), ['mpr_defination_id_pk' => 'mpr_definition_id_fk']);
	}
	
	public function getProduct()
	{
		return $this->hasOne(Product::className(), ['code' => 'product_code']);
	}
}
