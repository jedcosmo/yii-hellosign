<?php
//namespace vendor\hellosign;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require(__DIR__ . '/../../../vendor/autoload.php');
require(__DIR__ . '/../../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../../common/config/bootstrap.php');
require(__DIR__ . '/../../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../common/config/main.php'),
    require(__DIR__ . '/../../../common/config/main-local.php'),
    require(__DIR__ . '/../../config/main.php'),
    require(__DIR__ . '/../../config/main-local.php')
);

$application = new yii\web\Application($config);

include('AbstractObject.php');  
include('BaseException.php');
include('Error.php');
include('AbstractResource.php');
include('AbstractSignatureRequest.php');
include('Signature.php');
include('AbstractList.php');
include('AbstractResourceList.php');      
include('SignatureList.php'); 
include('Signer.php'); 
include('SignerList.php'); 
include('SignatureRequest.php'); 
include('AbstractSignatureRequestWrapper.php'); 
include('EmbeddedResponse.php');
include('EmbeddedSignatureRequest.php');
include('Template.php'); 
include('TemplateList.php'); 
include('TemplateSignatureRequest.php'); 
include('Account.php');
include('Warning.php');  
include('Client.php');
include('config.php');


$module_primary_id = (isset($_REQUEST['module_primary_id']) && $_REQUEST['module_primary_id']>0)?$_REQUEST['module_primary_id']:0;
$module_name = (isset($_REQUEST['module_name']) && $_REQUEST['module_name']!='')?$_REQUEST['module_name']:'';
$signType = (isset($_REQUEST['signType']) && $_REQUEST['signType']!='')?$_REQUEST['signType']:'';

$masterMPR = isset($_REQUEST['masterMPR'])?$_REQUEST['masterMPR']:0;
$masterBPR = isset($_REQUEST['masterBPR'])?$_REQUEST['masterBPR']:0;

define('MODULE_NAME',$module_name);
define('MODULE_PRIMARY_ID',$module_primary_id);
define('SIGN_TYPE',$signType);
define('MASTER_MPR',$masterMPR);
define('MASTER_BPR',$masterBPR);

$docTitle = '';

ob_start();
if($module_name=='MPR_Approvals')
{
	$docTitle = 'Master Production Record';
	include 'mprPdfData.php';
}
else
{
	$docTitle = 'Batch Production Record';
	include 'bprPdfData.php';
}
$outputHtml = ob_get_contents();
ob_end_clean();
/************************************************/
			define('WEB_SITE_NAME','Cloud GMP');
			define('YOUR_COMPANY_NAME', 'Test Company');
			require_once('../tcpdf/tcpdf_include.php');
			
			$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
			
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor(WEB_SITE_NAME);
			$pdf->SetTitle(WEB_SITE_NAME);
			$pdf->SetSubject(WEB_SITE_NAME);
			$pdf->SetKeywords(WEB_SITE_NAME);
			
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			
			$pdf->setFontSubsetting(true);
			
			$pdf->SetFont('dejavusans', '', 10, '', true);
			
			$pdf->AddPage();
			
			$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
			
			$html = $outputHtml;

			print_r($html); die;
			
			$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
			
			$pdfname = time()."_".Yii::$app->user->identity->person_id_pk."_x30.pdf";
			$fullPdfName = Yii::$app->params['dirRootPath'].'/backend/web/uploads/hellosign_pdfs/'.$pdfname; 
			$pdf->Output($fullPdfName, 'F');

/************************************************/
if(file_exists($fullPdfName))
{
	$client_id = CLIENT_ID;
	$client = new HelloSign\Client(API_KEY);
	$account = $client->getAccount();
	
	$templates = $client->getTemplates();
	foreach ($templates as $template) { 
		//echo "TEMPLATE NAME : ".$template->getTitle() . "\n";
	}
	
	$request = new HelloSign\SignatureRequest;
	//$request->enableTestMode();
	$request->setTitle(Yii::$app->name.'-'.$docTitle);
	$request->setSubject(Yii::$app->name.'-'.$docTitle);
	$request->setMessage('Please sign this document and then we can discuss more. Let me know if you have any questions.');
	$request->addSigner(Yii::$app->user->identity->emailid, Yii::$app->user->identity->user_name_person);
	
	$request->addFile($fullPdfName);
	//$request->addFile('D:/xampp/htdocs/projects/cloudgmp.git/backend/web/uploads/bpr_doc2.pdf');
	$request->setUseTextTags(true);
	$request->setHideTextTags(true);
	
	// Turn it into an embedded request
	$embedded_request = new HelloSign\EmbeddedSignatureRequest($request, $client_id);
	
	// Send it to HelloSign
	$response = $client->createEmbeddedSignatureRequest($embedded_request);
	
	// Grab the signature ID for the signature page that will be embedded in the
	// page (for the demo, we'll just use the first one)
	$signatures   = $response->getSignatures();
	$signature_id = $signatures[0]->getId();
	
	//echo $signature_id;exit;
	
	// Retrieve the URL to sign the document
	$response = $client->getEmbeddedSignUrl($signature_id);
	
	// Store it to use with the embedded.js HelloSign.open() call
	$sign_url = $response->getSignUrl();
	//$sign_url = '';
	
	unlink($fullPdfName);
}
echo ".";

?>
<script type="text/javascript" src="jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="embedded.js"></script>
<script type="text/javascript">
	var module_name = '<?=$module_name;?>';
	var mySignType = '<?=$signType;?>';
    HelloSign.init('<?=CLIENT_ID;?>');
    HelloSign.open({
        url: "<?= $sign_url ?>",
		//test_mode : 1,   
		skipDomainVerification: true,
        allowCancel: true,
        messageListener: function(eventData) {
			var mySignature_id = eventData.signature_id;
			var jsonTxt = JSON.stringify(eventData); 
			if(eventData.event == 'signature_request_signed')
			{
				$.post('eventData.php', {signature_id:eventData.signature_id,module_primary_id:'<?=$module_primary_id;?>', module_name:module_name, signType:'<?=$signType;?>'}, function(data){
					if(data=="Success")
					{
						var signBtnObj = window.opener.$("#HelloSignBtn");
						var confirmBtnObj = window.opener.$("#HelloSignConfirmBtn");
						if(module_name=='BPR_Step_Approvals' || module_name=='BPR_Status_Change' || module_name=='BPR_Equipment_Operator')
						{
							var signatureHidObj = window.opener.$("#bpr_status_signature_id");
							$(signatureHidObj).val(mySignature_id);	
						}
						$(signBtnObj).hide();
						$(confirmBtnObj).show();
						window.close();
					}
				});
			}
        }    
    });
</script>
