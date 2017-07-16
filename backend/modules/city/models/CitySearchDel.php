<?php

namespace backend\modules\city\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\city\models\City;

/**
 * CitySearch represents the model behind the search form about `backend\modules\city\models\City`.
 */
class CitySearchDel extends City
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city_id_pk'], 'integer'],
            [['name','state_id_fk', 'country_id_fk','reasonIsDeleted','deleted_datetime'], 'safe'],
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
		$deleted_datetime ='';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['deleted_datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
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
		
		$countryid = array(); $stateid = array();
		
		if(isset($this->country_id_fk) && $this->country_id_fk!='')
			$countryid = $this->getCountryID($this->country_id_fk);
			
		if(isset($this->state_id_fk) && $this->state_id_fk!='')
			$stateid = $this->getStateID($this->state_id_fk);
				
        $query->andFilterWhere([
			'isDeleted' => '1',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'state_id_fk', $stateid]);
		$query->andFilterWhere(['in', 'country_id_fk', $countryid]);
		
        $query->andFilterWhere(['like', 'city_id_pk', trim($this->city_id_pk)])
		->andFilterWhere(['like', 'name', trim($this->name)])
		->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

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
}
