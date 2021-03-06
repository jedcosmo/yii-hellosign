<?php

namespace backend\modules\unit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\unit\models\Unit;

/**
 * UnitSearch represents the model behind the search form about `backend\modules\unit\models\Unit`.
 */
class UnitSearchDel extends Unit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id_pk'], 'integer'],
            [['name', 'description', 'reasonIsDeleted','deleted_datetime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Unit::find();
		$deleted_datetime ='';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['deleted_datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

		$dateStr = ''; $deleted_datetime1=''; $deleted_datetime2='';
		if(isset($this->deleted_datetime) && $this->deleted_datetime!='')
		{
			$dateStr = $this->deleted_datetime;
			$newDtArr = explode("|",trim($this->deleted_datetime));
			if(count($newDtArr)>1)
			{
				$deleted_datetime1 = $newDtArr[0];
				$deleted_datetime2 = $newDtArr[1]; 
				$query->andFilterWhere(['>=','deleted_datetime',$deleted_datetime1]);
				$query->andFilterWhere(['<=','deleted_datetime',$deleted_datetime2]);
			}
			else
			{
				$query->andFilterWhere(['like','deleted_datetime',$dateStr]);
			}
		}
			
        $query->andFilterWhere([
			'isDeleted' => '1',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'unit_id_pk', trim($this->unit_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'description', trim($this->description)])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);

        return $dataProvider;
    }
}
