<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\equipmentmap\models\EquipmentmapSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="equipmentmap-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'equipment_map_id_pk') ?>

    <?= $form->field($model, 'mpr_defination_id_fk') ?>

    <?= $form->field($model, 'product_id_fk') ?>

    <?= $form->field($model, 'product_code') ?>

    <?= $form->field($model, 'equipment_id_fk') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
