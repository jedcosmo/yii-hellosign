<?php

namespace backend\modules\sitesetting\models;

use Yii;

/**
 * This is the model class for table "sitesetting".
 *
 * @property integer $id
 * @property string $type
 * @property string $value
 */
class Sitesetting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sitesetting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'value'], 'required'],
            [['type', 'value'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'value' => 'Value',
        ];
    }
}
