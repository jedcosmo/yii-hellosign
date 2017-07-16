<?php

namespace backend\modules\myprofile\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\myprofile\models\Myprofile;

/**
 * PersonSearch represents the model behind the search form about `backend\modules\person\models\Person`.
 */
class MyprofileSearch extends Person
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id_pk', 'role_id_fk', 'city_id_fk', 'state_id_fk', 'country_id_fk', 'addedby_person_id_fk'], 'integer'],
            [['first_name', 'last_name', 'phone', 'fax', 'address', 'pobox', 'zip_pincode', 'emailid', 'user_name_person', 'password_person', 'isDeleted', 'created_datetime','super_company_id_fk'], 'safe'],
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
        $query = Myprofile::find()->joinWith('rolemanagement');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['person_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		
		$companyid = '';
		
		if(isset($this->super_company_id_fk) && $this->super_company_id_fk!='')
			$companyid = $this->getCompanyID($this->super_company_id_fk);
			
		if(Yii::$app->user->identity->is_super_admin==1)
		{
			$query->andFilterWhere([
				'bpr_person.person_id_pk' => $this->person_id_pk,
				'bpr_person.role_id_fk' => $this->role_id_fk,
				'bpr_person.city_id_fk' => $this->city_id_fk,
				'bpr_person.state_id_fk' => $this->state_id_fk,
				'bpr_person.country_id_fk' => $this->country_id_fk,
				'bpr_person.addedby_person_id_fk' => $this->addedby_person_id_fk,
				'bpr_person.created_datetime' => $this->created_datetime,
				'bpr_person.isDeleted' => '0',
				'bpr_person.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
		}
		else
		{
			$query->andFilterWhere([
				'bpr_person.person_id_pk' => $this->person_id_pk,
				'bpr_person.role_id_fk' => $this->role_id_fk,
				'bpr_person.city_id_fk' => $this->city_id_fk,
				'bpr_person.state_id_fk' => $this->state_id_fk,
				'bpr_person.country_id_fk' => $this->country_id_fk,
				'bpr_person.addedby_person_id_fk' => $this->addedby_person_id_fk,
				'bpr_person.created_datetime' => $this->created_datetime,
				'bpr_person.isDeleted' => '0',
				'bpr_person.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
				//'person_id_pk' => Yii::$app->user->id,
			]);
		}

        $query->andFilterWhere(['like', 'bpr_person.first_name', $this->first_name])
            ->andFilterWhere(['like', 'bpr_person.last_name', $this->last_name])
            ->andFilterWhere(['like', 'bpr_person.phone', $this->phone])
            ->andFilterWhere(['like', 'bpr_person.fax', $this->fax])
            ->andFilterWhere(['like', 'bpr_person.address', $this->address])
            ->andFilterWhere(['like', 'bpr_person.pobox', $this->pobox])
            ->andFilterWhere(['like', 'bpr_person.zip_pincode', $this->zip_pincode])
            ->andFilterWhere(['like', 'bpr_person.emailid', $this->emailid])
            ->andFilterWhere(['like', 'bpr_person.user_name_person', $this->user_name_person])
			->andFilterWhere(['>=', 'bpr_person.role_id_fk', 0]);
			//->andFilterWhere(['!=', 'person_id_pk', Yii::$app->user->id]);

        return $dataProvider;
    }
	
	
	
	public function getCompanyID($companyname)
	{
		$params = [':companyname' => "%".$companyname."%"];
		$Company = Yii::$app->db->createCommand("SELECT company_id_pk FROM bpr_person_company WHERE name like :companyname", $params)->queryOne();
			if($Company['company_id_pk'])
    			$name = $Company['company_id_pk'];
			else
				$name = '0';
		return $name;
	}
}
