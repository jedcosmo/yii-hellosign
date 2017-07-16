<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\minstructions\models\MinstructionsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="minstructions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'mi_id_pk') ?>

    <?= $form->field($model, 'mi_step') ?>

    <?= $form->field($model, 'mi_action') ?>

    <?= $form->field($model, 'unit_id_fk') ?>

    <?= $form->field($model, 'mi_range') ?>

    <?php // echo $form->field($model, 'target') ?>

    <?php // echo $form->field($model, 'perfomer') ?>

    <?php // echo $form->field($model, 'verifier') ?>

    <?php // echo $form->field($model, 'document_id_fk') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_persona_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
