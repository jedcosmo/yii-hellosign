<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    
    $contract = $this->params['contract'];
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Contract to Indemnify<br>
                Contrato de Indemizacion</h1>
            </div>
        </div>

        <div class="row">
            <?php if($contract){ //show review button of all required fields are filled up. ?>
                <div class="col-lg-12">
                    <a class="btn-review-hellosign button2" href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review">Review & Sign</a>
                </div>
            <?php } ?>
            
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin(['options' => ['id' => 'contractForm', 'name' => 'contractForm', 'autocomplete' => 'off']]); ?>
                    
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
                    <p>For and in consideration of AAA ATLAS BAIL BONDS, securing the release (from jail) of <input value="<?php echo $contract['contract_jail_from']; ?>" type="text" name="contract_jail_from" id="contract_jail_from" size="30" onkeyup="synccontract_jail_from()" >, hereinafter referred to as Bonded Person, I, <input value="<?php echo $contract['contract_person_name']; ?>" type="text" name="contract_person_name" id="contract_person_name" size="25" onkeyup="synccontract_person_name()"> hereinafter referred to as Indemnitor, agree to pay AAA Atlas Bail Bonds the sum of $ <input value="<?php echo $contract['contract_bond_sum']; ?>" type="number" name="contract_bond_sum" id="contract_bond_sum" size="10" onkeyup="synccontract_bond_sum()">(total amount of bonds) plus court cost within five (5) Days of a bond forfeiture, or writ forfeiture of Bonded Person.<br><br>

                    Por y en consideración de Fianzas AAA ATLAS, asegurando la liberación (de la cárcel) de <input value="<?php echo $contract['contract_jail_from']; ?>" type="text" id="contract_jail_from_sync" size="30">, en lo sucesivo referido como la persona en condiciones bajo fianza, Yo, 
                    <input value="<?php echo $contract['contract_person_name']; ?>" type="text" id="contract_person_name_sync" size="25"> en lo sucesivo referido como el indemnizador, acuerda pagar Fianzas AAA ATLAS la suma de $ <input value="<?php echo $contract['contract_bond_sum']; ?>" type="number" id="contract_bond_sum_sync" size="10"> (cantidad total de fianzas) más los costos de la corte dentro de los cinco (5) días siguientes a la confiscación de la fianza, o al mandato de decomiso de la fianza de la persona bajo tal..<br><br>

                    In addition to the above, cosigner (indemnitor) agrees to pay AAA Atlas Bail Bonds all reasonable and
                    necessary expenses incurred, if any, therein in attempting to locate, find, attach, arrest, and submit Bonded Person as a result of the bond forfeiture or writ forfeiture.<br><br>

                    En adición a lo anterior, el cosignatario (indemnizador) se compromete a pagar Fianzas AAA Atlas todos los gastos razonables y necesarios incurridos, en su caso, en el mismo intento de localizar, encontrar, adjuntar, detener y presentar a la persona bajo fianza como resultado de la pérdida de la misma o mandato de decomiso de la fianza.<br><br>

                    Indemnitor further agrees to pay reasonable attorney fees and court costs of a law suit if brought to recover any indemnity or expenses incurred pursuant to this contract.<br><br>

                    El indemnizador acepta pagar los honorarios razonables de abogados y costos de corte si se presentara una demanda para recuperar cualquier indemnización o gastos derivados de la aplicación de este contrato.<br><br>

                    A bond-forfeiture occurs when it appears to the judge of the court where Bonded Person's case is docketed
                    that Bonded Person did not appear in court and the judge so designates, notes, writes, or expresses the same on the court's docket.<br><br>

                    El decomiso de la fianza se produce cuando la persona bajo fianza, no se presenta ante el juez de la corte, donde el caso está asignado, y el juez a su vez designa, anota, escribe o expresa lo mismo en la lista de la Corte asignada.<br><br>

                    Indemnitor further agrees to assume all liability for any balance due on account when bond is posted.<br><br>

                    El Indemnizador acuerda asumir toda responsabilidad por cualquier saldo en la cuenta de la fianza puesta.<br><br>

                    I have fully read this contract, acknowledge an understanding of bond forfeiture and writ forfeiture, and agree to its terms and conditions.<br><br>

                    He leído completamente este contrato, reconozco el término de confiscación y decomiso de la fianza, y estoy de acuerdo con sus términos y condiciones.<br><br>

                    DATED this <input value="<?php echo $contract['contract_date'] ? date('d', strtotime($contract['contract_date'])) : ''; ?>" type="number" name="contract_date_day" size="3"> day of <input value="<?php echo $contract['contract_date'] ? date('m', strtotime($contract['contract_date'])) : ''; ?>" type="number" name="contract_date_month" size="10"> , 20<input value="<?php echo $contract['contract_date'] ? date('y', strtotime($contract['contract_date'])) : ''; ?>" type="number" name="contract_date_year" size="1">.<br><br>

                    AAA Atlas Bail Bonds<input value="<?php echo $contract['contract_bail_bonds']; ?>" type="text" name="contract_bail_bonds" size="25"><br><br>

                    Indemnitor/Cosigner<input value="<?php echo $contract['contract_cosigner']; ?>" type="text" name="contract_cosigner" size="25"><br><br>

                    INDEMNITOR’S CURRENT ADDRESS: <input value="<?php echo $contract['contract_current_address']; ?>" type="text" name="contract_current_address" id="contract_current_address" size="60">
                    </p>
                    <div class="form-group" style="text-align: right;">
                        <button type="submit" value="Submit" class="button2">Submit</button>
                    </div>
                <?php ActiveForm::end(); ?>                   
            </div>
        </div>
    </div>
</div>
<?php echo $this->render('footer'); ?>

