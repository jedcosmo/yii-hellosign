<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;     
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar'); ?>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Completed - Atlas PaperWorks</h1>
            </div>
        </div>
        <div class="row">            
            <div class="col-lg-12">                
                <iframe width="100%" height="800px" src="<?php echo $this->params['file_url']; ?>"></iframe>                
            </div>
        </div>
    </div>
</div>

<?php echo $this->render('footer'); ?>