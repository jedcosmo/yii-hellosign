function getBillOfMaterials()
{
	var mpr_definition_id_fk = $('#dsbh_bpr_batch').val();
	if(mpr_definition_id_fk > 0){
		$.get(SITEROOT+'site/dashboard',{mpr_definition_id_fk:mpr_definition_id_fk},function(data){
			if(data){
				$('#bom_list').html(data);
			}
			else{
				$('#bom_list').html("");
			}
		});
	}else{
		$('#bom_list').html("");
	}
}
