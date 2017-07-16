<?php

namespace backend\modules\documents\models;

use Yii;

/**
 * This is the model class for table "bpr_documents".
 *
 * @property integer $document_id_pk
 * @property string $docname
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Documents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_documents';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['docname', 'isDeleted', 'addedby_person_id_fk', 'created_datetime'], 'required'],
            [['isDeleted'], 'string'],
            [['addedby_person_id_fk'], 'integer'],
            [['created_datetime'], 'safe'],
            [['docname'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'document_id_pk' => 'Document Id Pk',
            'docname' => 'Docname',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person Id Fk',
            'created_datetime' => 'Created Datetime',
        ];
    }
}
