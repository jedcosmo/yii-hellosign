<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\AdminForgotPasswordForm;
use backend\modules\adminUser\models\Admin;
/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);

$this->title = 'Email Testing';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="login-page">

<?php $this->beginBody() ?>

<div class="login-box">
    <div class="login-logo">
        <a href="#">CLOUD GMP</a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Email Testing</p>
		<p> Enter your e-mail address below to receive email. </p>
		<?php $model = new AdminForgotPasswordForm();?>
		<?php $form = ActiveForm::begin(['options' =>['id' => 'login-form','autocomplete'=>'off']]); ?>

		<div class="form-group">
		<input type="text" name="email" id="email" placeholder="Email" value="" class="form-control">
        </div>
        <?php if(isset($success_msg) && $success_msg!=''){ echo " <div class='form-group alert alert-success' style='color:#3c763d;'>".$success_msg."</div>";} ?>
        <?php if(isset($error_msg) && $error_msg!=''){ echo " <div class='form-group' style='color:#a94442;'>".$error_msg."</div>";} ?>
        
        <div class="form-actions">
         <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3">
            	<?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-flat pull-left', 'name' => 'login-button']) ?> 
            </div>
       	</div>
       
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();exit; ?>
