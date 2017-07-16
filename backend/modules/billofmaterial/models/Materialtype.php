<?php

namespace backend\modules\billofmaterial\models;

use Yii;

/**
 * This is the model class for table "bpr_material_type".
 *
 * @property integer $material_type_id_pk
 * @property string $type_name
 */
class Materialtype extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_material_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type_name'], 'required'],
            [['type_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'material_type_id_pk' => 'Material Type Id Pk',
            'type_name' => 'Type Name',
        ];
    }
}
