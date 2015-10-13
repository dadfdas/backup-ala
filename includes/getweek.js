// JavaScript Document
<!-- 
//Browser Support Code

function getWeek(page){
	var ajaxRequest;  // The variable that makes Ajax possible!
	
	try{
		// Opera 8.0+, Firefox, Safari
		ajaxRequest = new XMLHttpRequest();
	} catch (e){
		// Internet Explorer Browsers
		try{
			ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try{
				ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e){
				// Something went wrong
				alert("Your browser broke!");
				return false;
			}
		}
	}
	doLoading('weken');
	// Create a function that will receive data sent from the server
	ajaxRequest.onreadystatechange = function(){
		if(ajaxRequest.readyState == 4){
			var tagetDiv = document.getElementById('weken');
			tagetDiv.innerHTML = ajaxRequest.responseText;
		}
	}
	
	ajaxRequest.open("GET", "includes/getweek.php?id=" + page, true);
	ajaxRequest.send(null); 	

}
function doLoading ( Target )
{
	var Img = '<img src="images/laden.gif" alt="Loading..." />';   
         
    document.getElementById ( Target ).innerHTML = Img + ' Een ogenblik geduld...';     
}
//-->
