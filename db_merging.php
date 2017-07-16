<?php
/*******************************************************/
// Purpose of this script is to merge existing database in new table structure
// After change to DB table, it's neccessary to map old records into new relational table.
/*******************************************************/

$server = 'SERVER';

switch($server)
{
	case 'LOCAL':
			$host = 'localhost';
			$USR = 'root';
			$PWD = '';
			$DBNM = 'mts_cloudgmp';
			break;
	case 'SERVER':
			$host = 'localhost';
			$USR = 'cloudx30';
			$PWD = '1rWwa4~2';
			$DBNM = 'mts_cloudgmp';
			break;
	default:
			break;
}
$conn = mysqli_connect($host, $USR, $PWD);  

if (!$conn) {
	die("Connection failed:");
	exit();
}
$db_check = mysqli_select_db($conn,$DBNM);
if(!$db_check)	{trigger_error(mysqli_error(),E_USER_ERROR); }	

$eqpmapResult = mysqli_query($conn,"SELECT e.*,b.bpr_id_pk,b.mpr_version FROM bpr_equipment_map as e LEFT JOIN bpr_batch_processing_records as b ON b.mpr_definition_id_fk=e.mpr_defination_id_fk ");

if($eqpmapResult)
{
?>
	<table cellpadding="2" cellspacing="2" width="100%" border="1">
    	<tr>
            <td>#</td>
            <td>EQP ID</td>
            <td>BPR ID</td>
            <td>MPR ID</td>
            <td>MPR Version</td>
            <td>Status</td>
        </tr>
<?php
	$k = 1;
	$mprBprArray = array();
	while($eqpmapRow = mysqli_fetch_assoc($eqpmapResult))
	{
		if(isset($eqpmapRow['bpr_id_pk']) && $eqpmapRow['approved_status']=='Approved')
		{
			$insert_Operator_signature_status = mysqli_query($conn,"INSERT INTO bpr_operator_signature_status (eqp_map_id_fk, bpr_id_fk, mpr_definition_id_fk, approved_status, approved_datetime, approved_by_person_id_fk, HS_signature_id) VALUES ('".$eqpmapRow['equipment_map_id_pk']."','".$eqpmapRow['bpr_id_pk']."','".$eqpmapRow['mpr_defination_id_fk']."','".$eqpmapRow['approved_status']."','".$eqpmapRow['approved_datetime']."','".$eqpmapRow['approved_by_person_id_fk']."','".$eqpmapRow['HS_signature_id']."')");
		} 
		?>
        	<tr>
            	<td><?=$k;?></td>
                <td><?=$eqpmapRow['equipment_map_id_pk'];?></td>
                <td><?=$eqpmapRow['bpr_id_pk'];?></td>
                <td><?=$eqpmapRow['mpr_defination_id_fk'];?></td>
                <td><?=$eqpmapRow['mpr_version'];?></td>
                <td><?=$eqpmapRow['approved_status'];?></td>
            </tr>
        <?php
			$mprBprArray[$k] =  'BPR :'.$eqpmapRow['bpr_id_pk'].'|| MPR :'.$eqpmapRow['mpr_defination_id_fk'];
		$k++;
	}
	//echo "<pre>";print_r($mprBprArray);
}
?>