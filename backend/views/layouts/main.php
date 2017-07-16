<?php
use yii\helpers\Html;
use common\models\CommonFunctions;
use common\models\checkCommonFunctions;
use common\models\getCommonFunctions;

/* @var $this \yii\web\View */
/* @var $content string */
if(substr($_SERVER['HTTP_HOST'], 0, 3) == "www")
{
	// Remove www.
	$domain_name = preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
	$redirect = "http://".$domain_name.$_SERVER['REQUEST_URI'];
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: $redirect");
	exit;
}


if (Yii::$app->controller->action->id === 'login') { 
/**
 * Do not use this code in your template. Remove it. 
 * Instead, use the code  $this->layout = '//main-login'; in your controller.
 */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {
	if(CommonFunctions::checkSessionUserId()==false)
	{
		header("Location:".Yii::$app->homeUrl."site/logout");
		exit;
	}
	
	//Unlock MPR & BPR records for multi-threading issue
	$lockController = Yii::$app->controller->id; 
	$lockAction = Yii::$app->controller->action->id;
	if($lockAction!='index'){}
	else
	{
		CommonFunctions::unlockTheRecord();
	}

	//Get logged-in person details	
	$user = getCommonFunctions::getPersonDetails(Yii::$app->user->identity->person_id_pk);

	if($user['isDeleted']=='1')
	{
		header("Location:".Yii::$app->homeUrl."site/logout");
		exit;
	}
	
	//Unlock all records of MPR & BPR from DB if recent activity timestamp is greater than 15mins.
	if(Yii::$app->user->isGuest==false && Yii::$app->user->identity->person_id_pk>0)
	{
		CommonFunctions::unlockAllUserRecords();
		CommonFunctions::updateRecentActivityTime(Yii::$app->user->identity->person_id_pk);
	}
	
	/***************************************************/
    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }

    dmstr\web\AdminLteAsset::register($this);

    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
	
	$directoryAssetN = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Yii::$app->name ?> <?= Html::encode($this->title) ?></title>
        <!-- FontAwesome 4.3.0 -->
    	<link href="<?= Yii::$app->homeUrl;?>css/font-awesome.min.4.4.0.css" rel="stylesheet" type="text/css" />
    	<!-- Ionicons 2.0.0 -->
    	<link href="<?= Yii::$app->homeUrl;?>css/ionicons.min.2.0.1.css" rel="stylesheet" type="text/css" />    
        <?php $this->head() ?>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/2.3.11/css/AdminLTE.css" >
        
     <script type="text/javascript">
	 	var SITEROOT = '<?= Yii::$app->homeUrl;?>';
		
		function showGlobalLoadingMessage(status){
			$("#divShowGlobalLoadingMessage").addClass('alert alert-success');
			if(status == 'start')
				$("#divMainShowGlobalLoadingMessage").show('slow');
			if(status == 'end')
				$("#divMainShowGlobalLoadingMessage").hide('slow');
		}
	 </script>

	 	 <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
     
    </head>
    <!--<body class="skin-blue sidebar-mini">-->
    <body class="skin-black sidebar-mini hold-transition skin-blue fixed">
    
    <div style="position:fixed; top:55px; width:100%; opacity:0.8;z-index:1030; display:none" id="divMainShowGlobalLoadingMessage">
        <div style="margin:0 auto; width:20%">
            <div class="" style="font-weight:bold" id="divShowGlobalLoadingMessage"><span><img src="<?=Yii::$app->homeUrl?>images/indicator.gif" > &nbsp; Processing...</span></div>
        </div>
    </div>
     
	<!-- <div id="overlayLoad" align="center">
    	 <img src="<?=Yii::$app->homeUrl?>images/indicator.gif" alt="Loading" />&nbsp;&nbsp;Loading...
	</div> -->
	
    <?php $this->beginBody() ?>
    <div class="wrapper">

        <?= $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>

        <?= $this->render(
            'left.php',
            ['directoryAsset' => $directoryAsset]
        )
        ?>
        <?= $this->render(
            'content.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        ) ?>

    </div>

    <?php $this->endBody() ?>
    <script type="text/javascript" src="<?= Yii::$app->homeUrl;?>js/jquery.cookie.js"></script>
   	<script type="text/javascript">
		$(window).load(function(){
   		// PAGE IS FULLY LOADED  
   		// FADE OUT YOUR OVERLAYING DIV
   		$('#overlayLoad').fadeOut();
		});

		<?php if (Yii::$app->controller->action->id != 'verifyemail') { ?>
		var checkIfIOS = iOS();
		var checkIfIE = msieversion();
		if(checkIfIOS==false && checkIfIE==false)
		{
			// Wrap in an IIFE accepting jQuery as a parameter.
			(function ($) {
				var setCookie,
					removeCookie,
					// Create constants for things instead of having same string
					// in multiple places in code.
					COOKIE_NAME = 'TabOpen',
					SITE_WIDE_PATH = { path : '/' };
			
				setCookie = function () {
					$.cookie(COOKIE_NAME, '1', SITE_WIDE_PATH); 
				};
			
				removeCookie = function () {
					$.removeCookie(COOKIE_NAME, SITE_WIDE_PATH);
				};
			
				// We don't need to wait for DOM ready to check the cookie
				if ($.cookie(COOKIE_NAME) === undefined) {
					setCookie();
					$(window).unload(removeCookie);
				} else {
					// Replace the whole body with an error message when the DOM is ready.
					$(function () { 
						$('body').html('<div style="background-color: #eeeeee;border: 1px solid #CCCCCC;margin: 20px;min-height: 300px;width: 95%;">' + 
							'<p style="background-color:#0d69a3;font-size:18px;font-weight:bold;padding:5px;color:#FFFFFF;">CloudGMP Notice</p>'+
							'<div class="error" style="width:100%;text-align:center;"><h2>Sorry!</h2>'+
							'<p>You can only have one instance of this web page open at a time.</p>' + 
							'<p>Due to security reasons - we are not allowing another instance.</p>'+
							'</div>');
						});
				}
			}(jQuery));
		}
		<?php } ?>
		/****Function to detect device type*****************************/
		function iOS() 
		{
			  var iDevices = [
				'iPad Simulator',
				'iPhone Simulator',
				'iPod Simulator',
				'iPad',
				'iPhone',
				'iPod'
			  ];
			
			  if (!!navigator.platform) 
			  {
					while (iDevices.length) 
					{
						if (navigator.platform === iDevices.pop()){ return true; }
					}
			  }
			  return false;
		}
		/****Function to detect IE browser*****************************/
		function msieversion() 
		{
			var ua = navigator.userAgent;
			var msie = ua.indexOf("MSIE");
		
			if (msie > 0) // If Internet Explorer, return version number
			{
				return true;
			}
			else  // If another browser, return 0
			{
				return false;
			}
		
			return false;
		}
		/**************************************************************/
		
		</script>
   <script type="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
