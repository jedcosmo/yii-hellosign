<?php

namespace backend\modules\person\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\person\models\Person;

/**
 * PersonSearch represents the model behind the search form about `backend\modules\person\models\Person`.
 */
class PersonSearchDel extends Person
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id_pk', 'city_id_fk', 'state_id_fk', 'country_id_fk', 'addedby_person_id_fk'], 'integer'],
            [['role_id_fk','first_name', 'last_name', 'phone', 'fax', 'address', 'pobox', 'zip_pincode', 'emailid', 'user_name_person', 'password_person', 'isDeleted', 'created_datetime','reasonIsDeleted','deleted_datetime','super_company_id_fk'], 'safe'],
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
        $query = Person::find();
		$deleted_datetime ='';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['deleted_datetime'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		
		$companyid = '';
	   if(isset($this->super_company_id_fk) && $this->super_company_id_fk!='')
			$companyid = $this->getCompanyID($this->super_company_id_fk);
				
		$dateStr = ''; $deleted_datetime1=''; $deleted_datetime2='';
		if(isset($this->deleted_datetime) && $this->deleted_datetime!='')
		{
			$dateStr = $this->deleted_datetime;
			$newDtArr = explode("|",trim($this->deleted_datetime));
			if(count($newDtArr)>1)
			{
				$deleted_datetime1 = $newDtArr[0];
				$deleted_datetime2 = $newDtArr[1]; 
				$query->andFilterWhere(['>=','deleted_datetime',$deleted_datetime1]);
				$query->andFilterWhere(['<=','deleted_datetime',$deleted_datetime2]);
			}
			else
			{
				$query->andFilterWhere(['like','deleted_datetime',$dateStr]);
			}
		}
			
		$roleidfk = array();
		if(isset($this->role_id_fk) && $this->role_id_fk!='')
			$roleidfk = $this->getRoleID($this->role_id_fk);

		if(Yii::$app->user->identity->is_super_admin==1)
		{
		
			$query->andFilterWhere([
				'city_id_fk' => $this->city_id_fk,
				'state_id_fk' => $this->state_id_fk,
				'country_id_fk' => $this->country_id_fk,
				'addedby_person_id_fk' => $this->addedby_person_id_fk,
				'created_datetime' => $this->created_datetime,
				'isDeleted' => '1',
				'bpr_person.super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
		}
		else
		{
			$query->andFilterWhere([
				'city_id_fk' => $this->city_id_fk,
				'state_id_fk' => $this->state_id_fk,
				'country_id_fk' => $this->country_id_fk,
				'addedby_person_id_fk' => $this->addedby_person_id_fk,
				'created_datetime' => $this->created_datetime,
				'isDeleted' => '1',
				'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
		}
		
		$query->andFilterWhere(['in', 'bpr_person.role_id_fk', $roleidfk]);

        $query->andFilterWhere(['like', 'bpr_person.person_id_pk', trim($this->person_id_pk)])
		    ->andFilterWhere(['like', 'first_name', trim($this->first_name)])
            ->andFilterWhere(['like', 'last_name', trim($this->last_name)])
            ->andFilterWhere(['like', 'phone', trim($this->phone)])
            ->andFilterWhere(['like', 'fax', trim($this->fax)])
            ->andFilterWhere(['like', 'address', trim($this->address)])
            ->andFilterWhere(['like', 'pobox', trim($this->pobox)])
            ->andFilterWhere(['like', 'zip_pincode', trim($this->zip_pincode)])
            ->andFilterWhere(['like', 'emailid', trim($this->emailid)])
            ->andFilterWhere(['like', 'user_name_person', trim($this->user_name_person)])
            ->andFilterWhere(['like', 'password_person', trim($this->password_person)])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
			->andFilterWhere(['>=', 'role_id_fk', 0]);

        return $dataProvider;
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
		$name = array();
		$rolename = trim($rolename);
		$params = [':rolename' => "%".$rolename."%"];
		$Role = Yii::$app->db->createCommand("SELECT role_id_pk FROM bpr_role WHERE name like :rolename", $params)->queryAll();
		if(is_array($Role) && count($Role)>0)
		{
			foreach($Role as $k=>$v)
			{
				if($v['role_id_pk'])
    				$name[] = $v['role_id_pk'];
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
