jQuery(document).ready(function(){
	jQuery("#msgS").fadeOut(5000);
	jQuery("#msgE").fadeOut(5000);
	$('[data-toggle="tooltip"]').tooltip();
});

function approveMPR(mpr_appr_id,masterMPR)
{
	var chkMarked = $('#performerChk_'+mpr_appr_id).is(':checked');
	if(chkMarked)
	{
		$('#myModal .modal-content').load(
			SITEROOT + 'mprapprovals/mprapprovals/apporveperformer?mpr_appr_id='+mpr_appr_id+'&masterMPR='+masterMPR,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModal').modal(options);
			}
		);
	}
}


function verifyMPR(mpr_appr_id,masterMPR)
{
	var chkMarked = $('#verifierChk_'+mpr_appr_id).is(':checked');
	if(chkMarked)
	{
		$('#myModal .modal-content').load(
			SITEROOT + 'mprapprovals/mprapprovals/verifyperformer?mpr_appr_id='+mpr_appr_id+'&masterMPR='+masterMPR,
			function(e){
				var options = {
					"backdrop" : "static",
					"show" : true,
					"keyboard" : true
				}
				$('#myModal').modal(options);
			}
		);
	}
}
