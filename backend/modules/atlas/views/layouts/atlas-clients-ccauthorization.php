<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    
    $ccauthorization = $this->params['ccauthorization'];
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">AAA Atlas Bail Bonds</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?php 
                    $notification_msg = Yii::$app->session->get('notification_msg');                    
                    if($notification_msg) { 
                ?>
                   <div class="alert alert-info">
                       <?php echo $notification_msg; ?>
                   </div>                                            
                <?php 
                       Yii::$app->session->remove('notification_msg');                
                    } 
                ?>
                
                <?php if($ccauthorization){ //show review button of all required fields are filled up. ?>
                    <div class="col-lg-12">
                        <a class="btn-review-hellosign button2" href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review">Review & Sign</a>
                    </div>
                <?php } ?>
                
                <?php $form = ActiveForm::begin(['options' => ['id' => 'ccForm', 'name' => 'ccForm', 'autocomplete' => 'off']]); ?> 
                
                    <?php if($this->params['errors'] && is_array($this->params['errors']) && count($this->params['errors']) > 0) { ?>
                        <div class="alert alert-danger">
                            <?php echo $form->errorSummary($this->params['model']); ?>
                        </div>
                    <?php } ?>
                                                    
                    <p>I authorize AAA Atlas Bail Bonds, its employees, agents or representatives to charge the bail bond
                    premium/renewal in the sum of:<br><br>
                    <input value="<?php echo $ccauthorization['ccauthorization_premiunm_amount_text']; ?>" type="text" name="ccauthorization_premiunm_amount_text" size="50"> U.S. Dollars 
                    <br><br>
                    $<input value="<?php echo $ccauthorization['ccauthorization_premiunm_amount']; ?>" type="number" name="ccauthorization_premiunm_amount" size="10"><br><br>
                    
                    
                    The authorization information below shall be held on file in strict confidence. The credit card may be checked for validity before issuance of the bail bond(s). The card number below may be used to pay the premium when it becomes due. As long as the bail bond obligation undertaken by AAA Atlas Bail Bonds is in force, this authorization will remain in full force and effect until such time as the bond obligation referred to herein is fully exonerated or discharged.<br><br>

                    The undersigned agrees to authorize AAA Atlas Bail Bonds to submit credit card charges using the credit card listed below to recover all payments due and all other unpaid amounts for the payment of premiums, premium renewals or forfeitures.<br><br>

                    Security Code: <input value="<?php echo $ccauthorization['ccauthorization_security_code']; ?>" type="number" name="ccauthorization_security_code" size="10"><br>
                    Card Type: 
                    <input type="radio" name="ccauthorization_card_type" value="mastercard" <?php echo ($ccauthorization['ccauthorization_card_type'] == 'mastercard') ? 'checked' : ''; ?>> MasterCard ® &nbsp;
                    <input type="radio" name="ccauthorization_card_type" value="visa" <?php echo ($ccauthorization['ccauthorization_card_type'] == 'visa') ? 'checked' : ''; ?>> VISA ® &nbsp;
                    <input type="radio" name="ccauthorization_card_type" value="amex" <?php echo ($ccauthorization['ccauthorization_card_type'] == 'amex') ? 'checked' : ''; ?>> Amex ® &nbsp;
                    <input type="radio" name="ccauthorization_card_type" value="discover" <?php echo ($ccauthorization['ccauthorization_card_type'] == 'discover') ? 'checked' : ''; ?>> Discover ® &nbsp;
                       <br>
                    Name on Card <input value="<?php echo $ccauthorization['ccauthorization_card_name']; ?>" type="text" name="ccauthorization_card_name" size="25"><br>
                    Card Number <input value="<?php echo $ccauthorization['ccauthorization_card_number']; ?>" type="number" name="ccauthorization_card_number" size="25"> <br>
                    Expiration <input value="<?php echo $ccauthorization['ccauthorization_card_expiration']; ?>" type="date" name="ccauthorization_card_expiration" size="10"><br>
                    Billing Address <input value="<?php echo $ccauthorization['ccauthorization_billing_address']; ?>" type="text" name="ccauthorization_billing_address" size="60"><br>
                    Billing City <input value="<?php echo $ccauthorization['ccauthorization_billing_city']; ?>" type="text" name="ccauthorization_billing_city" size="25"> <br>
                    State <input value="<?php echo $ccauthorization['ccauthorization_state']; ?>" type="text" name="ccauthorization_state" size="25"><br>
                    Zip Code <input value="<?php echo $ccauthorization['ccauthorization_zip_code']; ?>" type="number" name="ccauthorization_zip_code" size="10"><br><br>

                    I hereby declare that I am the holder of the above credit card and I authorize its use to pay premium(s), renewals or forfeitures for Bail bonds provided by AAA Atlas Bail Bonds. I also understand that this credit card may be charged for any future invoice for any and all costs associated with this/these bail bond(s).<br><br>

                    Cardholder’s Signature: <input type="text" name="ccauthorization_signature_name" size="25"> <br>
                    Date: <input value="<?php echo $ccauthorization['ccauthorization_date_signed']; ?>" type="date" name="ccauthorization_date_signed" size="25"><br><br>

                    If this authorization is to be returned by FAX, please fax back to (214) 256-9006</p>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" value="Submit" class="button2">Submit</button>
                    </div>
                <?php ActiveForm::end(); ?>                   
            </div>
        </div>
    </div>
</div>
<?php echo $this->render('footer'); ?>

