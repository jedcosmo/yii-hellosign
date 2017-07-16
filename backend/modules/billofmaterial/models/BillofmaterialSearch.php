<?php

namespace backend\modules\billofmaterial\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\billofmaterial\models\Billofmaterial;

/**
 * BillofmaterialSearch represents the model behind the search form about `backend\modules\billofmaterial\models\Billofmaterial`.
 */
class BillofmaterialSearch extends Billofmaterial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bom_id_pk','material_name','qty_branch','qb_unit_id_fk', 'composition','com_unit_id_fk','mpr_defination_id_fk','material_test_status'], 'safe'],
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
        $query = Billofmaterial::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['bom_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		$qb_unit_id_fk = array(); $com_unit_id_fk = array();
		
		if(isset($this->qb_unit_id_fk) && $this->qb_unit_id_fk!='')
			$qb_unit_id_fk = $this->getUnitID($this->qb_unit_id_fk);
			
		if(isset($this->com_unit_id_fk) && $this->com_unit_id_fk!='')
			$com_unit_id_fk = $this->getUnitID($this->com_unit_id_fk);

        $query->andFilterWhere([
           // 'bom_id_pk' => $this->bom_id_pk,
           // 'qty_branch' => $this->qty_branch,
           // 'qb_unit_id_fk' => $qb_unit_id_fk,
           // 'com_unit_id_fk' => $com_unit_id_fk,
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
			'isDeleted' => '0',
			'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);
		
		$query->andFilterWhere(['in', 'qb_unit_id_fk', $qb_unit_id_fk]);
		$query->andFilterWhere(['in', 'com_unit_id_fk', $com_unit_id_fk]);

        $query->andFilterWhere(['like', 'bom_id_pk', trim($this->bom_id_pk)])
			->andFilterWhere(['like', 'qty_branch', '%'.trim($this->qty_branch).'%',false])
			->andFilterWhere(['like', 'material_name', trim($this->material_name)])
            ->andFilterWhere(['like', 'composition', trim($this->composition)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
			 ->andFilterWhere(['like', 'material_test_status', '%'.trim($this->material_test_status).'%',false]);

        return $dataProvider;
    }
	
	public function getUnitID($unitname)
	{
		$unitname = trim($unitname);
		$name = array();
		$params = [':unitname' => '%'.$unitname.'%'];
		$Unit = Yii::$app->db->createCommand("SELECT unit_id_pk, name, description, symbols, isDeleted, reasonIsDeleted, deleted_datetime, isRestored, reasonIsRestored, restore_datetime, addedby_person_id_fk, created_datetime, super_company_id_fk FROM bpr_unit WHERE name like :unitname", $params)->queryAll();
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
}
