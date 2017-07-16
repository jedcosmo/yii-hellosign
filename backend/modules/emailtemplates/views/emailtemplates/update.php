<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\modules\emailtemplates\models\Emailtemplates */

$this->title = 'Update Email Template: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Emailtemplates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="emailtemplates-update">

    

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
