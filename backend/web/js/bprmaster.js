jQuery(document).ready(function(){
	jQuery("#msgS").fadeOut(5000);
	jQuery("#msgE").fadeOut(5000);
	
	$('#myModal').on('shown.bs.modal', function () {
		$('#username').focus();
	});
	
});


function searchMPRVersion(fromAuditLog)
{
	var product_code = $('#product_code').val();
	product_code = base64_encode(product_code);
	var searchText = "";
	if(fromAuditLog == 'Yes'){
		searchText = "&al=1";
	}
	if(product_code)
	{
		$('#product_code_err_div').hide();
		$('#myModalBig .modal-content').load(
			SITEROOT + 'bprrecords/bprrecords/mprversion_popup?product_code='+product_code+searchText,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModalBig').modal(options);
			}
		);
	}
	else
	{
		$('#product_code_err_div').show();
	}
}

function showBPRDetails(mpr_def_id,MPR_version)
{
	$('#mpr_version_ro').val(MPR_version);
	$('#mpr_def_id_fk').val(mpr_def_id);
	$.get( SITEROOT+'bprrecords/bprrecords/mprversiondetails?mpr_def_id='+mpr_def_id, function( data ) { 
		$("#mpr_details_div").html( data );	
		$("#submitBtnDiv").show();
		$('#myModalBig').modal('hide');
	});
}

function searchOnPart(product_code)
{
	var sort_mpr = $('#sort_mpr').val();
	var product_code =  $('#prdcode_hid').val();
	var product_part = $('#part_search_txt').val();
	if(product_part!='')
	{
		$.get(SITEROOT+'bprrecords/bprrecords/ajaxmprlisting',{product_part:product_part,product_code:product_code,sort_mpr:sort_mpr},function(data){
			$("#myPartTable").html( data );	
		});
	}
}

function searchClear(product_code)
{
	var sort_mpr = $('#sort_mpr').val();
	var product_code =  $('#prdcode_hid').val();
	$('#part_search_txt').val('');
	$.get(SITEROOT+'bprrecords/bprrecords/ajaxmprlisting',{product_part:'',product_code:product_code,sort_mpr:sort_mpr},function(data){
		$("#myPartTable").html( data );	
	});
}

function sortResultMPR()
{
	var sort_mpr = $('#sort_mpr').val();
	var product_code =  $('#prdcode_hid').val();
	var product_part = $('#part_search_txt').val();

	$.get(SITEROOT+'bprrecords/bprrecords/ajaxmprlisting',{product_part:product_part,product_code:product_code,sort_mpr:sort_mpr},function(data){
		$("#myPartTable").html( data );	
	});
}

function showPerformerPopup(actmember, mi_id_pk, bpr_id_fk)
{	
	/*var rflag = $(obj).is(':checked');
	if(rflag)
	{*/
		$('#myModalBig .modal-content').load(
			SITEROOT + 'bprrecords/bprrecords/bpr_instruction?mi_id_pk='+mi_id_pk+'&bpr_id_fk='+bpr_id_fk+'&actmember='+actmember,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModalBig').modal(options);
			}
		);
	//}
}

function showInstructionViewPopup(mi_id_pk, bpr_id_fk)
{	
	$('#myModalBig .modal-content').load(
		SITEROOT + 'bprrecords/bprrecords/manufacturing_inst_view?id='+mi_id_pk+'&bprid='+bpr_id_fk+'&mode=bpr',
		function(e){
			var options = {
				"backdrop" : "static",
				"show" : true,
				"keyboard" : true
			}
			$('#myModalBig').modal(options);
		}
	);	
}

function sign_bpr_mi(bpr_id,mpr_def_id,mi_id,signType)
{	
	$('#myModal .modal-content').load(
		SITEROOT + 'bprrecords/bprrecords/sign_approve?bpr_id='+bpr_id+'&mpr_def_id='+mpr_def_id+'&mi_id='+mi_id+'&signType='+signType,
		function(e){
			var options = {
				"backdrop" : "static",
				"show" : true,
				"keyboard" : true
			}
			$('#myModal').modal(options);
		});
}

function approveBPR(bpr_appr_id,masterBPR)
{
	var chkMarked = $('#performerChk_'+bpr_appr_id).is(':checked');
	if(chkMarked)
	{
		showGlobalLoadingMessage('start');
		var datenow = new Date();
		var nowtime = datenow.getTime();
		$('#myModal .modal-content').load(
			SITEROOT + 'bprapprovals/bprapprovals/apporveperformer?bpr_appr_id='+bpr_appr_id+'&masterBPR='+masterBPR+'&time=' + nowtime,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModal').modal(options);
			});
	}
}

function verifyBPR(bpr_appr_id,masterBPR)
{
	var chkMarked = $('#verifierChk_'+bpr_appr_id).is(':checked');
	if(chkMarked)
	{
		showGlobalLoadingMessage('start');
		var datenow = new Date();
		var nowtime = datenow.getTime(); 	
		$('#myModal .modal-content').load(
			SITEROOT + 'bprapprovals/bprapprovals/verifyperformer?bpr_appr_id='+bpr_appr_id+'&masterBPR='+masterBPR+'&time=' + nowtime,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModal').modal(options);
			});
	}
}

function changeBPRstaus(status, bprid)
{
	if(status)
	{
		var datenow = new Date();
		var nowtime = datenow.getTime();
		$('#myModal .modal-content').load(
		SITEROOT + 'bprrecords/bprrecords/change_bpr_status?bprid='+bprid+'&status='+status+'&time=' + nowtime,
		function(e){
			var options = {
				"backdrop" : "static",
				"show" : true,
				"keyboard" : true
			}
			$('#myModal').modal(options);
		});
	}
}

function confirmBprStep(actmember)
{
	var formdata = $('#bpr_instructions').serialize();
	var bpr_id_pk = $('#bpr_id_pk').val();
	var mpr_definition_id_fk = $('#mpr_definition_id_fk').val();
	var mi_id_pk = $('#mi_id_pk').val();
	var bpr_status_signature_id = $('#bpr_status_signature_id').val();
	
	$.post(SITEROOT + 'bprrecords/bprrecords/bpr_instruction?mi_id_pk='+mi_id_pk+'&bpr_id_fk='+bpr_id_pk+'&actmember='+actmember+'&bpr_status_signature_id='+bpr_status_signature_id, formdata, function(dataNew){
		if(dataNew!='gotoInstructions')		
			$('#bpr_inst_div').html(dataNew);
		else
			window.location.href = SITEROOT + 'bprrecords/bprrecords/view?id='+bpr_id_pk+'&tab=manufacturingInst';
	});
}

function closeBprInstructionPopup()
{
	var bpr_id_pk = $('#bpr_id_pk').val();
	$('#myModalBig').modal('hide');
	window.location.href = SITEROOT + 'bprrecords/bprrecords/view?id='+bpr_id_pk+'&tab=manufacturingInst';
}

function changeMPRonProductChange()
{
	$('#mpr_version_hid').val('');
	$('#mpr_version_ro').val('');
	$('#mpr_details_div').html('');
	$('#submitBtnDiv').hide();
}

function equipmentOperatorSignature(bpr_id,mpr_def_id,equipment_map_id_pk)
{	
	var signType = '';
	$('#myModal .modal-content').load(
		SITEROOT + 'bprrecords/bprrecords/equipment_operator_signature?bpr_id='+bpr_id+'&mpr_def_id='+mpr_def_id+'&equipment_map_id_pk='+equipment_map_id_pk+'&signType='+signType,
		function(e){
			var options = {
				"backdrop" : "static",
				"show" : true,
				"keyboard" : true
			}
			$('#myModal').modal(options);
		});
}
