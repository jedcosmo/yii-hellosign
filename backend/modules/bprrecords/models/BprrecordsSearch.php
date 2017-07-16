<?php

namespace backend\modules\bprrecords\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bprrecords\models\Bprrecords;

/**
 * BprrecordsSearch represents the model behind the search form about `backend\modules\bprrecords\models\Bprrecords`.
 */
class BprrecordsSearch extends Bprrecords
{
	public $product_name;
	public $MPR_version;
	public $product_part;
	
	public $status;
	public $status_approver;
	public $time_stamp;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['batch', 'manufacturing_date', 'product_code', 'mpr_version', 'product_name', 'MPR_version', 'product_part'], 'safe'],
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
        $query = Bprrecords::find()->joinWith('mprdefination');//->joinWith('product');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['bpr_id_pk'=>SORT_DESC]]
        ]);
		
		 $dataProvider->sort->attributes['MPR_version'] = [
			'asc' => ['bpr_mpr_defination.MPR_version' => SORT_ASC],
			'desc' => ['bpr_mpr_defination.MPR_version' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['product_name'] = [
			'asc' => ['bpr_mpr_defination.product_name' => SORT_ASC],
			'desc' => ['bpr_mpr_defination.product_name' => SORT_DESC],
		];
		
		$dataProvider->sort->attributes['product_part'] = [
			'asc' => ['bpr_mpr_defination.product_part' => SORT_ASC],
			'desc' => ['bpr_mpr_defination.product_part' => SORT_DESC],
		];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //. return $dataProvider;
        }

        $query->andFilterWhere([
            'bpr_batch_processing_records.bpr_id_pk' => $this->bpr_id_pk,
            'bpr_batch_processing_records.manufacturing_date' => $this->manufacturing_date,
            'bpr_batch_processing_records.mpr_definition_id_fk' => $this->mpr_definition_id_fk,
            'bpr_batch_processing_records.addedby_person_id_fk' => $this->addedby_person_id_fk,
            'bpr_batch_processing_records.created_datetime' => $this->created_datetime,
			'bpr_mpr_defination.isDeleted' => '0',
			'bpr_batch_processing_records.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'batch', $this->batch])
            ->andFilterWhere(['like', 'bpr_batch_processing_records.product_code', trim($this->product_code)])
			->andFilterWhere(['like', 'bpr_mpr_defination.MPR_version', trim($this->MPR_version)])
			->andFilterWhere(['like', 'bpr_mpr_defination.product_name', trim($this->product_name)])
			->andFilterWhere(['like', 'bpr_mpr_defination.product_part', trim($this->product_part)]);

        return $dataProvider;
    }
}
