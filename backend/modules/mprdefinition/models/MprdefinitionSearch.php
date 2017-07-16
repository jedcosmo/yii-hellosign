<?php

namespace backend\modules\mprdefinition\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\mprdefinition\models\Mprdefination;

/**
 * MprdefinitionSearch represents the model behind the search form about `backend\modules\mprdefinition\models\Mprdefination`.
 */
class MprdefinitionSearch extends Mprdefination
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch_size', ], 'integer'],
            [['product_code', 'MPR_version', 'product_part', 'author', 'product_name', 'formulation_id', 'MPR_unit_id', 'theoritical_yield', 'purpose', 'scope'], 'safe'],
            [['product_strength'], 'number'],
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
        $query = Mprdefination::find()->joinWith('product');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['mpr_defination_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           // return $dataProvider;
        }

        $query->andFilterWhere([
            'bpr_mpr_defination.mpr_defination_id_pk' => $this->mpr_defination_id_pk,
			'bpr_mpr_defination.MPR_version' => $this->MPR_version,
            'bpr_mpr_defination.product_id_fk' => $this->product_id_fk,
            'bpr_mpr_defination.product_strength' => $this->product_strength,
            'bpr_mpr_defination.batch_size' => $this->batch_size,
            'bpr_mpr_defination.company_id_fk' => $this->company_id_fk,
			//'bpr_product.isDeleted' => '0',
			'bpr_mpr_defination.isDeleted' => '0',
			'bpr_mpr_defination.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'bpr_mpr_defination.product_code', trim($this->product_code)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.product_part', trim($this->product_part)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.author', trim($this->author)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.product_name', trim($this->product_name)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.formulation_id', trim($this->formulation_id)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.MPR_unit_id', trim($this->MPR_unit_id)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.theoritical_yield', trim($this->theoritical_yield)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.purpose', trim($this->purpose)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.scope', trim($this->scope)]);

        return $dataProvider;
    }
	
	public function searchExcel($params)
    {
       $query = Mprdefination::find()->joinWith('product');

	   $sortOrd = SORT_DESC; $sortBy = 'mpr_defination_id_pk';
	   
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
            'bpr_mpr_defination.mpr_defination_id_pk' => $this->mpr_defination_id_pk,
			'bpr_mpr_defination.MPR_version' => $this->MPR_version,
            'bpr_mpr_defination.product_id_fk' => $this->product_id_fk,
            'bpr_mpr_defination.product_strength' => $this->product_strength,
            'bpr_mpr_defination.batch_size' => $this->batch_size,
            'bpr_mpr_defination.company_id_fk' => $this->company_id_fk,
			'bpr_mpr_defination.isDeleted' => '0',
			//'bpr_product.isDeleted' => '0',
			'bpr_mpr_defination.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'bpr_mpr_defination.product_code', trim($this->product_code)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.product_part', trim($this->product_part)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.author', trim($this->author)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.product_name', trim($this->product_name)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.formulation_id', trim($this->formulation_id)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.MPR_unit_id', trim($this->MPR_unit_id)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.theoritical_yield', trim($this->theoritical_yield)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.purpose', trim($this->purpose)])
            ->andFilterWhere(['like', 'bpr_mpr_defination.scope', trim($this->scope)]);

		$results = $query->all();
        return $results;
    }
}
