<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_type_id_fk', 'status'], 'integer'],
            [[ 'username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'firstname', 'lastname', 'mobileno'], 'safe'],
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
        $query = User::find();

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
            'user_type_id_fk' => $this->user_type_id_fk,
            'status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'you_are', $this->you_are])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key])
            ->andFilterWhere(['like', 'password_hash', $this->password_hash])
            ->andFilterWhere(['like', 'password_reset_token', $this->password_reset_token])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'firstname', $this->firstname])
            ->andFilterWhere(['like', 'lastname', $this->lastname])
            ->andFilterWhere(['like', 'mobileno', $this->mobileno])
            ->andFilterWhere(['like', 'gender', $this->gender])
            ->andFilterWhere(['like', 'nricno', $this->nricno])
            ->andFilterWhere(['like', 'hearAboutUs', $this->hearAboutUs])
            ->andFilterWhere(['like', 'captcha', $this->captcha]);

        return $dataProvider;
    }
}
