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