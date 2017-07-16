<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipment\models\EquipmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'equipment_id_pk') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'model') ?>

    <?= $form->field($model, 'serial') ?>

    <?= $form->field($model, 'caliberation_due_date') ?>

    <?php // echo $form->field($model, 'preventive_m_due_date') ?>

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
