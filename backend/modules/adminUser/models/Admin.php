<?php

namespace backend\modules\adminUser\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "admin".
 *
 * @property integer $id
 * @property string $firstname
 * @property string $lastname
 * @property string $nickname
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $image
 * @property string $email
 * @property string $companyname
 * @property string $company_registrationno
 * @property string $address1
 * @property string $address2
 * @property string $postalcode
 * @property integer $country_id
 * @property string $telephone
 */
class Admin extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_admin';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['firstname', 'lastname', 'username', 'email'], 'required'],
            [['firstname', 'lastname', 'nickname', 'username', 'email'], 'string', 'max' => 255, 'min' => 5],
			[['image'], 'image', 'skipOnEmpty' => true]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'First Name',
            'lastname' => 'Last Name',
            'nickname' => 'Nick Name',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'image' => 'Profile Picture',
            'email' => 'Email Id',
        ];
    }
	
	/**
	* Uploading File
	**/
	public function upload()
    {
        if ($this->validate()) {
            $this->image->saveAs('uploads/' . $this->image->baseName . '.' . $this->image->extension);
            return true;
        } else {
            return false;
        }
    }
	
}
