<?php

namespace backend\modules\equipment\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\equipment\models\Equipment;

/**
 * EquipmentSearch represents the model behind the search form about `backend\modules\equipment\models\Equipment`.
 */
class EquipmentSearchDel extends Equipment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment_id_pk'], 'integer'],
            [['name', 'model', 'serial', 'caliberation_due_date', 'preventive_m_due_date', 'reasonIsDeleted'], 'safe'],
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
        $query = Equipment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['equipment_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           //  return $dataProvider;
        }
		
		$caliberation1 = ''; $preventive1 = '';
	    $caliberation2 = ''; $preventive2 = '';
	   
		if(isset($this->caliberation_due_date) && $this->caliberation_due_date!='')
		{
			$caliberation1 = $this->caliberation_due_date;
			$caliDtArr = explode("|",trim($this->caliberation_due_date));
			if(count($caliDtArr)>1)
			{
				$query->andFilterWhere(['>=','caliberation_due_date',$caliDtArr[0]]);
				$query->andFilterWhere(['<=','caliberation_due_date',$caliDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','caliberation_due_date',$caliberation1]);
			}
		}
		
		if(isset($this->preventive_m_due_date) && $this->preventive_m_due_date!='')
		{
			$preventive1 = $this->preventive_m_due_date;
			$prevDtArr = explode("|",trim($this->preventive_m_due_date));
			if(count($prevDtArr)>1)
			{
				$query->andFilterWhere(['>=','preventive_m_due_date',$prevDtArr[0]]);
				$query->andFilterWhere(['<=','preventive_m_due_date',$prevDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','preventive_m_due_date',$preventive1]);
			}
		}
	   
        $query->andFilterWhere([
			'isDeleted' => '1',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'equipment_id_pk', trim($this->equipment_id_pk)])
			->andFilterWhere(['like', 'name', trim($this->name)])
            ->andFilterWhere(['like', 'model', trim($this->model)])
            ->andFilterWhere(['like', 'serial', trim($this->serial)])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        return $dataProvider;
    }
}
