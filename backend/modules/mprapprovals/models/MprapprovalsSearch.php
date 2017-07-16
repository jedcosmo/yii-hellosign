<?php

namespace backend\modules\mprapprovals\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\mprapprovals\models\Mprapprovals;

/**
 * MprapprovalsSearch represents the model behind the search form about `backend\modules\mprapprovals\models\Mprapprovals`.
 */
class MprapprovalsSearch extends Mprapprovals
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['approval_person_id_fk', 'approval_job_function', 'approval_datetime', 'verifier_person_id_fk', 'verifier_job_function', 'verified_datetime'], 'safe'],
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
        $query = Mprapprovals::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['mpr_approval_id_pk'=>SORT_DESC]]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }

		$performer_id_fk = array(); $verifier_id_fk = array();
		
		if(isset($this->approval_person_id_fk) && $this->approval_person_id_fk!='')
			$performer_id_fk = $this->getPersonID($this->approval_person_id_fk);
			
		if(isset($this->verifier_person_id_fk) && $this->verifier_person_id_fk!='')
			$verifier_id_fk = $this->getPersonID($this->verifier_person_id_fk);
			
		$apprStr = ''; $appr_datetime1=''; $appr_datetime2='';
		if(isset($this->approval_datetime) && $this->approval_datetime!='')
		{
			$apprStr = $this->approval_datetime;
			$apprDtArr = explode("|",trim($this->approval_datetime));
			if(count($apprDtArr)>1)
			{ 
				$query->andFilterWhere(['>=','approval_datetime',$apprDtArr[0]]);
				$query->andFilterWhere(['<=','approval_datetime',$apprDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','approval_datetime',$apprStr]);
			}
		}
		
		$veriStr = ''; $veri_datetime1=''; $veri_datetime2='';
		if(isset($this->verified_datetime) && $this->verified_datetime!='')
		{
			$veriStr = $this->verified_datetime;
			$veriDtArr = explode("|",trim($this->verified_datetime));
			if(count($veriDtArr)>1)
			{ 
				$query->andFilterWhere(['>=','verified_datetime',$veriDtArr[0]]);
				$query->andFilterWhere(['<=','verified_datetime',$veriDtArr[1]]);
			}
			else
			{
				$query->andFilterWhere(['like','verified_datetime',$veriStr]);
			}
		}
			
        $query->andFilterWhere([
            'mpr_approval_id_pk' => $this->mpr_approval_id_pk,
            'mpr_defination_id_fk' => $this->mpr_defination_id_fk,
          	'isDeleted' => '0',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);
		
		$query->andFilterWhere(['in', 'approval_person_id_fk', $performer_id_fk]);
		$query->andFilterWhere(['in', 'verifier_person_id_fk', $verifier_id_fk]);

        $query->andFilterWhere(['like', 'approval_job_function', trim($this->approval_job_function)])
            ->andFilterWhere(['like', 'approval_status', trim($this->approval_status)])
            ->andFilterWhere(['like', 'verifier_job_function', trim($this->verifier_job_function)])
            ->andFilterWhere(['like', 'verified_status', trim($this->verified_status)]);

        return $dataProvider;
    }
	
	public function getPersonID($personname)
	{
		$personname = trim($personname);
		$id = array();
		$params = [':personname' => '%'.$personname.'%'];
		$Person = Yii::$app->db->createCommand('SELECT person_id_pk FROM bpr_person WHERE concat_ws(" ",first_name,last_name) like :personname', $params)->queryAll();
		if(is_array($Person) && count($Person)>0)
		{
			foreach($Person as $k=>$v)
			{
				if($v['person_id_pk'])
    				$id[] = $v['person_id_pk'];
				else
					$id[] = 0;
			}
		}
		else
		{
			$id[] = 0;
		}
		return $id;
	}
}
