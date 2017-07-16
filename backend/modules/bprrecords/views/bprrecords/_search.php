<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\bprrecords\models\BprrecordsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bprrecords-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'bpr_id_pk') ?>

    <?= $form->field($model, 'batch') ?>

    <?= $form->field($model, 'manufacturing_date') ?>

    <?= $form->field($model, 'product_id_fk') ?>

    <?= $form->field($model, 'product_code') ?>

    <?php // echo $form->field($model, 'mpr_version') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
