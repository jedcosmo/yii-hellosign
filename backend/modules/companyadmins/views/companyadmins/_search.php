<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\modules\person\models\PersonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="person-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'person_id_pk') ?>

    <?= $form->field($model, 'role_id_fk') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?= $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'fax') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'city_id_fk') ?>

    <?php // echo $form->field($model, 'state_id_fk') ?>

    <?php // echo $form->field($model, 'pobox') ?>

    <?php // echo $form->field($model, 'zip_pincode') ?>

    <?php // echo $form->field($model, 'country_id_fk') ?>

    <?php // echo $form->field($model, 'emailid') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'isDeleted') ?>

    <?php // echo $form->field($model, 'addedby_person_id_fk') ?>

    <?php // echo $form->field($model, 'created_datetime') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
