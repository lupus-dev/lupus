/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function login() {
	var username = $("#username").val();
	var password = $("#password").val();
	var nome = $("#nome").val();
	var cognome = $("#cognome").val();
	$("#status").text("Registrazione in corso...");
	$.ajax({
		url: APIdir + "/signup",
		type: 'GET',
		dataType: 'json',
		data: {
			username: username,
			password: password,
			name: nome,
			surname: cognome
		},
		success: function() {
			$("#status").text("Registrazione riuscita");
			document.location.href = "index";
		},
		error: function(error) {
			console.error(error);
			$("#status").text("Registrazione fallita");
			showError(getErrorMessage(error));
		}
	});
}

$(function() {
	var form = $(".signup-form");
	form.on("submit", function(e) {
		e.preventDefault();
		login();
	});
});