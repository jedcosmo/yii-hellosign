<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\mprapprovals\models\MprapprovalsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'MPR APPROVALS';
$this->params['breadcrumbs'][] = 'MPR Approvals';
?>
<div class="mprapprovals-index">
	<p class="text-right">
        <?= Html::a('<i class="fa fa-plus"></i>&nbsp;<span>Add MPR Approval</span>', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'pager' => [
					'firstPageLabel' => '&laquo;',
					'lastPageLabel' => '&raquo;',
					'nextPageLabel' => '&rsaquo;',
					'prevPageLabel' => '&lsaquo;',
					],	
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'mpr_approval_id_pk',
           // 'mpr_defination_id_fk',
            'approval_person_id_fk',
            'approval_job_function',
           // 'approval_status',
             'approval_datetime',
             'verifier_person_id_fk',
             'verifier_job_function',
            // 'verified_status',
             'verified_datetime',
            // 'document_id_fk',
            // 'isDeleted',
            // 'addedby_person_id_fk',
            // 'created_datetime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
