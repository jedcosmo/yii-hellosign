// Wait for the DOM to be ready
$(function() {
  // Initialize form validation on the registration form.
  // It has the name attribute "registration"
  $("form[name='adminForm']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      lastname: "required",
      firstname: "required",
      middlename: "required",
      phone: "required",
      address: "required",
      state: "required",
      city: "required",
      zip: "required",
      email: {
        required: true,
        // Specify that email should be validated
        // by the built-in "email" rule
        email: true
      },
      username: "required",
      password: {
        required: true,
        minlength: 6
      }
    },
    // Specify validation error messages
    messages: {
      lastname: "",
      firstname: "", 
      middlename: "",
      phone: "",
      address: "",
      state: "",
      city: "",
      zip: "",
      email: "",
      username: {
        required: "Please provide a username"
      },
      password: {
        required: "Please provide a password",
        minlength: "Your password must be at least 5 characters long"
      },
      email: "Please provide a valid E-mail Address"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function(form) {
      form.submit();
    }
  });
  
  $("form[name='contractForm']").validate({
        // Specify validation rules
        rules: {
          // The key name on the left side is the name attribute
          // of an input field. Validation rules are defined
          // on the right side
          contract_jail_from: "required",
          contract_person_name: "required",
          contract_bond_sum: "required",
          contract_date_day: {
                                required: true,
                                range: [1, 31]
                              },
          contract_date_month: {
                                required: true,
                                range: [1, 12]
                              },
          contract_date_year: "required",
          contract_current_address: "required"
        },
        // Specify validation error messages
        messages: {
          contract_jail_from: "",
          contract_person_name: "",
          contract_bond_sum: "",
          contract_date_day: "", 
          contract_date_month: "",
          contract_date_year: "",
          contract_current_address: ""          
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
          form.submit();
        }
    });   
    
    $("form[name='promissoryForm']").validate({
        // Specify validation rules
        rules: {
          // The key name on the left side is the name attribute
          // of an input field. Validation rules are defined
          // on the right side
          promissory_date: "required",
          promissory_note_amount: "required",
          promissory_city: "required",
          promissory_state: "required",
          promissory_principal_sum_text: "required",
          promissory_principal_sum_numbers: "required",
          promissory_defendant_name: "required",
          promissory_defendant_address: "required",
          promissory_payment_amount: "required",
          promissory_weekly_payment_start_date:"required",
          promissory_debtor_name: "required",
          contract_current_address: "required",
          promissory_debtor_date: "required",
          promissory_witness_name: "required",
          promissory_witness_date: "required"
        },
        // Specify validation error messages
        messages: {
          promissory_date: "",
          promissory_note_amount: "",
          promissory_city: "",
          promissory_state: "",
          promissory_principal_sum_text: "",
          promissory_principal_sum_numbers: "",
          promissory_defendant_name: "",
          promissory_defendant_address: "",
          promissory_payment_amount: "",
          promissory_weekly_payment_start_date:"",
          promissory_debtor_name: "",
          contract_current_address: "",
          promissory_debtor_date: "",
          promissory_witness_name: "",
          promissory_witness_date: ""         
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
          form.submit();
        }
    });
    
    $("form[name='ccForm']").validate({
        // Specify validation rules
        rules: {
          // The key name on the left side is the name attribute
          // of an input field. Validation rules are defined
          // on the right side
          ccauthorization_premiunm_amount_text: "required",
          ccauthorization_premiunm_amount: "required",
          ccauthorization_security_code: "required",
          ccauthorization_card_type: "required",
          ccauthorization_card_name: "required",          
          ccauthorization_card_number: {
                                        required: true,
                                        creditcard: true
                                      },
          ccauthorization_card_expiration: "required",
          ccauthorization_billing_address: "required",
          ccauthorization_billing_city: "required",
          ccauthorization_state: "required",
          ccauthorization_zip_code: "required",
          ccauthorization_date_signed: "required"
        },
        // Specify validation error messages
        messages: {
          ccauthorization_premiunm_amount_text: "",
          ccauthorization_premiunm_amount: "",
          ccauthorization_security_code: "",
          ccauthorization_card_type: "Please select credit card type.",
          ccauthorization_card_name: "",          
          ccauthorization_card_number: "Please use a valid credit card number",
          ccauthorization_card_expiration: "",
          ccauthorization_billing_address: "",
          ccauthorization_billing_city: "",
          ccauthorization_state: "",
          ccauthorization_zip_code: "",
          ccauthorization_date_signed: ""          
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
          form.submit();
        }
    });
});

function synccontract_jail_from()
{
  var n1 = document.getElementById('contract_jail_from');
  var n2 = document.getElementById('contract_jail_from_sync');
  n2.value = n1.value;
  n1.value = n2.value;
}

function synccontract_person_name()
{
 var n1 = document.getElementById('contract_person_name');
 var n2 = document.getElementById('contract_person_name_sync');
 n2.value = n1.value;
}

function synccontract_bond_sum()
{
 var n1 = document.getElementById('contract_bond_sum');
 var n2 = document.getElementById('contract_bond_sum_sync');
 n2.value = n1.value;
}

function syncpromissory_principal_sum_text()
{
  var n1 = document.getElementById('promissory_principal_sum_text');
  var n2 = document.getElementById('promissory_principal_sum_text_sync');
  n2.value = n1.value;
  n1.value = n2.value;
}

function syncpromissory_principal_sum_numbers()
{
 var n1 = document.getElementById('promissory_principal_sum_numbers');
 var n2 = document.getElementById('promissory_principal_sum_numbers_sync');
 n2.value = n1.value;
}

function syncpromissory_defendant_address()
{
  var n1 = document.getElementById('promissory_defendant_address');
  var n2 = document.getElementById('promissory_defendant_address_sync');
  n2.value = n1.value;
  n1.value = n2.value;
}

function syncpromissory_defendant_name()
{
 var n1 = document.getElementById('promissory_defendant_name');
 var n2 = document.getElementById('promissory_defendant_name_sync');
 n2.value = n1.value;
}