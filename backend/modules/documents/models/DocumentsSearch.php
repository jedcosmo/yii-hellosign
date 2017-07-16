<?php

namespace backend\modules\documents\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\documents\models\Documents;

/**
 * DocumentsSearch represents the model behind the search form about `backend\modules\documents\models\Documents`.
 */
class DocumentsSearch extends Documents
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id_pk', 'addedby_person_id_fk'], 'integer'],
            [['docname', 'isDeleted', 'created_datetime'], 'safe'],
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
        $query = Documents::find();

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
            'document_id_pk' => $this->document_id_pk,
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
        ]);

        $query->andFilterWhere(['like', 'docname', $this->docname])
            ->andFilterWhere(['like', 'isDeleted', $this->isDeleted]);

        return $dataProvider;
    }
}
