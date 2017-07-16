<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\documents\models\DocumentsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="documents-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'document_id_pk') ?>

    <?= $form->field($model, 'docname') ?>

    <?= $form->field($model, 'isDeleted') ?>

    <?= $form->field($model, 'addedby_person_id_fk') ?>

    <?= $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
