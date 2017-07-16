<?php

namespace backend\modules\formulation\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\formulation\models\Formulation;

/**
 * BillofmaterialSearch represents the model behind the search form about `backend\modules\billofmaterial\models\Billofmaterial`.
 */
class FormulationSearch extends Formulation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['f_id_pk','material_part','material_name','formulation_percentage','mpr_defination_id_fk'], 'safe'],
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
		
	   
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['f_id_pk'=>SORT_DESC]],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		
        $query->andFilterWhere([
          
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
			'isDeleted' => '0',
			'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);
		
	
        $query->andFilterWhere(['like', 'f_id_pk', trim($this->f_id_pk)])
			->andFilterWhere(['like', 'material_name', trim($this->material_name)])
           ->andFilterWhere(['like', 'material_part', trim($this->material_part)])
		   ->andFilterWhere(['like', 'formulation_percentage', trim($this->formulation_percentage)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)]);

        return $dataProvider;
    }
	
	
}
