<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\emailtemplates\models\Emailtemplates */

$this->title = 'Create Email Template';
$this->params['breadcrumbs'][] = ['label' => 'Emailtemplates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emailtemplates-create">

 

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
