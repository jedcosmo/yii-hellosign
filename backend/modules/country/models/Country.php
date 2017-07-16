<?php

namespace backend\modules\country\models;

use Yii;

/**
 * This is the model class for table "bpr_country".
 *
 * @property integer $country_id_pk
 * @property string $name
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country_id_pk' => 'Country ID',
            'name' => 'Name',
			'reasonIsDeleted' => 'Delete Reason',
        ];
    }
}
