<?php
use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\AdminForgotPasswordForm;
use backend\modules\adminUser\models\Admin;
/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);

$this->title = 'Forgot Password';

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
        <p class="login-box-msg">Forgot Password ?</p>
		<p> Enter your e-mail address below to reset your password. </p>
		<?php $model = new AdminForgotPasswordForm();?>
		<?php $form = ActiveForm::begin(['options' =>['id' => 'login-form','autocomplete'=>'off']]); ?>

        <?= $form->field($model, 'email',['inputOptions' => ['placeholder' => 'Email',]])->label(false); ?>
        
        <?php if(isset($success_msg) && $success_msg!=''){ echo " <div class='form-group' style='color:#3c763d;'>".$success_msg."</div>";} ?>
        <?php if(isset($error_msg) && $error_msg!=''){ echo " <div class='form-group' style='color:#a94442;'>".$error_msg."</div>";} ?>
        
        <div class="form-actions">
         <div class="row">
            <div class="col-xs-3 col-sm-3 col-md-3">
            	<?= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-flat pull-left', 'name' => 'login-button']) ?> 
            </div>
            <div class="col-xs-9 col-sm-9 col-md-9">
                 <a id="back-btn" class="btn btn-default btn-flat pull-left" href="<?php echo Yii::$app->homeUrl;?>site/login">Cancel</a>
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
