<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\modules\sitesetting\models\Sitesetting */

$this->title = 'Create Site Information';
$this->params['breadcrumbs'][] = ['label' => 'Sitesettings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sitesetting-create">

   

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
