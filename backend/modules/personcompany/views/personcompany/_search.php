<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\company\models\CompanySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'company_id_pk') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'address1') ?>

    <?= $form->field($model, 'address2') ?>

    <?= $form->field($model, 'pobox') ?>

    <?php // echo $form->field($model, 'city_id_fk') ?>

    <?php // echo $form->field($model, 'state_id_fk') ?>

    <?php // echo $form->field($model, 'zip_postalcode') ?>

    <?php // echo $form->field($model, 'country_id_fk') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
