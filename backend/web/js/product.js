jQuery(document).ready(function(){
								
		//	set autofous to form first field
		$('#gmp-product-form:first *:input[type!=hidden]:first').focus();
		$('#gmp-productupdate-form:first *:input[type!=hidden]:first').focus();
		
		var edit_id = $('#gmp_product_id_pk').val();
		jQuery.validator.addMethod("gmpalphabets", function (param, element) {
				if(param.length > 0)
				  	return param.match(/^[a-zA-Z\s]+$/);
				else
					return true;
  		}, "Only alphabets & space is allowed");
		 
		jQuery.validator.addMethod("gmpproductname", function (param, element) {
				if(param.length > 0)
				  return param.match(/^(?!\s)(?!.*\s$)(?=.*[a-zA-Z0-9])[a-zA-Z0-9 '()-@.&,\s~?!]{2,}$/i);
				else
					return true;
  		}, "You can use alphabets,digits,space and special characters .,/()'&");
		
		
		jQuery.validator.addMethod("gmpproductcode", function (param, element) {
				if(param.length > 0)
				  return param.match(/^[a-zA-Z0-9 -.]+$/);
				else
					return true;
  		}, "You can use alphabets,digits & special characters .-");
		 		 
		// validate add person form on keyup and submit
		jQuery("#gmp-product-form").validate({
			errorElement:'div',
			rules: {
				gmp_product_name : { 
									required: true,
									maxlength: 150,
									//gmpproductname: true,
								 },
				gmp_company : { required: true },
				
				gmp_product_code:{
							required: true,
							maxlength: 20,
							gmpproductcode:true,
							remote: { 
								url : SITEROOT + "product/product/codeunique", 
								data : {"action" : "check_duplicate_code",'edit_id': edit_id},
								type : "post"
								//async:false
							}
					},
				gmp_product_unit : { required: true },
			
				/*gmp_product_qty : { 
									required: true,
									maxlength: 10,
									number:true,
								},*/
				/*gmp_product_document : { 
									required: true,
								},*/
			},
			messages:{
				gmp_product_name : { required: "Please enter product name." },
				gmp_company : { required: "Please select company." },
				gmp_product_code:{ required: "Please enter product code.",
								   remote: "Code is already in use for other product.",
								},
				gmp_product_unit : { required: "Please select unit." },
				/*gmp_product_qty : { required: "Please enter quantity." },*/
				/*gmp_product_document : {required: "Please upload document."},*/
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		
		// validate add person form on keyup and submit
		jQuery("#gmp-productupdate-form").validate({
			errorElement:'div',
			rules: {
				gmp_product_name : { 
									required: true,
									maxlength: 150,
									//gmpproductname: true,
								 },
				gmp_company : { required: true },
				
				gmp_product_code:{
							required: true,
							maxlength: 20,
							gmpproductcode:true,
							//remote: SITEROOT+'product/product/codeunique?action=check_duplicate_code&edit_id=' + edit_id,
					},
				gmp_product_unit : { required: true },
			
				/*gmp_product_qty : { 
									required: true,
									maxlength: 10,
									number:true,
								},*/
			},
			messages:{
				gmp_product_name : { required: "Please enter product name." },
				gmp_company : { required: "Please select company." },
				gmp_product_code:{ required: "Please enter product code.",
								   remote: "Code is already in use for other product.",
								},
				gmp_product_unit : { required: "Please select unit." },
				/*gmp_product_qty : { required: "Please enter quantity." },*/
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		
		jQuery("#msg").fadeOut(5000);
	});
