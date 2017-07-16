jQuery(document).ready(function(){
		
		var maxdate = new Date();

		$("#from_date").datetimepicker({ dateFormat: 'mm/dd/yy', timeFormat :'HH:mm:ss', changeMonth: true, changeYear: true, maxDate: maxdate});
		$("#to_date").datetimepicker({ dateFormat: 'mm/dd/yy', timeFormat :'HH:mm:ss', changeMonth: true, changeYear: true, maxDate: maxdate});
		
		
		jQuery.validator.addMethod("comparedate", function(value, element) {
			
			var start_time = jQuery("#from_date").val() ;
			
			if(start_time!='' && value!='')
			{
				var end_time = value;
				
				var starttm = Date.parse(start_time);
				var endtm = Date.parse(end_time);
			
				if(end_time > start_time)
					return true;
				else
					return false;
			}
			else
				return true;
		}, "To date must be greater than from date.");
		
		jQuery.validator.addMethod("comparefromdate", function(value, element) {
			
			var start_time = value;
			var end_time = jQuery("#to_date").val();
			
			if(end_time!='' && value!='')
			{	
				var starttm = Date.parse(start_time);
				var endtm = Date.parse(end_time);
			
				if(end_time > start_time)
					return true;
				else
					return false;
			}
			else
				return true;
		}, "From date must be less than to date.");
		
		
		jQuery.validator.addMethod("comparesearchfor", function(value, element) {
			
			var search_by = jQuery("#search_by").val();
			
			if(value!='')
			{	
				if(search_by=='')
					return false;
				else
					return true;
			}
			else
				return true;
		}, "Please select search by field");
		
		
		// validate add person form on keyup and submit
		jQuery("#auditsearch_frm").validate({
			errorElement:'div',
			rules: {
				search_by : {  },
				search_for : { comparesearchfor:true },
				from_date : { comparefromdate: true },
				to_date : { comparedate: true }
			},
			messages:{
			
			},
			// set this class to error-labels to indicate valid fields
			success: function(label){
				// set &nbsp; as text for IE
				label.hide();
			}
		});
		
		jQuery("#msg").fadeOut(5000);
});


function resetSearch()
{
	$('#search_by').val('');
	$('#search_for').val('');
	$('#from_date').val('');
	$('#to_date').val('');
	$('#auditsearch_frm').submit();
}

function showPersonId(search_by)
{
	switch(search_by){
		
	case 'Person_ID':
		$('#person_id_div').show();
		$('#search_for_div').hide();
		$('#action_div').hide();
		$('#screen_div').hide();
		$('#search_for_person').val('');
		break;
	case 'Action':
		$('#action_div').show();
		$('#search_for_div').hide();
		$('#person_id_div').hide();
		$('#screen_div').hide();
		$('#search_for_action').val('');
		break;
	case 'Screen_Name':
		$('#screen_div').show();
		$('#search_for_div').hide();
		$('#person_id_div').hide();
		$('#action_div').hide();
		$('#search_for_Screen_Name').val('');
		break;
	default:
		$('#search_for_div').show();
		$('#person_id_div').hide();
		$('#action_div').hide();
		$('#screen_div').hide();
		$('#search_for').val('');
		break;
	}
}

function checkCriteria()
{
	var search_by = $('#search_by').val();
	var start_time = jQuery("#from_date").val() ;
	var end_time = jQuery("#to_date").val();
	if(search_by=='' && start_time=='' && end_time=='')
	{
		bootbox.dialog({
		  message: "<span class='text-danger'>Please select any of the search criteria.</span>",
		  title: "Error",
		  buttons: {
			success: {
			  label: "Ok",
			  className: "btn-primary",
			  callback: function() {
			  }
			},
		   
		  }
		});
		return false;
	}
	else{
		return true;
	}
}
