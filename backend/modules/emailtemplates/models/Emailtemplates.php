<?php

namespace backend\modules\emailtemplates\models;

use Yii;

/**
 * This is the model class for table "emails".
 *
 * @property integer $id
 * @property string $subject
 * @property string $message
 * @property string $added_date
 */
class Emailtemplates extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emails';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'message'], 'required'],
            [['subject', 'message'], 'string'],
            [['added_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'message' => 'Message',
            'added_date' => 'Added Date',
        ];
    }
}
