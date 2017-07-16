<?php
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;
    use yii\helpers\ArrayHelper;
    
    $promissory = $this->params['promissory'];
?>
<?php echo $this->render('header'); ?>
<?php echo $this->render('sidebar', array('settings' => $this->params['sidebar_settings'])); ?>
<!-- Page Content -->
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header" style="font-size: 32px">Promissory Note & Installment Payment Plan for Unpaid Premium <br> Pagare y Plan de Pagos for El Balance a Pagar</h1>
            </div>
        </div>

        <div class="row">
            <?php if($promissory){ //show review button of all required fields are filled up. ?>
                <div class="col-lg-12">
                    <a class="btn-review-hellosign button2" href="<?php echo Yii::$app->homeUrl; ?>atlas/atlas/review">Review & Sign</a>
                </div>
            <?php } ?>
            
            <div class="col-lg-12">
                <?php $form = ActiveForm::begin(['options' => ['id' => 'promissoryForm', 'name' => 'promissoryForm', 'autocomplete' => 'off']]); ?>                
                    
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
                
                    <p>
                    Date / Fecha: <input type="date" value="<?php echo $promissory['promissory_date']; ?>" name="promissory_date" size="10" ><br><br>
                    Note Amount (amount owed) / Fecha Monto adeudado: $ <input type="number" value="<?php echo $promissory['promissory_note_amount']; ?>" name="promissory_note_amount" size="10" ><br><br>
                    City / Ciudad: <input type="text" value="<?php echo $promissory['promissory_city']; ?>" name="promissory_city" size="25" ><br><br>
                    State / Estado: <input type="text" value="<?php echo $promissory['promissory_state']; ?>" name="promissory_state" size="25" > <br><br>


                    1. FOR VALUE RECEIVED, I (we), the undersigned Debtor(s), jointly and severally (together and separately), promise to pay to the of AAA ATALS BAIL BONDS the principal sum of (write down amount) <input type="text" value="<?php echo $promissory['promissory_principal_sum_text']; ?>" name="promissory_principal_sum_text" id="promissory_principal_sum_text" size="25" onkeyup="syncpromissory_principal_sum_text()" > ($ <input type="number" value="<?php echo $promissory['promissory_principal_sum_numbers']; ?>" name="promissory_principal_sum_numbers" id="promissory_principal_sum_numbers" size="10" onkeyup="syncpromissory_principal_sum_numbers()">).<br>
                    Por el valor recibido, yo (nosotros,) el deudor(s) que subscribe, de forma solidaria (juntos y por separado), prometo pagar a la compañía de fianzas AAA ATLAS la suma principal de (anote la cantidad) <input type="text" value="<?php echo $promissory['promissory_principal_sum_text']; ?>" id="promissory_principal_sum_text_sync" size="25" > ($ <input type="number" value="<?php echo $promissory['promissory_principal_sum_numbers']; ?>" id="promissory_principal_sum_numbers_sync" size="10" >).<br><br>

                    Owed for the bail bond (bond) of (name of defendant) <input type="text" value="<?php echo $promissory['promissory_defendant_name']; ?>" name="promissory_defendant_name" id="promissory_defendant_name" size="25" onkeyup="syncpromissory_defendant_name()"> (Defendant) at the address shown (address of the office they will be making payments) <input type="text" value="<?php echo $promissory['promissory_defendant_address']; ?>" name="promissory_defendant_address" id="promissory_defendant_address" size="60" onkeyup="syncpromissory_defendant_address()"> or at such other place as AAA Atlas Bail Bonds may from time to time designate in writing according to the following payment plan:<br>
                    Adeudado por la fianza (fianza) de (nombre del acusado) <input type="text" value="<?php echo $promissory['promissory_defendant_name']; ?>" id="promissory_defendant_name_sync" size="25" > (acusado) en la dirección indicada (dirección de la oficina que va a hacer pagos) <input value="<?php echo $promissory['promissory_defendant_address']; ?>" type="text" id="promissory_defendant_address_sync" size="60" > o en cualquier otro lugar que Fianzas AAA Atlas posiblemente designe por escrito de acuerdo con el siguiente plan de pago:<br><br>

                    Amount of payment $ <input type="number" value="<?php echo $promissory['promissory_payment_amount']; ?>" name="promissory_payment_amount" size="10" ><br><br>

                    Weekly payments starting: <input type="date" value="<?php echo $promissory['promissory_weekly_payment_start_date']; ?>" name="promissory_weekly_payment_start_date" size="16" ><br><br>
                    Monto a pagar semanalmente Fecha inicial de pagos


                    2. The entire amount of the outstanding balance under this note shall become due and payable immediately under any one or more of the following events: (1) upon defendants failure to appear in the court for which the Bond was posted at any time required such court, (2) upon forfeiture of the bond; or (3) if any payment is not received by AAA Atlas Bail Bonds within five (5) days following its due date or is returned for insufficient funds, stopped or refused for any reason upon presentment to
                    a financial institution.<br><br>
                    La totalidad del importe del saldo pendiente de pago bajo esta nota serán exigibles y pagaderos inmediatamente debajo de uno o más de los siguientes eventos: (1) en caso que el acusado falte
                    a su fecha de corte para la cual fue requerida su presencia y por la cual se puso la fianza, , (2) en pérdida de la fianza; o (3) si cualquier pago no es recibido por Fianzas AAA Atlas dentro de
                    los cinco (5) días siguientes a su fecha de vencimiento o es devuelto por falta de fondos, detenido o rechazado por cualquier motivo a la presentación de una institución financiera.

                    3. I (we), jointly and severally (together and separately), hereby waive presentment, protest and demand, notice of protest, dishonor and nonpayment of this note, and expressly agree that, without in any way affecting my (our) liability under this note, AAA Atlas Bail Bonds may (1) extend the due date or the time of payment of any payment due under this note, (2) accept security or partial payments, (3) release any party liable under this note or any guarantee of this note and, (4) release any security now or later securing this note. The failure of AAA Atlas Bail Bonds to enforce any provision of this note, or to declare a default under this note, shall not be construed as a waiver of AAA Atlas Bail Bonds entitlement to payment, shall not be construed as a waiver or modification of the terms of this note, and shall not impair the right of AAA Atlas Bail Bonds to declare a default or to strictly enforce the terms of this note.<br><br>
                    Yo (nosotros), de forma solidaria (juntos y por separado), cede el derecho a protesta y demanda, notificación de protesta, rechazo y falta de pago de esta nota, y expresamente acuerda que, sin     incidir en modo alguno mi (nuestra) responsabilidad en virtud de esta nota, Fianzas AAA Atlas pueden (1) extender la fecha de vencimiento o el momento del pago de cualquier pago en virtud del presente nota, (2) aceptar pagos de seguridad o parciales, (3) eximir a toda parte responsable bajo esta nota o cualquier garantía de esta nota y, (4) liberar toda seguridad ahora o más adelante asegurar esta nota. El fracaso de Fianzas AAA Atlas para hacer cumplir cualquier disposición de esta nota, o declarar un incumplimiento bajo esta nota, no se interpretará como una renuncia de AAA Atlas Fianzas derecho al pago, no se interpretará como una renuncia o modificación de los términos de esta nota, y no deberán poner en peligro el derecho de Fianzas AAA Atlas para declarar un valor predeterminado o para hacer cumplir estrictamente los términos de esta nota.<br><br>

                    4. All obligations under this note remain in full force and are not terminated, modified or otherwise affected: (1) by revocation of the Bond; (2) by any change in the status of the Bond or the surety’s liability under the Bond; (3) by any change in the status of court proceedings for which the Bond was posted; or (4) by any change in whereabouts or status of the Defendant. This note shall remain in full force and effect.<br><br>
                    Todas las obligaciones derivadas de la presente nota se mantienen en pleno vigor y no se terminan, modificados o no afectadas: (1) por revocación de la fianza; (2) por cualquier cambio en el estado de la fianza o la responsabilidad del fiador bajo el Bond; (3) por cualquier cambio en el estado de los procesos judiciales para la que se pagó la fianza; o (4) por cualquier cambio en paradero o situación del acusado. Esta nota permanecerá en pleno vigor y efecto.<br><br>

                    5. If any portion of this note or any application of such provision shall be declared by a court of competent jurisdiction to be invalid or unenforceable, such invalidity or unenforceability shall not affect any other applications of such provision or the remaining provisions, which shall, to the fullest extent, remain in full force and effect. Any amendment or modification of this note must be in writing and signed by AAA Atlas Bail Bonds and me (us).<br><br>
                    Si cualquier parte de esta nota o cualquier aplicación de dicha disposición deberán ser declarados por un tribunal de jurisdicción competente como no válida o no aplicable, dicha invalidez o inaplicabilidad no afectará a ninguna otra aplicación de tal disposición o las disposiciones restantes, los cuales, en la mayor medida, permanecerán en pleno vigor y efecto. Cualquier enmienda o modificación de esta nota deben ser por escrito y firmado por Fianzas AAA Atlas y yo (nosotros).<br><br>

                    6. I (we) agree to all terms and conditions of this note and acknowledge receipt of a copy of this note. I (we) also agree to pay all collection costs including, without limitation, court costs, reasonable and actual attorney’s fees and expenses, and any other fees permitted by applicable law.<br><br>
                    Yo (nosotros) acepto todos los términos y condiciones de esta nota y acuso recibo de una copia de esta nota. Yo (nosotros) también de acuerdo en pagar todos los gastos de recaudación, incluyendo, sin limitación, los gastos judiciales, honorarios y gastos razonables y reales de abogados, y otros cargos permitidos por la ley aplicable. Yo (Nosotros) acepto Todos Los Términos y Condiciones de esta nota y acuso recibo de una copia de esta nota. Yo (Nosotros) también Acuerdo EN PAGAR TODOS LOS GASTOS de recaudación, incluyendo, sin limitación, los gastos Judiciales, honorarios razonables y gastos razonables y reales de abogados, y otros cargos permitidos por la ley que procede.<br><br>

                    Debtor Signature: <input type="text" size="25" ><br>
                    Firma del deudor<br><br>

                    Print Name: <input type="text" value="<?php echo $promissory['promissory_debtor_name']; ?>" name="promissory_debtor_name" size="30" ><br>
                    Nombre<br><br>

                    Date: <input type="date" value="<?php echo $promissory['promissory_debtor_date']; ?>" name="promissory_debtor_date" size="16" ><br>
                    Fecha<br><br>

                    AAA Atlas Bail Bonds Witness: <input type="text" name="name" size="25" ><br>
                    Testigo de AAA Atlas Bail Bonds<br><br>

                    Print Name: <input type="text" value="<?php echo $promissory['promissory_witness_name']; ?>" name="promissory_witness_name" size="30" ><br>
                    Nombre<br><br>

                    Date: <input type="date" value="<?php echo $promissory['promissory_witness_date']; ?>" name="promissory_witness_date" size="16" ><br>
                    Fecha<br><br>     
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

