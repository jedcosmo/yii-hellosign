<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\documents\models\Documents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="documents-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'docname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'isDeleted')->dropDownList([ '0', '1', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'addedby_person_id_fk')->textInput() ?>

    <?= $form->field($model, 'created_datetime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
