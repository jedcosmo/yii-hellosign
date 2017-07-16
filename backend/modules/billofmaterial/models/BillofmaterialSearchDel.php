<?php

namespace backend\modules\billofmaterial\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\billofmaterial\models\Billofmaterial;

/**
 * BillofmaterialSearch represents the model behind the search form about `backend\modules\billofmaterial\models\Billofmaterial`.
 */
class BillofmaterialSearchDel extends Billofmaterial
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bom_id_pk','material_name','qty_branch','qb_unit_id_fk', 'composition','com_unit_id_fk','mpr_defination_id_fk','reasonIsDeleted','deleted_datetime'], 'safe'],
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
		
		$qb_unit_id_fk = array(); $com_unit_id_fk = array();
		
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
		
		if(isset($this->qb_unit_id_fk) && $this->qb_unit_id_fk!='')
			$qb_unit_id_fk = $this->getUnitID($this->qb_unit_id_fk);
			
		if(isset($this->com_unit_id_fk) && $this->com_unit_id_fk!='')
			$com_unit_id_fk = $this->getUnitID($this->com_unit_id_fk);

        $query->andFilterWhere([
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
			'isDeleted' => '1',
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
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

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
