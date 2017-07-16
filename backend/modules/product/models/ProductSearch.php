<?php

namespace backend\modules\product\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\product\models\Product;

/**
 * ProductSearch represents the model behind the search form about `backend\modules\product\models\Product`.
 */
class ProductSearch extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id_pk'], 'integer'],
            [['name', 'part', 'code','company_id_fk','unit_id_fk'], 'safe'],
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
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['part'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // return $dataProvider;
        }
		$companyid = array(); $unitid = array();
		
		if(isset($this->company_id_fk) && $this->company_id_fk!='')
			$companyid = $this->getCompanyID($this->company_id_fk);
			
		if(isset($this->unit_id_fk) && $this->unit_id_fk!='')
			$unitid = $this->getUnitID($this->unit_id_fk);

        $query->andFilterWhere([
            'product_id_pk' => $this->product_id_pk,
            'qty' => $this->qty,
			'isDeleted' => '0',
			//'company_id_fk' => $companyid,
            //'unit_id_fk' => $unitid,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'company_id_fk', $companyid]);
		$query->andFilterWhere(['in', 'unit_id_fk', $unitid]);
		
        $query->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'part', trim($this->part)])
            ->andFilterWhere(['like', 'code', trim($this->code)]);

        return $dataProvider;
    }
	
	public function getCompanyID($companyname)
	{
		$name = array();
		$companyname = trim($companyname);
		$params = [':companyname' => "%".$companyname."%"];
		$Company = Yii::$app->db->createCommand("SELECT company_id_pk FROM bpr_company WHERE name like :companyname", $params)->queryAll();
		if(is_array($Company) && count($Company)>0)
		{
			foreach($Company as $k=>$v)
			{
				if($v['company_id_pk'])
    				$name = $v['company_id_pk'];
				else
					$name[] = 0;
			}
		}
		else
		{
			$name[] = 0;
		}
		return $name;
	}
	
	public function getUnitID($unitname)
	{
		$name = array();
		$unitname = trim($unitname);
		$params = [':unitname' => '%'.$unitname.'%'];
		$Unit = Yii::$app->db->createCommand("SELECT unit_id_pk FROM bpr_unit WHERE name like :unitname", $params)->queryAll();
		if(is_array($Unit) && count($Unit)>0)
		{
			foreach($Unit as $k=>$v)
			{
				if($v['unit_id_pk'])
    				$name[] = $v['unit_id_pk'];
				else
					$name[] = 0;
			}
		}
		else
		{
			$name[] = 0;
		}
		return $name;
	}
	
	public function searchExcel($params)
    {
       $query = Product::find();

	   $sortOrd = SORT_DESC; $sortBy = 'part';
	   
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
		
       $companyid = array(); $unitid = array();
		
		if(isset($this->company_id_fk) && $this->company_id_fk!='')
			$companyid = $this->getCompanyID($this->company_id_fk);
			
		if(isset($this->unit_id_fk) && $this->unit_id_fk!='')
			$unitid = $this->getUnitID($this->unit_id_fk);

        $query->andFilterWhere([
            'product_id_pk' => $this->product_id_pk,
            'qty' => $this->qty,
			'isDeleted' => '0',
			//'company_id_fk' => $companyid,
            //'unit_id_fk' => $unitid,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'company_id_fk', $companyid]);
		$query->andFilterWhere(['in', 'unit_id_fk', $unitid]);
		
        $query->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'part', trim($this->part)])
            ->andFilterWhere(['like', 'code', trim($this->code)]);

		$results = $query->all();
        return $results;
    }
}
