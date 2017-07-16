<?php

namespace backend\modules\rolemanagement\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\rolemanagement\models\Rolemanagement;

/**
 * RolemanagementSearch represents the model behind the search form about `backend\modules\rolemanagement\models\Rolemanagement`.
 */
class RolemanagementSearch extends Rolemanagement
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role_id_pk'], 'integer'],
            [['name', 'modules', 'isDeleted', 'reasonIsDeleted', 'created_datetime'], 'safe'],
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
		
		$rolemodules = '';
		if(isset($this->modules) && $this->modules!='')
		{
			$rolemodules = $this->getRoleDisplay($this->modules);
			if(count($rolemodules)==1 && $rolemodules[0]=='0')
				$rolemodules = $this->modules;
		}
			
		//print_r($rolemodules); 
        $query->andFilterWhere([
            'role_id_pk' => $this->role_id_pk,
			'isDeleted' => '0',
			'is_administrator' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		//$query->andFilterWhere(['in', 'modules', $rolemodules]);
		
        $query->andFilterWhere(['like', 'name', trim($this->name)])
			->andFilterWhere(['>', 'role_id_pk', '-1'])
            ->andFilterWhere(['like', 'modules', $rolemodules])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
            ->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        return $dataProvider;
    }
	
	
	public function searchExcel($params)
    {
        $query = Rolemanagement::find();
		
		 $sortOrd = SORT_DESC; $sortBy = 'role_id_pk';
	   
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

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
           //  return $dataProvider;
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
			'isDeleted' => '0',
			'is_administrator' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);
		
		//$query->andFilterWhere(['in', 'modules', $rolemodules]);

        $query->andFilterWhere(['like', 'name', trim($this->name)])
			->andFilterWhere(['>', 'role_id_pk', '-1'])
            ->andFilterWhere(['like', 'modules', $rolemodules])
            ->andFilterWhere(['like', 'isDeleted', trim($this->isDeleted)])
            ->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

        $results = $query->all();
        return $results;
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
				//print_r($Roles);
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
		//echo $name = implode(",",$name); 
		return $name;
	}
}
