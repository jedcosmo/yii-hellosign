<?php

namespace backend\modules\state\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\state\models\State;

/**
 * StateSearch represents the model behind the search form about `backend\modules\state\models\State`.
 */
class StateSearch extends State
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['state_id_pk'], 'integer'],
            [['name','country_id_fk'], 'safe'],
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
        $query = State::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['state_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		$countryid = array();
		
		if(isset($this->country_id_fk) && $this->country_id_fk!='')
			$countryid = $this->getCountryID($this->country_id_fk);
			
        $query->andFilterWhere([
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'country_id_fk', $countryid]);
        $query->andFilterWhere(['like', 'state_id_pk', trim($this->state_id_pk)])
		->andFilterWhere(['like', 'name', trim($this->name)]);

        return $dataProvider;
    }
	
	public function getCountryID($countryname)
	{
		$name = array();
		$countryname = trim($countryname);
		$params = [':countryname' => '%'.$countryname.'%'];
		$Country = Yii::$app->db->createCommand('SELECT country_id_pk FROM bpr_country WHERE name like :countryname', $params)->queryAll();
		if(is_array($Country) && count($Country)>0)
		{
			foreach($Country as $k=>$v)
			{
				if($v['country_id_pk'])
    				$name[] = $v['country_id_pk'];
				else
					$name[] = '';
			}
		}
		else
		{
			$name[] = '';
		}
		return $name;
	}
	
	public function searchExcel($params)
    {
       $query = State::find();

	   $sortOrd = SORT_DESC; $sortBy = 'state_id_pk';
	   
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
		
       $countryid = array();
		
		if(isset($this->country_id_fk) && $this->country_id_fk!='')
			$countryid = $this->getCountryID($this->country_id_fk);
			
        $query->andFilterWhere([
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'country_id_fk', $countryid]);
        $query->andFilterWhere(['like', 'state_id_pk', trim($this->state_id_pk)])
		->andFilterWhere(['like', 'name', trim($this->name)]);

		$results = $query->all();
        return $results;
    }
}
