<?php

namespace backend\modules\personcompany\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\personcompany\models\Personcompany;

/**
 * CompanySearch represents the model behind the search form about `backend\modules\company\models\Company`.
 */
class PersoncompanySearch extends Personcompany
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id_pk'], 'integer'],
            [['name','address1', 'address2', 'pobox', 'zip_postalcode'], 'safe'],
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
        $query = Personcompany::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['company_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           // return $dataProvider;
        }

        $query->andFilterWhere([
            //'company_id_pk' => $this->company_id_pk,
        	'isDeleted' => '0',
        ]);

        $query->andFilterWhere(['like', 'company_id_pk', trim($this->company_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)])
			->andFilterWhere(['like', 'address1', trim($this->address1)])
            ->andFilterWhere(['like', 'address2', trim($this->address2)])
            ->andFilterWhere(['like', 'pobox', trim($this->pobox)])
            ->andFilterWhere(['like', 'zip_postalcode', trim($this->zip_postalcode)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);

        return $dataProvider;
    }
	
	public function searchExcel($params)
    {
       $query = Personcompany::find();

	   $sortOrd = SORT_DESC; $sortBy = 'company_id_pk';
	   
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
            'company_id_pk' => $this->company_id_pk,
        	'isDeleted' => '0',
        ]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
			->andFilterWhere(['like', 'address1', trim($this->address1)])
            ->andFilterWhere(['like', 'address2', trim($this->address2)])
            ->andFilterWhere(['like', 'pobox', trim($this->pobox)])
            ->andFilterWhere(['like', 'zip_postalcode', trim($this->zip_postalcode)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);
			
		$results = $query->all();
        return $results;
    }
}
