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

var lastNotificationUpdate = null;

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
	try {
		return JSON.parse(jqError.responseText);
	} catch (e) {
		return JSON.stringify(jqError);
	}
}
function removeError() {
	$("#error-"+(lastError++)).fadeTo(500, 0).slideUp(500, function(){
		$(this).remove();	
	});
}

function removeNotification() {
	var $this = $(this);
	var id_notification = $this.attr('data-id-notification');
	$.ajax({
		url: APIdir + "/notification/dismiss",
		data: { id_notification: id_notification },
		dataType: 'json',
		type: 'POST',
		success: function(data) {
			$this.closest('.notification-element').remove();
			if ($('.notification-element').length == 0)
				$('.dropdown-menu').append("<p id='notifications-empty'>Nessuna notifica recente...</p>");
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
	return false;
}

function refreshNotifications() {
	$.ajax({
		url: APIdir + "/notification/update",
		data: { since: lastNotificationUpdate },
		dataType: 'json',
		type: 'GET',
		success: function (data) {
			lastNotificationUpdate = new Date();
			appendNotifications(data);
		},
		error: function (error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
	return false;
}

function appendNotifications(list) {
	if (list.length == 0) return;
	var $notifications = $("#notifications .dropdown-menu");
	$("#notifications-empty").remove();

	while (list.length) {
		var noti = list.pop();

		var $noti = $("<a href='"+noti.link+"' class='notification-element'>");
		$noti.append(
			$('<div class="btn-dismiss-notification" data-id-notification="'+noti.id_notification+'">')
				.append('<div class="glyphicon glyphicon-remove"></div>')
				.click(removeNotification)
		);
		$noti.append(noti.message);

		$noti.insertAfter("#notifications-title");
	}
}

$(function() {
	if (location.pathname != path + "/login") {
		$(".btn-dismiss-notification").click(removeNotification);
		$("#notifications-refresh").click(refreshNotifications);
		$("#notifications-toggle").dropdown();
		$("#notifications").on("show.bs.dropdown", function () {
			refreshNotifications();
		});
		refreshNotifications();
	}
});
