<?php

namespace backend\modules\equipmentmap\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\equipmentmap\models\Equipmentmap;

/**
 * EquipmentmapSearch represents the model behind the search form about `backend\modules\equipmentmap\models\Equipmentmap`.
 */
class EquipmentmapSearch extends Equipmentmap
{
	public $caliberation_due_date;
	public $preventive_m_due_date;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_code','equipment_id_fk','eqp_name','caliberation_due_date','preventive_m_due_date'], 'safe'],
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
        $query = Equipmentmap::find()->joinWith('equipment');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['equipment_map_id_pk'=>SORT_DESC]]
        ]);
		
		 $dataProvider->sort->attributes['caliberation_due_date'] = [
			'asc' => ['bpr_equipment.caliberation_due_date' => SORT_ASC],
			'desc' => ['bpr_equipment.caliberation_due_date' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['preventive_m_due_date'] = [
			'asc' => ['bpr_equipment.preventive_m_due_date' => SORT_ASC],
			'desc' => ['bpr_equipment.preventive_m_due_date' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		$eqpid = array(); $caliberation = ''; $preventive = '';
		
		
		$caliberation1 = ''; $preventive1 = '';
	    $caliberation2 = ''; $preventive2 = '';
		
		if(isset($this->caliberation_due_date) && $this->caliberation_due_date!='')
		{
			$caliberation1 = $this->caliberation_due_date;
			$caliDtArr = explode("|",trim($this->caliberation_due_date));
			if(count($caliDtArr)>1)
			{
				$query->andFilterWhere(['>=','bpr_equipment.caliberation_due_date',$caliDtArr[0]]);
				$query->andFilterWhere(['<=','bpr_equipment.caliberation_due_date',$caliDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','bpr_equipment.caliberation_due_date',$caliberation1]);
			}
		}
		
		if(isset($this->preventive_m_due_date) && $this->preventive_m_due_date!='')
		{
			$preventive1 = $this->preventive_m_due_date;
			$prevDtArr = explode("|",trim($this->preventive_m_due_date));
			if(count($prevDtArr)>1)
			{
				$query->andFilterWhere(['>=','bpr_equipment.preventive_m_due_date',$prevDtArr[0]]);
				$query->andFilterWhere(['<=','bpr_equipment.preventive_m_due_date',$prevDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','bpr_equipment.preventive_m_due_date',$preventive1]);
			}
		}
			
		if(isset($this->eqp_name) && $this->eqp_name!='')
			$eqpid = $this->getEquipmentID($this->eqp_name);

		if(is_array($eqpid) && count($eqpid)>0)
		{
			$query->andFilterWhere([
				'equipment_map_id_pk' => $this->equipment_map_id_pk,
				'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
				'product_id_fk' => $this->product_id_fk,
				'bpr_equipment_map.isDeleted' => '0',
				'bpr_equipment_map.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
			$query->andFilterWhere(['in', 'equipment_id_fk', $eqpid]);
		}
		else
		{
			$query->andFilterWhere([
				'equipment_map_id_pk' => $this->equipment_map_id_pk,
				'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
				'product_id_fk' => $this->product_id_fk,
				'bpr_equipment_map.isDeleted' => '0',
				'bpr_equipment_map.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
			$query->andFilterWhere(['like', 'equipment_id_fk', '%'.trim($this->equipment_id_fk).'%',false]);
		}
		
		
        $query->andFilterWhere(['like', 'product_code', trim($this->product_code)]);

        return $dataProvider;
    }
	
	public function getEquipmentID($eqpname)
	{
		$eqpname = trim($eqpname);
		$name = array();
		$params = [':eqpname' => '%'.$eqpname.'%'];
		$eqps = Yii::$app->db->createCommand("SELECT equipment_id_pk FROM bpr_equipment WHERE name like :eqpname", $params)->queryAll();
		if(is_array($eqps) && count($eqps)>0)
		{
			foreach($eqps as $k=>$v)
			{
				if($v['equipment_id_pk'])
    				$name[]= $v['equipment_id_pk'];
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
