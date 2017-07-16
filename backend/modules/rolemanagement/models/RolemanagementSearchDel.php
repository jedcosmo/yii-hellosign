<?php

namespace backend\modules\rolemanagement\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rolemanagement\models\Rolemanagement;

/**
 * RolemanagementSearch represents the model behind the search form about `backend\modules\rolemanagement\models\Rolemanagement`.
 */
class RolemanagementSearchDel extends Rolemanagement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id_pk', 'addedby_person_id_fk'], 'integer'],
            [['name', 'modules', 'isDeleted', 'reasonIsDeleted', 'created_datetime','deleted_datetime'], 'safe'],
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
        $query = Rolemanagement::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['role_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }
		$deleted_datetime ='';
			
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
			
		$rolemodules = '';
		if(isset($this->modules) && $this->modules!='')
		{
			$rolemodules = $this->getRoleDisplay($this->modules);
			if(count($rolemodules)==1 && $rolemodules[0]=='0')
				$rolemodules = $this->modules;
		}

        $query->andFilterWhere([
            'role_id_pk' => $this->role_id_pk,
            'addedby_person_id_fk' => $this->addedby_person_id_fk,
            'created_datetime' => $this->created_datetime,
			'isDeleted' => '1',
			'is_administrator' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
			->andFilterWhere(['>', 'role_id_pk', '-1'])
            ->andFilterWhere(['like', 'modules', trim($rolemodules)])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
            ->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        return $dataProvider;
    }
	
	public function getRoleDisplay($rolesearch)
	{
		$name = array();
		$rolesearch = trim($rolesearch);
		$rolesearch = rtrim($rolesearch,",");
		$rolesearch = explode(",",$rolesearch);
		
		if(is_array($rolesearch) && count($rolesearch)>0)
		{
			foreach($rolesearch as $k=>$v)
			{
				$params = [':rolename' => '%'.trim($v).'%'];
				$Roles = Yii::$app->db->createCommand("SELECT name_pk FROM bpr_role_modules WHERE display_name like :rolename", $params)->queryAll();
				if(is_array($Roles) && count($Roles)>0)
				{
					foreach($Roles as $k=>$v)
					{
						if($v['name_pk'])
							$name[] = $v['name_pk'];
						else
							$name[] = 0;
					}
				}
				else
				{
					$name[] = 0;
				}
			}
		}
		else
		{
			$name[] = 0;
		}
		array_filter($name);
		$name = implode(",",$name); 
		return $name;
	}
}
