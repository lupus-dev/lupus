/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var APIdir = "/lupus/api";

function login() {
	var username = $("#username").val();
	var password = $("#password").val();
	$.ajax({
		url: APIdir + "/login",
		type: 'GET',
		dataType: 'json',
		data: {
			"username": username,
			"password": password
		},
		success: function(data, textStatus, jqXHR) {
			document.location.href = "index";
		},
		error: function(jqXHR, textStatus, errorThrown) {
			console.error(jqXHR);
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