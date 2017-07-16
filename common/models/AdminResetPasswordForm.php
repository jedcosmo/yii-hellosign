<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class AdminResetPasswordForm extends Model
{
   
	public $password;
	public $retype_password;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password','retype_password'], 'required'],
			[['password','retype_password'], 'string','max'=>16, 'min'=>6],
			['password', 'match','pattern'=>'/^[\S]*$/', 'message' => 'All characters are allowed without space in password'],
            ['retype_password', 'compare','compareAttribute' =>'password', 'message' => 'Retyped password is incorrect'],
        ];
    }
    
}
