<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Country;

/* @var $this yii\web\View */
/* @var $model backend\modules\adminUser\models\Admin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admin-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true,'style'=>'width:50%;'])?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true,'style'=>'width:50%;']) ?> 
    
    <?= \cyneek\yii2\widget\upload\crop\UploadCrop::widget(['form' => $form, 'model' => $model, 'attribute' => 'image']) ?>
       
    <?= $image = Html::img(Yii::$app->urlManager->baseUrl . '/uploads/' . $model->image, [
            'alt'=>Yii::t('app', 'Avatar for ') . $model->username,
            'title'=>Yii::t('app', 'Click remove button below to remove this image'),
            'class'=>'file-preview-image',
			'width'=>'150',
			'height'=>'150'
            // add a CSS class to make your image styling consistent
        ]); ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true,'style'=>'width:50%;']) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
