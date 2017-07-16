<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\sitesetting\models\Sitesetting */

$this->title = "View Site Information: ".$model->id;
$this->params['breadcrumbs'][] = ['label' => 'Site Information', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sitesetting-view">

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
            'type',
            'value',
        ],
    ]) ?>

</div>
