<?php

namespace backend\modules\formulation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\formulation\models\Formulation;

/**
 * BillofmaterialSearch represents the model behind the search form about `backend\modules\billofmaterial\models\Billofmaterial`.
 */
class FormulationSearchDel extends Formulation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_id_pk','material_name','material_part','formulation_percentage','mpr_defination_id_fk','reasonIsDeleted','deleted_datetime'], 'safe'],
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
        $query = Formulation::find();
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
		
		
        $query->andFilterWhere([
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
			'isDeleted' => '1',
			'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		
		
        $query->andFilterWhere(['like', 'f_id_pk', trim($this->f_id_pk)])
			->andFilterWhere(['like', 'material_name', trim($this->material_name)])
           ->andFilterWhere(['like', 'material_part', trim($this->material_part)])
		   ->andFilterWhere(['like', 'formulation_percentage', trim($this->formulation_percentage)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        return $dataProvider;
    }
	
	
}
