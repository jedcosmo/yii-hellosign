<?php

namespace backend\modules\role\models;

use Yii;

/**
 * This is the model class for table "bpr_role".
 *
 * @property integer $role_id_pk
 * @property string $name
 * @property string $modules
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Role extends \yii\db\ActiveRecord
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
            [['name', 'modules', 'isDeleted', 'addedby_person_id_fk', 'created_datetime'], 'required'],
            [['modules', 'isDeleted'], 'string'],
            [['addedby_person_id_fk'], 'integer'],
            [['created_datetime'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'role_id_pk' => 'Role Id Pk',
            'name' => 'Name',
            'modules' => 'Modules',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person Id Fk',
            'created_datetime' => 'Created Datetime',
        ];
    }
}
