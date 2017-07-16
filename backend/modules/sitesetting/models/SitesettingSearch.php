<?php

namespace backend\modules\sitesetting\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\sitesetting\models\Sitesetting;

/**
 * SitesettingSearch represents the model behind the search form about `backend\modules\sitesetting\models\Sitesetting`.
 */
class SitesettingSearch extends Sitesetting
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['type', 'value'], 'safe'],
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
        $query = Sitesetting::find();

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
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'value', $this->value]);

        return $dataProvider;
    }
}
