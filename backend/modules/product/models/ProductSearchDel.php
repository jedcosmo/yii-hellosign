<?php

namespace backend\modules\product\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\product\models\Product;

/**
 * ProductSearch represents the model behind the search form about `backend\modules\product\models\Product`.
 */
class ProductSearchDel extends Product
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id_pk'], 'integer'],
            [['name', 'part', 'code','company_id_fk','unit_id_fk','reasonIsDeleted','deleted_datetime'], 'safe'],
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
		$deleted_datetime ='';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['deleted_datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
           //  return $dataProvider;
        }
		$companyid = ''; $unitid = '';
		
		if(isset($this->company_id_fk) && $this->company_id_fk!='')
			$companyid = $this->getCompanyID($this->company_id_fk);
			
		if(isset($this->unit_id_fk) && $this->unit_id_fk!='')
			$unitid = $this->getUnitID($this->unit_id_fk);

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
            'product_id_pk' => $this->product_id_pk,
            'qty' => $this->qty,
			'isDeleted' => '1',
			'company_id_fk' => $companyid,
            'unit_id_fk' => $unitid,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'part', trim($this->part)])
            ->andFilterWhere(['like', 'code', trim($this->code)])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        return $dataProvider;
    }
	
	public function getCompanyID($companyname)
	{
		$companyname = trim($companyname);
		$params = [':companyname' => "%".$companyname."%"];
		$Company = Yii::$app->db->createCommand("SELECT company_id_pk FROM bpr_company WHERE name like :companyname", $params)->queryOne();
			if($Company['company_id_pk'])
    			$name = $Company['company_id_pk'];
			else
				$name = '0';
		return $name;
	}
	
	public function getUnitID($unitname)
	{
		$unitname = trim($unitname);
		$params = [':unitname' => $unitname];
		$Unit = Yii::$app->db->createCommand("SELECT unit_id_pk FROM bpr_unit WHERE name=:unitname", $params)->queryOne();
			if($Unit['unit_id_pk'])
    			$name = $Unit['unit_id_pk'];
			else
				$name = '0';
		return $name;
	}
}
