<?php

namespace backend\modules\country\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\country\models\Country;

/**
 * CountrySearch represents the model behind the search form about `backend\modules\country\models\Country`.
 */
class CountrySearch extends Country
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id_pk'], 'integer'],
            [['name'], 'safe'],
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
        $query = Country::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['country_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           // return $dataProvider;
        }

        $query->andFilterWhere([
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'country_id_pk', trim($this->country_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)]);

        return $dataProvider;
    }
	
	public function searchExcel($params)
    {
       $query = Country::find();

	   $sortOrd = SORT_DESC; $sortBy = 'country_id_pk';
	   
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
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'country_id_pk', trim($this->country_id_pk)])
		->andFilterWhere(['like', 'name', trim($this->name)]);

		$results = $query->all();
        return $results;
    }
}
