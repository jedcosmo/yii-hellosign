var IE6 = (navigator.userAgent.indexOf("MSIE 6")>=0) ? true : false;
var IE7 = (navigator.userAgent.indexOf("MSIE 7")>=0) ? true : false;
var IE8 = (navigator.userAgent.indexOf("MSIE 8")>=0) ? true : false;
var IE9 = (navigator.userAgent.indexOf("MSIE 9")>=0) ? true : false;

if(IE6 || IE7 || IE8 || IE9){

	$(function(){
		
		$("<div>")
			.css({
				'position': 'absolute',
				'top': '0px',
				'left': '0px',
				backgroundColor: 'black',
				'opacity': '0.75',
				'width': '100%',
				'height': jQuery(document).height(), //'3000px',
				zIndex: 5000
			})
			.appendTo("body");			
		$("<div><img src='"+SITEROOT+"images/no-ie6.png' alt='' style='float: left;'/><p><br /><strong>Sorry! This application support Internet Explorer version 9+.</strong><br /><br />If you'd like to use our application then please upgrade your browser.</p>")
			.css({
				backgroundColor: 'white',
				'top': '50%',
				'left': '50%',
				marginLeft: -210,
				marginTop: -100,
				width: 410,
				paddingRight: 10,
				height: 200,
				'position': 'absolute',
				zIndex: 6000
			})
			.appendTo("body");
	});		
}
