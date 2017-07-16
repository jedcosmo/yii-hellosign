<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\documents\models\Documents */

$this->title = 'Update Documents: ' . ' ' . $model->document_id_pk;
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_id_pk, 'url' => ['view', 'id' => $model->document_id_pk]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="documents-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
