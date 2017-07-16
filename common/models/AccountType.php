<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "account_type".
 *
 * @property integer $account_type_id
 * @property string $accounttype
 * @property integer $is_active
 */
class AccountType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'account_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['accounttype'], 'required'],
            [['is_active'], 'integer'],
            [['accounttype'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'account_type_id' => 'Account Type ID',
            'accounttype' => 'Accounttype',
            'is_active' => 'Is Active',
        ];
    }
}
