<?php

namespace backend\modules\bprapprovals\models;

use Yii;

/**
 * This is the model class for table "bpr_bpr_approval".
 *
 * @property integer $bpr_approval_id_pk
 * @property integer $bpr_id_fk
 * @property integer $approval_person_id_fk
 * @property string $approval_job_function
 * @property string $approval_status
 * @property string $approval_datetime
 * @property integer $verifier_person_id_fk
 * @property string $verifier_job_function
 * @property string $verified_status
 * @property string $verified_datetime
 * @property integer $document_id_fk
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Bprapprovals extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	public $perfomer;
	public $verifier;
	
    public static function tableName()
    {
        return 'bpr_bpr_approval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'approval_person_id_fk', 'approval_job_function', 'verifier_person_id_fk','verifier_job_function'], 'required'],
            [['approval_job_function', 'verifier_job_function'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'bpr_approval_id_pk' => 'BPR Approval ID',
            'bpr_id_fk' => 'BPR ID',
            'approval_person_id_fk' => 'Approver',
            'approval_job_function' => 'Job Function',
            'approval_status' => 'Approval Status',
            'approval_datetime' => 'Datetime',
            'verifier_person_id_fk' => 'Verifier',
            'verifier_job_function' => 'Job Function',
            'verified_status' => 'Verified Status',
            'verified_datetime' => 'Datetime',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person ID',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted' => 'Delete Reason',
        ];
    }
	
	public function showPersonName($personid)
	{
		$params = [':person_id_pk' => $personid];
		$Person = Yii::$app->db->createCommand('SELECT first_name,last_name FROM bpr_person WHERE person_id_pk=:person_id_pk', $params)->queryOne();
			if($Person['first_name'])
    			$name = $Person['first_name']." ".$Person['last_name'];
			else
				$name = '';
		return $name;
	}
	
	public function getPersonFromUsername($username, $password)
	{
		$params = [':user_name_person' => $username, ':password_person'=>md5($password)];
		$Person = Yii::$app->db->createCommand('SELECT person_id_pk FROM bpr_person WHERE (user_name_person=:user_name_person or emailid=:user_name_person) and password_person=:password_person', $params)->queryOne();
			if($Person['person_id_pk'])
    			$person_id_pk = $Person['person_id_pk'];
			else
				$person_id_pk = '';
		return $person_id_pk;
	}
}
