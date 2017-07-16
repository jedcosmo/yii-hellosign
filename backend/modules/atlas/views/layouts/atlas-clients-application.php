<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    
    $application = $this->params['application'];
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Application</h1>
            </div>
        </div>
        
        <div class="row">            
            <?php $form = ActiveForm::begin(['options' => ['id' => 'applicationFormSteps', 'name' => 'applicationFormSteps', 'autocomplete' => 'off']]); ?>
                <div class="form-group" style="text-align: right;">
                    <?php if($application){ //show review button of all required fields are filled up. ?>
                        <a class="btn-review-hellosign button2" href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review">Review & Sign</a>
                    <?php } ?>
                    <button type="submit" value="Submit" class="button2">Submit</button>
                </div>
            
                <?php if($this->params['errors'] && is_array($this->params['errors']) && count($this->params['errors']) > 0) { ?>
                    <div class="alert alert-danger">
                        <?php echo $form->errorSummary($this->params['model']); ?>
                    </div>
                <?php } ?>
            
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
            
                <h3>Defendant Information</h3>
                <section class="col-lg-12">
                    <!-- Defendant Information -->                                   
                    <div class="form-group">
                        <label for="defendant_name">Name </label>
                        <input class="required" value="<?php echo $application['defendant_name']; ?>" type="text" name="defendant_name" size="30">                        
                    </div>
                    <div class="form-group">
                        <label for="defendant_us_citizen">Defendant is a US Citizen </label>
                        <input class="required" type="radio" name="defendant_us_citizen" value="yes" <?php echo ($application['defendant_us_citizen'] == 'yes') ? 'checked' : ''; ?>> Yes
                        <input class="required" type="radio" name="defendant_us_citizen" value="no" <?php echo ($application['defendant_us_citizen'] == 'no') ? 'checked' : ''; ?>> No
                    </div>
                    <div class="form-group">
                        <label for="defendant_dl">DL </label> 
                        <input class="required" value="<?php echo $application['defendant_dl']; ?>" type="number" name="defendant_dl" size="30">
                    </div>
                    <div class="form-group">
                        <label for="defendant_dob">Date of birth </label>
                        <input class="required" value="<?php echo $application['defendant_dob']; ?>" type="date" name="defendant_dob" size="16">
                    </div>
                    <div class="form-group">
                        <label for="defendant_ssn">SSN </label>
                        <input class="required" value="<?php echo $application['defendant_ssn']; ?>" type="text" name="defendant_ssn" size="30">
                    </div>
                    <div class="form-group">
                        <label for="defendant_phone">Phone </label>
                        <input class="required" value="<?php echo $application['defendant_phone']; ?>" type="number" name="defendant_phone" size="10">
                    </div>                                                         
                </section>
                
                <h3>Applicant Information</h3>
                <section class="col-lg-12">
                    <div class="form-group">
                        <label for="applicant_name">Name </label>
                        <input class="required" value="<?php echo $application['applicant_name']; ?>" type="text" name="applicant_name" size="30">
                    </div>
                    <div class="form-group">
                        <label for="applicant_dob">Date of birth: </label>
                        <input class="required" value="<?php echo $application['applicant_dob']; ?>" type="date" name="applicant_dob" size="16">
                    </div>
                       <div class="form-group">
                        <label for="applicant_ssn">SSN </label>
                        <input class="required" value="<?php echo $application['applicant_ssn']; ?>" type="text" name="applicant_ssn" size="30">
                    </div>
                    <div class="form-group">
                        <label for="applicant_phone">Phone: </label>
                        <input class="required" value="<?php echo $application['applicant_phone']; ?>" type="number" name="applicant_phone" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_address">Current Address </label>
                        <input class="required" value="<?php echo $application['applicant_current_address']; ?>" type="text" name="applicant_current_address" size="60">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_city">City </label>
                        <input class="required" value="<?php echo $application['applicant_current_city']; ?>" type="text" name="applicant_current_city" size="25">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_state">State </label>
                        <input class="required" value="<?php echo $application['applicant_current_state']; ?>" type="text" name="applicant_current_state" size="25">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_zip_code">Zip </label>
                        <input class="required" value="<?php echo $application['applicant_current_zip_code']; ?>" type="number" name="applicant_current_zip_code" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_homeownership">Home Ownership </label>
                        <input class="required" type="radio" name="applicant_current_homeownership" value="own" <?php echo ($application['applicant_current_homeownership'] == 'own') ? 'checked' : ''; ?>> Own
                        <input class="required" type="radio" name="applicant_current_homeownership" value="rented" <?php echo ($application['applicant_current_homeownership'] == 'rented') ? 'checked' : ''; ?>> Rented
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_monthly_payment">Monthly Payment or Rent </label>
                        <input class="required" value="<?php echo $application['applicant_current_monthly_payment']; ?>" type="number" name="applicant_current_monthly_payment" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_current_how_long">How Long </label>
                        <input class="required" value="<?php echo $application['applicant_current_how_long']; ?>" type="text" name="applicant_current_how_long" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_address">Previous Address </label>
                        <input class="required" value="<?php echo $application['applicant_previous_address']; ?>" type="text" name="applicant_previous_address" size="60">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_city">City </label>
                        <input class="required" value="<?php echo $application['applicant_previous_city']; ?>" type="text" name="applicant_previous_city" size="25">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_state">State </label>
                        <input class="required" value="<?php echo $application['applicant_previous_state']; ?>" type="text" name="applicant_previous_state" size="25">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_zip_code">Zip </label>
                        <input class="required" value="<?php echo $application['applicant_previous_zip_code']; ?>" type="number" name="applicant_previous_zip_code" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_homeownership">Home Ownership </label>
                        <input class="required" type="radio" name="applicant_previous_homeownership" value="own" <?php echo ($application['applicant_previous_homeownership'] == 'own') ? 'checked' : ''; ?>> Own
                        <input class="required" type="radio" name="applicant_previous_homeownership" value="rented" <?php echo ($application['applicant_previous_homeownership'] == 'rented') ? 'checked' : ''; ?>> Rented
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_monthly_payment">Monthly Payment or Rent </label>
                        <input class="required" value="<?php echo $application['applicant_previous_monthly_payment']; ?>" type="number" name="applicant_previous_monthly_payment" size="10">
                    </div>
                    <div class="form-group">
                        <label for="applicant_previous_how_long">How Long </label>
                        <input class="required" value="<?php echo $application['applicant_previous_how_long']; ?>" type="text" name="applicant_previous_how_long" size="10">
                    </div>
                </section>
                
                <h3>Employment Information</h3>
                <section class="col-lg-12">
                    <div class="form-group">
                        <label for="employment_current_employer">Current employer </label>
                        <input class="required" value="<?php echo $application['employment_current_employer']; ?>" type="text" name="employment_current_employer" size="30">
                    </div>
                    <div class="form-group">
                        <label for="employment_employer_address">Employer address </label>
                        <input class="required" value="<?php echo $application['employment_employer_address']; ?>" type="text" name="employment_employer_address" size="50">
                    </div>
                    <div class="form-group">
                        <label for="employment_how_long">How long? </label>
                        <input class="required" value="<?php echo $application['employment_how_long']; ?>" type="text" name="employment_how_long" size="10">
                    </div>
                    <div class="form-group">
                        <label for="employment_phone">Phone </label>
                        <input type="number" value="<?php echo $application['employment_phone']; ?>" name="employment_phone" size="10">
                    </div>
                    <div class="form-group">
                        <label for="employment_email_address" class="email">Email Address </label>
                        <input class="required email" value="<?php echo $application['employment_email_address']; ?>" type="text" name="employment_email_address" size="30" type="email">
                    </div>
                    <div class="form-group">
                        <label for="employment_fax">Fax </label>
                        <input type="number" value="<?php echo $application['employment_fax']; ?>" name="employment_fax" size="10">
                    </div>
                    <div class="form-group">
                        <label for="employment_city">City </label>
                        <input class="required" value="<?php echo $application['employment_city']; ?>" type="text" name="employment_city" size="25">
                    </div>
                    <div class="form-group">
                        <label for="employment_state">State </label>
                        <input class="required" value="<?php echo $application['employment_state']; ?>" type="text" name="employment_state" size="25">
                    </div>
                    <div class="form-group">
                        <label for="employment_zip_code">Zip Code </label>
                        <input class="required" value="<?php echo $application['employment_zip_code']; ?>" type="number" name="employment_zip_code" size="10">
                    </div>
                    <div class="form-group">
                        <label for="employment_position">Position </label>
                        <input class="required" value="<?php echo $application['employment_position']; ?>" type="text" name="employment_position" size="25">
                    </div>
                    <div class="form-group">
                        <label for="employment_salary_type">Salary Type </label>
                        <input class="required" type="radio" name="employment_salary_type" value="hourly" <?php echo ($application['employment_salary_type'] == 'hourly') ? 'checked' : ''; ?>> Hourly
                        <input class="required" type="radio" name="employment_salary_type" value="salary" <?php echo ($application['employment_salary_type'] == 'salary') ? 'checked' : ''; ?>> Salary
                    </div>
                     <div class="form-group">
                        <label for="employment_annual_income">Annual Income </label>
                        <input class="required" value="<?php echo $application['employment_annual_income']; ?>" type="number" name="employment_annual_income" size="10">
                    </div>
                </section>
                
                <h3>Personal Reference</h3>
                <section class="col-lg-12">
                    <div class="form-group">
                        <label for="personal_reference_name">Name of a person not residing with you </label>
                        <input class="required" value="<?php echo $application['personal_reference_name']; ?>" type="text" name="personal_reference_name" size="30">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_address">Address </label>
                        <input class="required" value="<?php echo $application['personal_reference_address']; ?>" type="text" name="personal_reference_address" size="50">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_city">City </label>
                        <input class="required" value="<?php echo $application['personal_reference_city']; ?>" type="text" name="personal_reference_city" size="25">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_state">State </label>
                        <input class="required" value="<?php echo $application['personal_reference_state']; ?>" type="text" name="personal_reference_state" size="25">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_zip_code">Zip Code </label>
                        <input class="required" value="<?php echo $application['personal_reference_zip_code']; ?>" type="number" name="personal_reference_zip_code" size="10">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_phone">Phone </label>
                        <input class="required" value="<?php echo $application['personal_reference_phone']; ?>" type="number" name="personal_reference_phone" size="10">
                    </div>
                    <div class="form-group">
                        <label for="personal_reference_relationship">Relationship </label>
                        <input class="required" value="<?php echo $application['personal_reference_relationship']; ?>" type="text" name="personal_reference_relationship" size="30">
                    </div>
                </section>
                
                <h3>References</h3>
                <section class="col-lg-12">
                    <div class="form-group">
                        <label for="references_name">Name </label>
                        <input class="required" value="<?php echo $application['references_name']; ?>" type="text" name="references_name" size="30">
                    </div>
                    <div class="form-group">
                        <label for="references_address">Address </label>
                        <input class="required" value="<?php echo $application['references_address']; ?>" type="text" name="references_address" size="50">
                    </div>
                    <div class="form-group">
                        <label for="references_phone">Phone </label>
                        <input class="required" value="<?php echo $application['references_phone']; ?>" type="number" name="references_phone" size="10">
                    </div>
                </section>
                
                <h3>Signature</h3>
                <section class="col-lg-12">
                    <div class="form-group">
                        I authorize the verification of the information provided on this form as to my employment. I understand falsification of the information
                        contained herein constitutes insurance fraud and is a violation of law. I execute this application under penalty of perjury.
                    </div>
                    <div class="form-group">
                        <label for="singature_date">Signature Date </label>
                        <input class="required" value="<?php echo $application['signature_date']; ?>" type="date" name="signature_date" size="10">
                    </div>
                </section>
                <div class="form-group" style="text-align: right;">
                    <button type="submit" value="Submit" class="button2">Submit</button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<?php echo $this->render('footer'); ?>

