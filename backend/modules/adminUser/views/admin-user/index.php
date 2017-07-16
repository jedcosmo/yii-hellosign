<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\adminUser\models\AdminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Admin';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">

    <!---<h1><?php // Html::encode($this->title) ?></h1>--->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Admin', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //'id',
            'firstname',
            'lastname',
            //'nickname',
            'username',
            // 'auth_key',
            // 'password_hash',
            // 'password_reset_token',
            // 'image',
             'email:email',
             'companyname',
            // 'company_registrationno',
            // 'address1',
            // 'address2',
            // 'postalcode',
            // 'country_id',
            // 'telephone',

            ['class' => 'yii\grid\ActionColumn','template'=>'{view}{update}'],
        ],
    ]); ?>

</div>
