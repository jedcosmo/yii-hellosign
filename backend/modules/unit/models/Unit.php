<?php

namespace backend\modules\unit\models;

use Yii;

/**
 * This is the model class for table "bpr_unit".
 *
 * @property integer $unit_id_pk
 * @property string $name
 * @property string $description
 * @property string $symbols
 * @property string $isDeleted
 * @property integer $addedby_person_id_fk
 * @property string $created_datetime
 */
class Unit extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bpr_unit';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['description'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 20],
			[['name'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'unit_id_pk' => 'Unit ID',
            'name' => 'Name',
            'description' => 'Description',
            'symbols' => 'Symbols',
            'isDeleted' => 'Is Deleted',
            'addedby_person_id_fk' => 'Addedby Person Id Fk',
            'created_datetime' => 'Added Datetime',
			'reasonIsDeleted'=>'Delete Reason',
        ];
    }
	
	/**
	* Uploading File
	**/
	public function upload()
    {
        if ($this->validate()) {
			$imgname = $this->symbols->baseName."_".time().'.' . $this->symbols->extension;
            $this->symbols->saveAs('uploads/symbols/' . $imgname);
            return $imgname;
        } else {
            return false;
        }
    }
	
	public function showSymbol($symbol)
	{
		if($symbol)
		{
			return "<a title='View symbol' href='".Yii::$app->urlManager->baseUrl."/uploads/symbols/".$symbol."' target='_blank'><span class='glyphicon glyphicon-file'></span>&nbsp; View Symbol</a>";
		}
		else
		{
			return '';
		}
	}
}
