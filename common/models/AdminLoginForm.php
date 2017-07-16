<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class AdminLoginForm extends Model
{
    public $username;
	public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
			//echo "comes here"; exit;
            if (!$user || !$user->validatePassword($this->password)) {
				$error = "Incorrect username or password.";
				
				$params = [':user_name_person' => $this->username, ':password_person'=>md5($this->password)];
				$Person = Yii::$app->db->createCommand('SELECT isDeleted,super_company_id_fk,is_verified FROM bpr_person WHERE (user_name_person=:user_name_person or emailid=:user_name_person) and password_person=:password_person', $params)->queryOne();
				if($Person['isDeleted'] == '1'){
					$error = "Your account has been deleted, please contact admin";
				}
				elseif($Person['is_verified'] == '0')
				{
					$error = "Your emailid is not yet verified, please verify your emailid.";
				}
				
				$params = [':company_id_pk' => $Person['super_company_id_fk']];
				$personCompany = Yii::$app->db->createCommand('SELECT isDeleted FROM bpr_person_company WHERE (company_id_pk=:company_id_pk)', $params)->queryOne();
				if($personCompany['isDeleted'] == '1'){
					$error = "Your company has been deleted, please contact admin";
				}
				
                $this->addError($attribute, $error);
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Admin::findByUsername($this->username);
			if($this->_user == false)
			{
				$user = Admin::findByEmail($this->username);
				if($user != false)
					$this->_user = Admin::find()->where(['emailid' => $this->username])->one();
			}		
        }
	
        return $this->_user;
    }
}
