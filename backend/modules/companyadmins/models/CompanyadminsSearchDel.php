<?php

namespace backend\modules\companyadmins\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\companyadmins\models\Companyadmins;

/**
 * PersonSearch represents the model behind the search form about `backend\modules\person\models\Person`.
 */
class CompanyadminsSearchDel extends Companyadmins
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['person_id_pk',  'city_id_fk', 'state_id_fk', 'country_id_fk', 'addedby_person_id_fk'], 'integer'],
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
        $query = Companyadmins::find()->joinWith('rolemanagement');
		$deleted_datetime ='';
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['deleted_datetime'=>SORT_DESC]]
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
			

		$dateStr = ''; $deleted_datetime1=''; $deleted_datetime2='';
		if(isset($this->deleted_datetime) && $this->deleted_datetime!='')
		{
			$dateStr = $this->deleted_datetime;
			$newDtArr = explode("|",trim($this->deleted_datetime));
			if(count($newDtArr)>1)
			{
				$deleted_datetime1 = $newDtArr[0];
				$deleted_datetime2 = $newDtArr[1]; 
				$query->andFilterWhere(['>=','bpr_person.deleted_datetime',$deleted_datetime1]);
				$query->andFilterWhere(['<=','bpr_person.deleted_datetime',$deleted_datetime2]);
			}
			else
			{
				$query->andFilterWhere(['like','bpr_person.deleted_datetime',$dateStr]);
			}
		}
		
			$query->andFilterWhere([
				'bpr_person.role_id_fk' => $this->role_id_fk,
				'bpr_person.city_id_fk' => $this->city_id_fk,
				'bpr_person.state_id_fk' => $this->state_id_fk,
				'bpr_person.country_id_fk' => $this->country_id_fk,
				'bpr_person.addedby_person_id_fk' => $this->addedby_person_id_fk,
				'bpr_person.created_datetime' => $this->created_datetime,
				'bpr_person.isDeleted' => '1',
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
            ->andFilterWhere(['like', 'bpr_person.password_person', trim($this->password_person)])
			->andFilterWhere(['like', 'bpr_person.reasonIsDeleted', trim($this->reasonIsDeleted)])
            ->andFilterWhere(['like', 'bpr_person.isDeleted', trim($this->isDeleted)])
			->andFilterWhere(['>=', 'bpr_person.role_id_fk', 0]);

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
}
