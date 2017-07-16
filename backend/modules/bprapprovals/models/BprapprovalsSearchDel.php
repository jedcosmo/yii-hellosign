<?php

namespace backend\modules\bprapprovals\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\bprapprovals\models\Bprapprovals;

/**
 * MprapprovalsSearch represents the model behind the search form about `backend\modules\mprapprovals\models\Mprapprovals`.
 */
class BprapprovalsSearchDel extends Bprapprovals
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['approval_person_id_fk', 'approval_job_function', 'approval_datetime', 'verifier_person_id_fk', 'verifier_job_function', 'verified_datetime','reasonIsDeleted','deleted_datetime'], 'safe'],
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
        $query = Bprapprovals::find();
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

		$dateStr = ''; $deleted_datetime1=''; $deleted_datetime2='';
		if(isset($this->deleted_datetime) && $this->deleted_datetime!='')
		{
			$dateStr = str_replace("/","-",trim($this->deleted_datetime));
			if(strlen($dateStr)>1)
				$dateStr = rtrim($dateStr,"-");
			$deleted_datetime1 = date("Y-m-d H:i:s",strtotime(trim($dateStr)));
			$deleted_datetime2 = date("Y-m-d",strtotime(trim($this->deleted_datetime)));
		}

		$apprStr = ''; $appr_datetime1=''; $appr_datetime2='';
		if(isset($this->approval_datetime) && $this->approval_datetime!='')
		{
			$apprStr = str_replace("/","-",trim($this->approval_datetime));
			if(strlen($apprStr)>1)
				$apprStr = rtrim($apprStr,"-");
			$appr_datetime1 = date("Y-m-d H:i:s",strtotime(trim($apprStr)));
			$appr_datetime2 = date("Y-m-d",strtotime(trim($this->approval_datetime)));
		}
		
		$veriStr = ''; $veri_datetime1=''; $veri_datetime2='';
		if(isset($this->verified_datetime) && $this->verified_datetime!='')
		{
			$veriStr = str_replace("/","-",trim($this->verified_datetime));
			if(strlen($veriStr)>1)
				$veriStr = rtrim($veriStr,"-");
			$veri_datetime1 = date("Y-m-d H:i:s",strtotime(trim($veriStr)));
			$veri_datetime2 = date("Y-m-d",strtotime(trim($this->verified_datetime)));
		}


		$performer_id_fk = array(); $verifier_id_fk = array();
		
		if(isset($this->approval_person_id_fk) && $this->approval_person_id_fk!='')
			$performer_id_fk = $this->getPersonID($this->approval_person_id_fk);
			
		if(isset($this->verifier_person_id_fk) && $this->verifier_person_id_fk!='')
			$verifier_id_fk = $this->getPersonID($this->verifier_person_id_fk);
			
        $query->andFilterWhere([
            'bpr_approval_id_pk' => $this->bpr_approval_id_pk,
            'bpr_id_fk' => $this->bpr_id_fk,
          	'isDeleted' => '1',
			'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
        ]);

		$query->andFilterWhere(['in', 'approval_person_id_fk', $performer_id_fk]);
		$query->andFilterWhere(['in', 'verifier_person_id_fk', $verifier_id_fk]);
		
        $query->andFilterWhere(['like', 'approval_job_function', trim($this->approval_job_function)])
            ->andFilterWhere(['like', 'approval_status', trim($this->approval_status)])
            ->andFilterWhere(['like', 'verifier_job_function', trim($this->verifier_job_function)])
            ->andFilterWhere(['like', 'verified_status', trim($this->verified_status)])
			->andFilterWhere(['or',
            					['like','approval_datetime',$apprStr],
            					['like','approval_datetime',$appr_datetime1],
								['like','approval_datetime',$appr_datetime2]
							])
			->andFilterWhere(['or',
            					['like','verified_datetime',$veriStr],
            					['like','verified_datetime',$veri_datetime1],
								['like','verified_datetime',$veri_datetime2]
							])
			->andFilterWhere(['or',
            					['like','deleted_datetime',$dateStr],
            					['like','deleted_datetime',$deleted_datetime1],
								['like','deleted_datetime',$deleted_datetime2]
							])
			->andFilterWhere(['like', 'reasonIsDeleted', trim($this->reasonIsDeleted)]);

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
