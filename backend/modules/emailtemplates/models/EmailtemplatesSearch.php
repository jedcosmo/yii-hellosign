<?php

namespace backend\modules\emailtemplates\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\emailtemplates\models\Emailtemplates;

/**
 * EmailtemplatesSearch represents the model behind the search form about `backend\modules\emailtemplates\models\Emailtemplates`.
 */
class EmailtemplatesSearch extends Emailtemplates
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['subject', 'message', 'added_date'], 'safe'],
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
        $query = Emailtemplates::find();

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
            'added_date' => $this->added_date,
        ]);

        $query->andFilterWhere(['like', 'subject', $this->subject])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
