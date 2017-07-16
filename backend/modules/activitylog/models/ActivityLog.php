<?php

namespace backend\modules\activitylog\models;
use common\models\Admin;
use Yii;

/**
 * This is the model class for table "activity_log".
 *
 * @property integer $id
 * @property integer $userid
 * @property string $type
 * @property string $action
 * @property string $message
 * @property string $added_date
 */
class ActivityLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return 'activity_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid', 'type', 'action', 'message', 'added_date'], 'required'],
            [['userid'], 'integer'],
            [['message'], 'string'],
            [['added_date'], 'safe'],
            [['type', 'action'], 'string', 'max' => 255]
        ];
    }


	 public function getUserName($data)
	 {
		$model = Admin::findOne($data->userid);
		if($model)
			$name = $model->firstname." ".$model->lastname;
		else
			$name = '';
	    return $name;
	 }
	 
	 public static function logUserActivity($userid,$type,$action,$message,$urltext)
	 {	
		$model = new ActivityLog();
		$model->userid = $userid;
		$model->type = $type;
		$model->action = $action;
		$model->message = $message;
		$model->urltext = $urltext;
		$model->added_date = date("Y-m-d H:i:s");
		$model->super_company_id_fk = Yii::$app->user->identity->super_company_id_fk;
	    $model->save();
	 }
	 
	 public function showloglink($model)
	 {
	 	if($model->urltext=='')
			return "Not Applicable";
		else
		{
			if(strstr($model->urltext,'al=1')==false)
			{
				if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { 
					$model->urltext = str_replace("http://","https://",$model->urltext);
				} 
				if(strstr($model->urltext,"?"))
					$model->urltext = $model->urltext."&al=1";
				else
					$model->urltext = $model->urltext."?al=1";
			}
			return "<a href='".$model->urltext."'>".$model->urltext."</a>";
		}
	 }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userid' => 'Person ID',
            'type' => 'Object',
            'action' => 'Action',
            'message' => 'Log',
			'urltext' => 'URL',
            'added_date' => 'Date & Time',
        ];
    }
}
