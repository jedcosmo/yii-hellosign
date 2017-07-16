<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\rolemanagement\models\RolemanagementSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rolemanagement-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'role_id_pk') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'modules') ?>

    <?= $form->field($model, 'isDeleted') ?>

    <?= $form->field($model, 'reasonIsDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
