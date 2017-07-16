<?php 

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;
use backend\modules\minstructions\models\Minstructions;

$rflag = checkCommonFunctions::check_If_MPR_Approved($model->mpr_defination_id_pk);
$isMDeleted = checkCommonFunctions::check_If_Record_Deleted($model->mpr_defination_id_pk,'bpr_mpr_defination','mpr_defination_id_pk');


$gridactTpl = '{view}';


$commonModel = new getCommonFunctions();
?>

<?= GridView::widget([
    'dataProvider' => $minstdataProvider,
    'filterModel' => $minstsearchModel,
    'pager' => [
                    'firstPageLabel' => '&laquo;',
                    'lastPageLabel' => '&raquo;',
                    'nextPageLabel' => '&rsaquo;',
                    'prevPageLabel' => '&lsaquo;',
                    ],	
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        [
            'attribute'=>'mi_step',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:100px;word-wrap:break-word;'],
            'value' => 'mi_step'
        ],
        [
            'attribute'=>'mi_action',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:150px;word-wrap:break-word;'],
            'value' => 'mi_action'
        ],
        [
            'attribute'=>'unit_id_fk',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:100px;word-wrap:break-word;'],
            'value' => function($data){
                    $commonModel = new getCommonFunctions();
                    return $unitnm = $commonModel->getFieldNameValue("bpr_unit","name","unit_id_pk",$data->unit_id_fk);
                },
        ],
        [
            'attribute'=>'mi_range',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:70px;word-wrap:break-word;'],
            'value' => 'mi_range'
        ],
        [
            'attribute'=>'target',
            'format' => 'raw',
            'contentOptions' =>['style'=>'max-width:70px;word-wrap:break-word;'],
            'value' => 'target'
        ],
        [
            'attribute'=>'perfomer',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:50px;word-wrap:break-word;'],
            'value' => 'perfomer'
        ],
        [
            'attribute'=>'verifier',
            'format' => 'raw',
            'contentOptions' =>['style'=>'width:50px;word-wrap:break-word;'],
            'value' => 'verifier'
        ],
        [				
            'class' => 'yii\grid\ActionColumn',
            'template' => $gridactTpl,
            'buttons' => [
                'view' => function ($url) {
                    $minstid=  parse_url($url,PHP_URL_QUERY);
                    return Html::a(
                        '<span class="glyphicon glyphicon-eye-open"></span>',
                        Yii::$app->homeUrl.'minstructions/minstructions/view?'.$minstid.'&mprid='.$_GET['id'], 
                        [
                            'title' => 'View',
                            'data-pjax' => '0',
                        ]
                    );
                },
            ],
        ],
    ],
]); ?>
