<?php

namespace backend\modules\minstructions\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\minstructions\models\Minstructions;

/**
 * MinstructionsSearch represents the model behind the search form about `backend\modules\minstructions\models\Minstructions`.
 */
class MinstructionsSearch extends Minstructions
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mi_step', 'mi_action','unit_id_fk', 'mi_range', 'target', 'perfomer', 'verifier'], 'safe'],
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
        $query = Minstructions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['mi_id_pk'=>SORT_ASC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }

		$unit_id_fk = array();
		
		if(isset($this->unit_id_fk) && $this->unit_id_fk!='')
			$unit_id_fk = $this->getUnitID($this->unit_id_fk);
			
        $query->andFilterWhere([
            'mi_id_pk' => $this->mi_id_pk,
            //'unit_id_fk' => $unit_id_fk,            
			'isDeleted' => '0',
			'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
			//'perfomer' => $this->perfomer,
			//'verifier' => $this->verifier,
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'unit_id_fk', $unit_id_fk]);
		
        $query->andFilterWhere(['like', 'mi_step', trim($this->mi_step)])
            ->andFilterWhere(['like', 'mi_action', trim($this->mi_action)])
            ->andFilterWhere(['like', 'mi_range', trim($this->mi_range)])
            ->andFilterWhere(['like', 'target', trim($this->target)])
			->andFilterWhere(['like', 'perfomer', '%'.trim($this->perfomer).'%',false])
			->andFilterWhere(['like', 'verifier', '%'.trim($this->verifier).'%',false]);

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
