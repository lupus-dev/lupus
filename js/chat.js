/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var players = [];
var groups = [];
var curr_group, curr_user;

function loadPlayers() {
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name,
		type: 'GET',
		dataType: 'json',
		async: false,
		success: function(data) {
			players = data.game.registred_players;
		},
		error: function(error) {
			console.error(error);	
			showError(getErrorMessage(error));
		}
	});
}
function loadGroups() {
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name + "/chat",
		type: 'GET',
		dataType: 'json',
		async: false,
		success: function(data) {
			groups = data.chat;
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}
function loadNav() {
	loadGroups();
	loadPlayers();

	$(".chat-groups").html("");
	for (i in groups) {
		var a = $("<a>");
		a.attr("href", "#chat");
		a.text(groups[i] + " ");
		a.append($("<span>").addClass("badge"));

		var li = $("<li>");
		li.attr("data-group", groups[i]);
		li.click(clickGroup);
		if (groups[i] == "Game")
			a.text("Partita ");
		if (groups[i] == "User") {
			li.addClass("dropdown");
			a.text("Utente ");
			a.addClass("dropdown-toggle")
					.attr("href", "#")
					.attr("data-toggle", "dropdown");
			a.append($("<span>").addClass("badge"));
			a.append($("<span>").addClass("caret"));


			var ul = $("<ul>").addClass("dropdown-menu");
			for (j in players)
				if (players[j] != username) {
					ul.append(
							$("<li>")
							.append(
									$("<a>").text(players[j] + " ").attr("href", "#chat").append($("<span>").addClass("badge"))
									)
							.attr("data-user", players[j])
							.click(clickUser)
							);
				}
			li.append(ul);
		}

		li.append(a);
		$(".chat-groups").append(li);
	}
}

function getDate(date) {
	var d = new Date();
	d.setTime(date * 1000);
	var day = d.getDate();
	var month = ['gen', 'feb', 'mar', 'apr', 'mag', 'giu', 'lug', 'ago', 'set', 'ott', 'nov', 'dic'][d.getMonth()];
	var hour = d.getHours();
	var minute = d.getMinutes();
	var second = d.getSeconds();
	return ((day < 10) ? '0' + day : day) + ' ' +
			month + ' - ' +
			((hour < 10) ? '0' + hour : hour) + ':' +
			((minute < 10) ? '0' + minute : minute) + ':' +
			((second < 10) ? '0' + second : second) + ' ';
}
function getMessage(mex) {
	var time = $("<span>").addClass("chat-time").text(getDate(mex.timestamp));
	var from = $("<span>").addClass("chat-from").text(mex.from);
	var text = $("<span>").addClass("chat-mex").text(mex.text);

	return $("<div>").addClass("chat-message").append(time).append(from).append(text);
}

function loadUserMessages(data, user) {
	var messages = $(".chat-body").html("");
	for (i in data) {
		var mex = data[i];
		if (mex.from == user || mex.to == user)
			messages.append(getMessage(mex));
	}
}
function loadGroupMessages(data) {
	var messages = $(".chat-body").html("");
	for (i in data)
		messages.append(getMessage(data[i]));
}

function loadChat(group, user) {
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name + "/chat/" + group,
		type: 'GET',
		data: {
			user: user
		},
		dataType: 'json',
		success: function(data) {
			if (group == "User")
				loadUserMessages(data.messages, user);
			else
				loadGroupMessages(data.messages);
			$(".chat-body").scrollTop($(".chat-body")[0].scrollHeight);
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}
function switchToChat(group, user) {
	loadChat(group, user);
	$(".chat-groups li").removeClass("active");
	$("li[data-group=" + group + "]").addClass("active");
	if (group == "User") {
		var badge = $("li[data-group=User] > a > .badge");
		var dec = $("li[data-user=" + user + "] .badge").text();
		var pre = badge.text();
		badge.text(pre - dec);
		if (pre - dec <= 0)
			badge.hide();

		$("li[data-user=" + user + "] .badge").hide();
	} else {
		$("li[data-group=" + group + "] .badge").hide();
	}
	curr_group = group;
	curr_user = user;
}

function clickGroup() {
	var group = $(this).data("group");
	if (group == "User")
		return;
	switchToChat(group);
}
function clickUser() {
	var user = $(this).data("user");
	switchToChat("User", user);
}

function pollMessages() {
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name + "/chat/after",
		type: 'GET',
		data: {
			min: true
		},
		dataType: 'json',
		success: function(data) {
			for (var k in data.groups) {
				if (data.groups[k].after > 0) {
					$("li[data-group=" + k + "] .badge").text(data.groups[k].after).show();
					if (k == curr_group)
						loadChat(curr_group, curr_user);
				}
				else
					$("li[data-group=" + k + "] .badge").hide();
			}
			var users_count = 0;
			for (var j in data.users) {
				users_count += data.users[j].after;
				if (data.users[j].after > 0)
					$("li[data-user=" + j + "] .badge").text(data.users[j].after).show();
				else
					$("li[data-user=" + j + "] .badge").hide();
			}
			if (users_count > 0)
				$("li[data-group=User] > a > .badge").text(users_count).show();
			else
				$("li[data-group=User] > a > .badge").hide();
		},
		error: function(jqXHR) {
			console.error(jqXHR);
			showError(getErrorMessage(error));
		}
	});
}
function sendMessage() {
	var text = $("#chat-text").val();
	$.ajax({
		url: APIdir + "/game/" + room_name + "/" + game_name + "/chat/" + curr_group + "/post",
		type: 'GET',
		dataType: 'json',
		data: {
			dest: curr_user,
			text: text
		},
		success: function() {
			switchToChat(curr_group, curr_user);
			$("#chat-text").val("");
		},
		error: function(jqXHR) {
			console.error(jqXHR);
			showError(getErrorMessage(error));
		}
	});
}

$(function() {
	loadNav();
	switchToChat("Game");
	pollMessages();
	setInterval(pollMessages, 5000);
	$("#chat-form").submit(function() {
		sendMessage();
		return false;
	});
});