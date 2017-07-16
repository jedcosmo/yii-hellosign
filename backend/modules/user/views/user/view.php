<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'View User: '.$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

   
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
           // 'user_type_id_fk',
            //'you_are',
            'username',
           // 'auth_key',
           // 'password_hash',
           // 'password_reset_token',
            'email:email',
            'firstname',
            'lastname',
            'mobileno',
           // 'nricno',
           // 'hearAboutUs',
            //'captcha',
           // 'status',
        ],
    ]) ?>

</div>
