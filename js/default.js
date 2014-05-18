/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var path = "/lupus";
var APIdir = path + "/api";

function logout() {
	$.ajax({
		url: APIdir+"/logout",
		type: "GET",
		dataType: "json",
		async: false,
		success: function() {
			window.location.href = path + "/login";
		},
		error: function(e) {
			window.location.href = path + "/login";
		}
	});
}
function isShortName(name) {
	var regex = /^[a-zA-Z][a-zA-Z0-9]{0,9}$/;
	return regex.test(name);
}
function isValidDescr(descr) {
	var regex = /^[a-zA-Z0-9][a-zA-Z0-9 ]{0,43}[a-zA-Z0-9]$/;
	return regex.test(descr);
}
function showError(message) {
	var div = $("<div>").addClass("alert").addClass("alert-danger").addClass("alert-dismissable");
	div.append('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>');
	div.append(message.error);
	$("nav.navbar").after(div);
}
function getErrorMessage(jqError) {
	return JSON.parse(jqError.responseText);
}