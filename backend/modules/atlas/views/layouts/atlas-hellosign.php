<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;     
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])); ?>
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Merged PDF - Atlas PaperWorks</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <?php if(!$this->params['signed_confirm_id']){ ?>                    
                    <a class="btn-review-hellosign button2" href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review?do_sign=true">E-Signature</a>
                <?php } ?>
            </div>
            <div class="col-lg-12">                
                <iframe width="100%" height="800px" src="<?php echo $this->params['merge_pdf_path']; ?>"></iframe>                
            </div>
        </div>
    </div>
</div>

<?php echo $this->render('footer'); ?>

<?php if( $this->params['do_sign'] ) { ?>
    <script type="text/javascript">
        HelloSign.init('<?php echo $this->params['client_id']; ?>');
        HelloSign.open({
            url: "<?php echo $this->params['sign_url']; ?>",
            test_mode : 1,   
            skipDomainVerification: true,
            allowCancel: true,
            messageListener: function(eventData) {
                var mySignature_id = eventData.signature_id;
                var jsonTxt = JSON.stringify(eventData);            
                console.log(jsonTxt);
                if(eventData.event === 'signature_request_signed') {                
                    $.post(
                        '<?php echo Yii::$app->homeUrl; ?>atlas/atlas/sign',
                        { 
                            '<?php echo Yii::$app->request->csrfParam; ?>': '<?php echo Yii::$app->request->csrfToken ?>',
                            'signature_request_id':  '<?php echo $this->params['signature_request_id']; ?>',
                            'signature_id':  eventData.signature_id,
                            'signature_url':  '<?php echo $this->params['sign_url']; ?>',
                            'client_id': '<?php echo $this->params['current_client_id']; ?>',
                            'event_type': eventData.event,
                            'file_url': 'https://api.hellosign.com/v3/signature_request/files/<?php echo $this->params['signature_request_id']; ?>'
                        },
                        function(data){
                            if(data === 'success'){                            
                                window.location.href = '<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review';
                            }
                        }
                    );                            
                }
                
                if(eventData.event === 'signature_request_canceled'){
                    window.location.href = '<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review';
                }
            }    
        });
    </script>
<?php } ?>
