<?php

namespace backend\modules\unit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\unit\models\Unit;

/**
 * UnitSearch represents the model behind the search form about `backend\modules\unit\models\Unit`.
 */
class UnitSearch extends Unit
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['unit_id_pk'], 'integer'],
            [['name', 'description'], 'safe'],
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['unit_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }

        $query->andFilterWhere([
           // 'unit_id_pk' => $this->unit_id_pk,
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'unit_id_pk', trim($this->unit_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'description', trim($this->description)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);

        return $dataProvider;
    }
	
	public function searchExcel($params)
    {
       $query = Unit::find();

	   $sortOrd = SORT_DESC; $sortBy = 'unit_id_pk';
	   
	   if(isset($params['sort']) && $params['sort']!='')
	   {
			if(substr($params['sort'],0,1)=="-")
			{
				$sortOrd = SORT_DESC;
				$sortBy = ltrim($params['sort'],'-');
			}
			else
			{
				$sortOrd = SORT_ASC;
				$sortBy = $params['sort'];
			}
	   }
	   $query->orderBy([$sortBy=>$sortOrd]);
		
       $this->load($params);
		
        $query->andFilterWhere([
            //'unit_id_pk' => $this->unit_id_pk,
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'unit_id_pk', trim($this->unit_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'description', trim($this->description)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);

		$results = $query->all();
        return $results;
    }
}
