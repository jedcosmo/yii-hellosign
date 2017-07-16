<?php

namespace backend\modules\rolemanagement\models;

use Yii;

/**
 * This is the model class for table "bpr_role".
 *
 * @property integer $role_id_pk
 * @property string $name
 * @property string $modules
 * @property string $isDeleted
 * @property string $reasonIsDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Rolemanagement extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_role';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id_pk' => 'Role ID',
            'name' => 'Name',
            'modules' => 'Modules',
            'isDeleted' => 'Is Deleted',
            'reasonIsDeleted' => 'Reason Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'deleted_datetime' => 'Deleted Datetime',
			'reasonIsDeleted' => 'Delete Reason',
        ];
    }
}
