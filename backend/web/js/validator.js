function validator() {

	this.trim = function(str) {
		var m = str;
		if(m != '' || m != 'undefined') {
			return m.replace(/^[\s]+/,'').replace(/[\s]+$/,'').replace(/[\s]{2,}/,' ');
		}else{
			return '';
		}//if
	}//trim
	
	
	
	this.isEmptyValue = function(val){
		var trim_val = this.trim(val);	
		
		if(trim_val == '')
		{	
			return false;
		}
		else
		{
			return true;
		}
		
	}
	
	
	this.isNumber = function (objValue) {
		var charpos = objValue.search("[^0-9]");
		if(charpos == -1 && (objValue.length > 0) ) {	
			return true
		} else {
			return false
		}
	}	
	
//if(str.substr(0,1)==1)

//else if(str.substr(0,3)==2.1)
this.isname = function (objValue)
{
		var temp; 
		var lTag;
		var unm;
		lTag = 0;
		temp = (objValue.length);
		unm= objValue.substring(0,1);
		var charpos = unm.search("[^A-Za-z]");
		
		if(charpos != -1)
		{
			return false; 
		}
		else {	return true; }
		
}

	this.isAlpha = function(objValue) { 
		var charpos = objValue.search("[^A-Za-z]"); 
		if(objValue.length < 1) {
			return true;
		}
		
		if(charpos == -1) {
			return true;
			
		} else {
			return false;			
		}
	}

	this.isAlphaNumeric = function (objValue) {
			
		var objValue = this.trim(objValue);
		var charpos = objValue.search("[^A-Za-z0-9\.\_]"); 
		
		if(charpos == -1 && (objValue.length > 0) ) {
					return true;
		}else {
			return false
		}
		
	}

	this.validEmail = function (str) {
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		var err_msg = "Invalid E-mail ID";
		var return_val = true;
		
		if (str.indexOf(at)==-1)
		{	return_val = false	}
		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr)
		{   return_val = false	}
		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr)
		{	return_val = false	}
		if (str.indexOf(at,(lat+1))!=-1)
		{	return_val = false	}
		if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot)
		{	return_val = false	}
		if (str.indexOf(dot,(lat+2))==-1)
		{	return_val = false	}
		if (str.indexOf(" ")!=-1)
		{	return_val = false	}
		
		if(return_val == true) {
			return true;
		} else {
			return false;
		}
	}//validEmail
	
	this.verifyEmail = function(email,vemail) {
		if(vemail.length > 0 && email == vemail) {
			return true;
		}else{
			return false;
		}
	}//verifyEmail	
	
	
	this.validatePassword = function(pass) {
		var pass = this.trim(pass);
		if(pass.length < 6) {
			return false;
		}else{
			return true;
		}//if
	}//validatePassword
	
	
	this.verifyPassword = function(pass,vpass) {
		if(pass == vpass) {
			return true;
		}else{
			return false;
		}
	}//verifyPassword	
	
	
	this.clearData = function (arr) {
		for(i=0; i < arr.length; i++) {
			Dom.get(arr[i]).value = '';	
		}
	}
	
	
	
	
	
	this.hasWhiteSpace = function(s) {
		
		var str = s;
		
		for(i=0;i<str.length;i++){
			if(str[i] == " "){
				return true;	
			}
		}
	}	
	
	
}//validator

var validator = new validator();


function validateFileExtension(image_name,extensions){
	var image_file = image_name;  
	var image_length = image_name.length;  
	var pos = image_file.lastIndexOf('.') + 1;  
	var ext = image_file.substring(pos, image_length);  
	
	var final_ext = ext.toLowerCase(); 
	
	if(( extensions.indexOf(final_ext) > -1 ) ){
		return true;
	}
		
	return false;  
}
