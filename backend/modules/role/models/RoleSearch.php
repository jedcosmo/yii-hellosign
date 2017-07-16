<?php

namespace backend\modules\role\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\role\models\Role;

/**
 * RoleSearch represents the model behind the search form about `backend\modules\role\models\Role`.
 */
class RoleSearch extends Role
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id_pk', 'addedby_person_id_fk'], 'integer'],
            [['name', 'modules', 'isDeleted', 'created_datetime'], 'safe'],
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
        $query = Role::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'role_id_pk' => $this->role_id_pk,
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'modules', $this->modules])
            ->andFilterWhere(['like', 'isDeleted', $this->isDeleted]);

        return $dataProvider;
    }
}
