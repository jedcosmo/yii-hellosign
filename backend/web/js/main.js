yii.allowAction = function ($e) {
    var message = $e.data('confirm');
    return message === undefined || yii.confirm(message, $e);
};

yii.confirm = function (message, ok, cancel) {
 	message = "<h3 class='delTitle'>X30</h3>" + message;
 
    bootbox.prompt(
        {
            title: message,
			value: '',
			placeholder: 'Reason',
			maxlength: 255,
            /*buttons: {
                confirm: {
                    label: "OK"
                },
                cancel: {
                    label: "Cancel"
                }
            },*/
            callback: function (confirmed) {
				$('.bootbox-form').append('<span id="errmsgPrompt" class="error"></span>');
                if (confirmed) {
					var delReason = $('.bootbox-form .bootbox-input-text').val();
					if(delReason!='')
					{
                    	!ok || ok();
						$('#errmsgPrompt').hide();
					}
                } else if(confirmed!=null) {
					$('.bootbox-form .bootbox-input-text').attr('style','border:1px solid red');
					$('#errmsgPrompt').html('Please enter reason');
					$('#errmsgPrompt').show();
					return false;
                    //!cancel || cancel();
                }
				else{
					$('#errmsgPrompt').hide();
					!cancel || cancel();
				}
            }
        }
    );
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
}

function base64_encode(data) {
  //  discuss at: http://phpjs.org/functions/base64_encode/
  // original by: Tyler Akins (http://rumkin.com)
  // improved by: Bayron Guevara
  // improved by: Thunder.m
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // improved by: Rafal Kukawski (http://kukawski.pl)
  // bugfixed by: Pellentesque Malesuada
  //   example 1: base64_encode('Kevin van Zonneveld');
  //   returns 1: 'S2V2aW4gdmFuIFpvbm5ldmVsZA=='
  //   example 2: base64_encode('a');
  //   returns 2: 'YQ=='

  var b64 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
  var o1, o2, o3, h1, h2, h3, h4, bits, i = 0,
    ac = 0,
    enc = '',
    tmp_arr = [];

  if (!data) {
    return data;
  }

  do { // pack three octets into four hexets
    o1 = data.charCodeAt(i++);
    o2 = data.charCodeAt(i++);
    o3 = data.charCodeAt(i++);

    bits = o1 << 16 | o2 << 8 | o3;

    h1 = bits >> 18 & 0x3f;
    h2 = bits >> 12 & 0x3f;
    h3 = bits >> 6 & 0x3f;
    h4 = bits & 0x3f;

    // use hexets to index into b64, and append result to encoded string
    tmp_arr[ac++] = b64.charAt(h1) + b64.charAt(h2) + b64.charAt(h3) + b64.charAt(h4);
  } while (i < data.length);

  enc = tmp_arr.join('');

  var r = data.length % 3;

  return (r ? enc.slice(0, r - 3) : enc) + '==='.slice(r || 3);
}


/*	How to know browser idle time?	*/
var IDLE_TIMEOUT = 1800; //seconds	30	180
var _idleSecondsTimer = null;
var _idleSecondsCounter = 0;

document.onclick = function() {
    _idleSecondsCounter = 0;
};

document.onmousemove = function() {
    _idleSecondsCounter = 0;
};

document.onkeypress = function() {
    _idleSecondsCounter = 0;
};

_idleSecondsTimer = window.setInterval(CheckIdleTime, 1000);

function CheckIdleTime() {
     _idleSecondsCounter++;
     var oPanel = document.getElementById("SecondsUntilExpire");
     if (oPanel)
         oPanel.innerHTML = (IDLE_TIMEOUT - _idleSecondsCounter) + "";
	if(_idleSecondsCounter >= 1200){	//10
		$("#divAutoLoggedOut").show();
	}else{
		$("#divAutoLoggedOut").hide();
	}
    if (_idleSecondsCounter >= IDLE_TIMEOUT) {
        window.clearInterval(_idleSecondsTimer);
        //alert("Time expired!");
        document.location.href = SITEROOT + "site/logout";
    }
}
/**/



/*yii.confirm = function (message, $e) {
    bootbox.confirm(message, function (confirmed) {
        if (confirmed) {
            yii.handleAction($e);
        }
    });
    // confirm will always return false on the first call
    // to cancel click handler
    return false;
}*/
