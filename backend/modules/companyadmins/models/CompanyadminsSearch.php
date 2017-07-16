<?php

namespace backend\modules\companyadmins\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\companyadmins\models\Companyadmins;

/**
 * PersonSearch represents the model behind the search form about `backend\modules\person\models\Person`.
 */
class CompanyadminsSearch extends Companyadmins
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id_pk', 'city_id_fk', 'state_id_fk', 'country_id_fk', 'addedby_person_id_fk'], 'integer'],
            [['role_id_fk','first_name', 'last_name', 'phone', 'fax', 'address', 'pobox', 'zip_pincode', 'emailid', 'user_name_person', 'password_person', 'isDeleted', 'created_datetime','super_company_id_fk'], 'safe'],
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
        $query = Companyadmins::find()->joinWith('rolemanagement');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['person_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            //return $dataProvider;
        }
		
		$companyid = '';
		
		if(isset($this->super_company_id_fk) && $this->super_company_id_fk!='')
			$companyid = $this->getCompanyID($this->super_company_id_fk);
			
		
			$query->andFilterWhere([
				//'bpr_person.person_id_pk' => $this->person_id_pk,
				'bpr_person.role_id_fk' => $this->role_id_fk,
				'bpr_person.city_id_fk' => $this->city_id_fk,
				'bpr_person.state_id_fk' => $this->state_id_fk,
				'bpr_person.country_id_fk' => $this->country_id_fk,
				'bpr_person.addedby_person_id_fk' => $this->addedby_person_id_fk,
				'bpr_person.created_datetime' => $this->created_datetime,
				'bpr_person.isDeleted' => '0',
				'bpr_person.super_company_id_fk' => $companyid,
				'bpr_role.is_administrator' => 1,
			]);
		

        $query->andFilterWhere(['like', 'bpr_person.person_id_pk', trim($this->person_id_pk)])
			->andFilterWhere(['like', 'bpr_person.first_name', trim($this->first_name)])
            ->andFilterWhere(['like', 'bpr_person.last_name', trim($this->last_name)])
            ->andFilterWhere(['like', 'bpr_person.phone', trim($this->phone)])
            ->andFilterWhere(['like', 'bpr_person.fax', trim($this->fax)])
            ->andFilterWhere(['like', 'bpr_person.address', trim($this->address)])
            ->andFilterWhere(['like', 'bpr_person.pobox', trim($this->pobox)])
            ->andFilterWhere(['like', 'bpr_person.zip_pincode', trim($this->zip_pincode)])
            ->andFilterWhere(['like', 'bpr_person.emailid', trim($this->emailid)])
            ->andFilterWhere(['like', 'bpr_person.user_name_person', trim($this->user_name_person)])
			->andFilterWhere(['>=', 'bpr_person.role_id_fk', 0]);
			//->andFilterWhere(['!=', 'person_id_pk', Yii::$app->user->id]);

        return $dataProvider;
    }
	
	public function searchExcel($params)
    {
       $query = Companyadmins::find()->joinWith('rolemanagement');

	   $sortOrd = SORT_DESC; $sortBy = 'person_id_pk';
	   
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
	   
	   $companyid = '';
	   if(isset($this->super_company_id_fk) && $this->super_company_id_fk!='')
			$companyid = $this->getCompanyID($this->super_company_id_fk);
		
		   $query->andFilterWhere([
				'bpr_person.person_id_pk' => $this->person_id_pk,
				//'bpr_person.role_id_fk' => $this->role_id_fk,
				'bpr_person.city_id_fk' => $this->city_id_fk,
				'bpr_person.state_id_fk' => $this->state_id_fk,
				'bpr_person.country_id_fk' => $this->country_id_fk,
				'bpr_person.addedby_person_id_fk' => $this->addedby_person_id_fk,
				'bpr_person.created_datetime' => $this->created_datetime,
				'bpr_person.isDeleted' => '0',
				'bpr_person.super_company_id_fk' => $companyid,
				'bpr_role.is_administrator' => 1,
			]);
		
        $query->andFilterWhere(['like', 'bpr_person.first_name', trim($this->first_name)])
            ->andFilterWhere(['like', 'bpr_person.last_name', trim($this->last_name)])
            ->andFilterWhere(['like', 'bpr_person.phone', trim($this->phone)])
            ->andFilterWhere(['like', 'bpr_person.fax', trim($this->fax)])
            ->andFilterWhere(['like', 'bpr_person.address', trim($this->address)])
            ->andFilterWhere(['like', 'bpr_person.pobox', trim($this->pobox)])
            ->andFilterWhere(['like', 'bpr_person.zip_pincode', trim($this->zip_pincode)])
            ->andFilterWhere(['like', 'bpr_person.emailid', trim($this->emailid)])
            ->andFilterWhere(['like', 'bpr_person.user_name_person', trim($this->user_name_person)])
			->andFilterWhere(['>=', 'bpr_person.role_id_fk', 0]);
			//->andFilterWhere(['!=', 'person_id_pk', Yii::$app->user->id]);

		$results = $query->all();
        return $results;
    }
	
	public function getCompanyID($companyname)
	{
		$companyname = trim($companyname);
		$params = [':companyname' => "%".$companyname."%"];
		$Company = Yii::$app->db->createCommand("SELECT company_id_pk FROM bpr_person_company WHERE name like :companyname", $params)->queryOne();
			if($Company['company_id_pk'])
    			$name = $Company['company_id_pk'];
			else
				$name = '0';
		return $name;
	}
	
	public function getRoleID($rolename)
	{
		$rolename = trim($rolename);
		$params = [':rolename' => "%".$rolename."%"];
		$Role = Yii::$app->db->createCommand("SELECT role_id_pk FROM bpr_role WHERE name like :rolename", $params)->queryOne();
			if($Role['role_id_pk'])
    			$name = $Role['role_id_pk'];
			else
				$name = '0';
		return $name;
	}
}
