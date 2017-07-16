<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\mprapprovals\models\MprapprovalsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mprapprovals-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'mpr_approval_id_pk') ?>

    <?= $form->field($model, 'mpr_defination_id_fk') ?>

    <?= $form->field($model, 'approval_person_id_fk') ?>

    <?= $form->field($model, 'approval_job_function') ?>

    <?= $form->field($model, 'approval_status') ?>

    <?php // echo $form->field($model, 'approval_datetime') ?>

    <?php // echo $form->field($model, 'verifier_person_id_fk') ?>

    <?php // echo $form->field($model, 'verifier_job_function') ?>

    <?php // echo $form->field($model, 'verified_status') ?>

    <?php // echo $form->field($model, 'verified_datetime') ?>

    <?php // echo $form->field($model, 'document_id_fk') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
