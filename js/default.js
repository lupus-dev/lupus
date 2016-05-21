/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var path = "";
var APIdir = path + "/api";

var errorCount = 0;
var lastError = 0;

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
	var div = $("<div>")			
			.addClass("alert")
			.addClass("alert-danger")
			.addClass("alert-dismissable")
			.attr("id", "error-"+(errorCount++));
	div.append('<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>');
	div.append(message.error);
	$("nav.navbar").after(div);
	setTimeout(removeError, 2000);
}
function getErrorMessage(jqError) {
	return JSON.parse(jqError.responseText);
}
function removeError() {
	$("#error-"+(lastError++)).fadeTo(500, 0).slideUp(500, function(){
		$(this).remove();	
	});
}
