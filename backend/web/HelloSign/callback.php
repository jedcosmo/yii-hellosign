<?php
	include('config.php');
	
	
	$request = $_POST;	
	
	$data = implode(",",$request);
	$data = json_decode(trim($data));
	
	// Get the event type.
	$event_type = $data->event->event_type;
	
	
	// The signature_request_all_signed event is called whenever the signature
	// request is completely signed by all signees, HelloSign has processed
	// the document and has it available for download.
	if ($event_type == 'signature_request_all_signed')
	{
		$signature_request = $data->signature_request->signature_request_id;
		$is_complete = $data->signature_request->is_complete;
		$signature_id = $data->signature_request->signatures[0]->signature_id;
		$docFileUrl = "https://api.hellosign.com/v3/signature_request/files/".$signature_request;
		
		// Create connection
		$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	
		// Check connection
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		
		$sql = "INSERT INTO bpr_hellosign_requests (HS_signature_id_fk,event_type,signature_request_id,doc_file_url) VALUES ('".$signature_id."','".$event_type."','".$signature_request."','".$docFileUrl."')";
		$conn->query($sql);
		$conn->close();

	}
	
	echo "Hello API Event Received";
		
?>
