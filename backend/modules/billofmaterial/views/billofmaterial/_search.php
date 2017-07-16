<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\billofmaterial\models\BillofmaterialSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="billofmaterial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'bom_id_pk') ?>

    <?= $form->field($model, 'material_name') ?>

    <?= $form->field($model, 'qty_branch') ?>

    <?= $form->field($model, 'qb_unit_id_fk') ?>

    <?= $form->field($model, 'composition') ?>

    <?php // echo $form->field($model, 'com_unit_id_fk') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
