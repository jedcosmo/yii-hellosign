<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprdefinition\models\MprdefinitionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mprdefination-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'mpr_defination_id_pk') ?>

    <?= $form->field($model, 'product_id_fk') ?>

    <?= $form->field($model, 'product_code') ?>

    <?= $form->field($model, 'MPR_version') ?>

    <?= $form->field($model, 'product_part') ?>

    <?php // echo $form->field($model, 'author') ?>

    <?php // echo $form->field($model, 'product_name') ?>

    <?php // echo $form->field($model, 'formulation_id') ?>

    <?php // echo $form->field($model, 'product_strength') ?>

    <?php // echo $form->field($model, 'batch_size') ?>

    <?php // echo $form->field($model, 'MRP_unit_id') ?>

    <?php // echo $form->field($model, 'theoritical_yield') ?>

    <?php // echo $form->field($model, 'company_id_fk') ?>

    <?php // echo $form->field($model, 'purpose') ?>

    <?php // echo $form->field($model, 'scope') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
