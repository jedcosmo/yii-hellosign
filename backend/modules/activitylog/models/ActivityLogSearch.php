<?php

namespace backend\modules\activitylog\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\activitylog\models\ActivityLog;

/**
 * ActivityLogSearch represents the model behind the search form about `backend\modules\activitylog\models\ActivityLog`.
 */
class ActivityLogSearch extends ActivityLog
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userid','type', 'action', 'message', 'added_date','urltext'], 'safe'],
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
        $query = ActivityLog::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
			'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]]
        ]);

		
        $this->load($params);
		
		$search_by = isset($_GET['search_by'])?$_GET['search_by']:'';
		$search_for = isset($_GET['search_for'])?$_GET['search_for']:'';
		$search_for_person = isset($_GET['search_for_person'])?$_GET['search_for_person']:'';
		$search_for_action = isset($_GET['search_for_action'])?$_GET['search_for_action']:'';
		$search_for_Screen_Name = isset($_GET['search_for_Screen_Name'])?$_GET['search_for_Screen_Name']:'';
		$from_date = (isset($_GET['from_date'])&&$_GET['from_date']!='')?date("Y-m-d H:i:s",strtotime($_GET['from_date'])):'';
		$to_date = (isset($_GET['to_date'])&&$_GET['to_date']!='')?date("Y-m-d H:i:s",strtotime($_GET['to_date'])):'';

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // return $dataProvider;
        }

		$added_this_date = (isset($this->added_date) && $this->added_date!='')?date("Y-m-d H:i:s",strtotime($this->added_date)):'';

		if(Yii::$app->user->identity->is_super_admin==1)
		{
			$query->andFilterWhere([
				'id' => $this->id,
				'userid' => $this->userid,
				'added_date' => $added_this_date,
				'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
		}
		else
		{
			$query->andFilterWhere([
				'id' => $this->id,
				'userid' => $this->userid,
				'added_date' => $added_this_date,
				'super_company_id_fk' => Yii::$app->user->identity->super_company_id_fk,
			]);
		}
		
		switch($search_by)
		{
			case 'Person_ID':
							$query->andFilterWhere(['userid' => $search_for_person]);
							break;
			case 'Action':
							$query->andFilterWhere(['action' => $search_for_action]);
							break;
			case 'Date_Time':
							if($search_for!='')
							{
								$query->andFilterWhere(['added_date' => date("Y-m-d H:i:s",strtotime($search_for))]);
							}
							break;
			case 'Screen_Name':
							$query->andFilterWhere(['type' => $search_for_Screen_Name]);
							break;
		}
		
		if(isset($from_date) && $from_date!='' && isset($to_date) && $to_date!='')
		{
			$query->andFilterWhere(['between', 'added_date', $from_date, $to_date]);
		}

        $query->andFilterWhere(['like', 'type', trim($this->type)])
            ->andFilterWhere(['like', 'action', trim($this->action)])
            ->andFilterWhere(['like', 'message', trim($this->message)])
			->andFilterWhere(['like', 'urltext', trim($this->urltext)]);

        return $dataProvider;
    }
}
