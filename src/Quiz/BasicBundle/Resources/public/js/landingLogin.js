$(document).ready(function () {
	
	/*setFormSwitch();
	
	$( "#loginForm button#submit" )
    .button()*/
    /*.click(function( event ) {
      event.preventDefault();
      
    });*/
	
	/*$( "#registerForm button#submit" )
    .button()*/
    /*.click(function( event ) {
      event.preventDefault();
      
    });*/
	
});

function setFormSwitch() {
	$("#setLogin").bind("click", function () {
		$("#formRegister").hide();
		$("#formLogin").show();		
	});
	
	$("#setRegister").bind("click", function () {
		$("#formLogin").hide();
		$("#formRegister").show();
	});
}