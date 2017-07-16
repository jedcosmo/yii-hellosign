<?php

namespace backend\modules\city\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\city\models\City;

/**
 * CitySearch represents the model behind the search form about `backend\modules\city\models\City`.
 */
class CitySearch extends City
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id_pk'], 'integer'],
            [['name','state_id_fk', 'country_id_fk'], 'safe'],
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
        $query = City::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['city_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		$countryid = array(); $stateid = array();
		
		if(isset($this->country_id_fk) && $this->country_id_fk!='')
			$countryid = $this->getCountryID($this->country_id_fk);
			
		if(isset($this->state_id_fk) && $this->state_id_fk!='')
			$stateid = $this->getStateID($this->state_id_fk);

		
        $query->andFilterWhere([
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'state_id_fk', $stateid]);
		$query->andFilterWhere(['in', 'country_id_fk', $countryid]);
		
        $query->andFilterWhere(['like', 'city_id_pk', trim($this->city_id_pk)])
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
	
	public function getStateID($statename)
	{
		$name = array();
		$statename= trim($statename);
		$params = [':statename' => '%'.$statename.'%'];
		$Country = Yii::$app->db->createCommand('SELECT state_id_pk FROM bpr_state WHERE name like :statename', $params)->queryAll();
		if(is_array($Country) && count($Country)>0)
		{
			foreach($Country as $k=>$v)
			{	
				if($v['state_id_pk'])
    				$name[] = $v['state_id_pk'];
				else
					$name[] = '';
			}
		}
		else{
			$name[] = '';
		}
		return $name;
	}
	
	public function searchExcel($params)
    {
       $query = City::find();

	   $sortOrd = SORT_DESC; $sortBy = 'city_id_pk';
	   
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
		
       $countryid = array(); $stateid = array();
		
		if(isset($this->country_id_fk) && $this->country_id_fk!='')
			$countryid = $this->getCountryID($this->country_id_fk);
			
		if(isset($this->state_id_fk) && $this->state_id_fk!='')
			$stateid = $this->getStateID($this->state_id_fk);
			
       $query->andFilterWhere([
			'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'state_id_fk', $stateid]);
		$query->andFilterWhere(['in', 'country_id_fk', $countryid]);
		
        $query->andFilterWhere(['like', 'city_id_pk', trim($this->city_id_pk)])
		->andFilterWhere(['like', 'name', trim($this->name)]);

		$results = $query->all();
        return $results;
    }
}
