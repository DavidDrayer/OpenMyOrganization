$(function () {
	$("#btn_newmember").click(function () {

		$.ajax({
		  method: "POST",
		  url: "/ajax/newmember.php",
		  data: { mail: $("#txt_mail").val() },
		  dataType: "script"
		})
		  .done(function( msg ) {
			// pas besoin
			//alert( "Data Saved: " + msg );
		  })	
		  .fail(function( jqXHR, textStatus ) {
			  alert( "La connexion avec le serveur a échoué: " + jqXHR.responseText );
			});	
		
	});
	$("#btn_validate").click(function () {

		$.ajax({
		  method: "POST",
		  url: "/ajax/newmember.php",
		  data: { id: $("#user_id").val(),
			  username: $("#user_username").val(),
			  firstname: $("#user_firstname").val(),
			  lastname: $("#user_lastname").val(),
			  password: $("#user_password2").val(),
			  verif: $("#user_verif").val() },
		  dataType: "script"
		})
		  .done(function( msg ) {
			// pas besoin
			//alert( "Data Saved: " + msg );
		  })	
		  .fail(function( jqXHR, textStatus ) {
			  alert( "La connexion avec le serveur a échoué: " + jqXHR.responseText );
			});	
		
	});
});
