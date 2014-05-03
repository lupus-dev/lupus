/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function login() {
	var username = $("#username").val();
	var password = $("#password").val();
			$("#status").text("Login in corso...");
	$.ajax({
		url: APIdir + "/login",
		type: 'GET',
		dataType: 'json',
		data: {
			"username": username,
			"password": password
		},
		success: function(data) {
			$("#status").text("Login riuscito");
			document.location.href = "index";
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error(jqXHR);
			if (jqXHR.status == 401)
				$("#status").text("Nome utente/Password errati");
			else
				$("#status").text("Errore connessione al server");
		}
	});
}

$(function() {
	var form = $(".login-form");
	form.on("submit", function(e) {
		e.preventDefault();
		login();
	});
});