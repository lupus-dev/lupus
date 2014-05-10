/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var players = [];
var groups = [];
var curr_group, curr_user;
var last_poll = 0;

function loadPlayers() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name,
		type: 'GET',
		dataType: 'json',
		async: false,
		success: function(data) {
			players = data.game.registred_players;
		},
		error: function(error) {
			console.error(error);
		}
	});
}
function loadGroups() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/chat",
		type: 'GET',
		dataType: 'json',
		async: false,
		success: function(data) {
			groups = data.chat;
		},
		error: function(error) {
			console.error(error);
		}
	});
}
function loadNav() {
	loadGroups();
	loadPlayers();
	
	$(".chat-groups").html("");
	for (i in groups) {
		var a = $("<a>");
		a.attr("href", "#");
		a.text(groups[i]);
		
		var li = $("<li>");
		li.attr("data-group", groups[i]);
		li.click(clickGroup);
		if (groups[i] == "Game")
			a.text("Partita");
		if (groups[i] == "User") {
			li.addClass("dropdown");
			a.text("Utente ");
			a.addClass("dropdown-toggle")
					.attr("href", "#")
					.attr("data-toggle", "dropdown");
			a.append($("<span>").addClass("caret"));
			
			var ul = $("<ul>").addClass("dropdown-menu");
			for (j in players)
				if (players[j] != username)
					ul.append(
						$("<li>")
							.append(
								$("<a>").text(players[j]).attr("href", "#")
							)
							.attr("data-user", players[j])
							.click(clickUser)
						);
			li.append(ul);
		}
		
		li.append(a);
		$(".chat-groups").append(li);
	}
}

function getDate(date) {
	var d = new Date();
	d.setTime(date*1000);
	//return d.toDateString() + " - " + d.toTimeString() + " ";
	return "1 gen - 12:32:45 ";
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
		url: APIdir + "/game/"+room_name+"/"+game_name+"/chat/"+group,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
			if (group == "User")
				loadUserMessages(data.messages, user);
			else
				loadGroupMessages(data.messages);
		},
		error: function(jqXHR) {
			
		}
	});
}
function switchToChat(group, user) {
	loadChat(group, user);
	$(".chat-groups li").removeClass("active");
	$("li[data-group="+group+"]").addClass("active");
	if (group == "User")
		$("li[data-user="+user+"]").addClass("active");
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
		url: APIdir + "/game/"+room_name+"/"+game_name+"/chat/"+curr_group+"/last",
		type: 'GET',
		dataType: 'json',
		data: {
			user: curr_user
		},
		success: function(data) {
			console.log("Polled");
			if (last_poll < data.timestamp) {
				switchToChat(curr_group, curr_user);
				console.log("Refresh");
			}
			last_poll = data.timestamp;
		},
		error: function(jqXHR) {
			console.error(jqXHR);
		}
	});
}
function sendMessage() {
	var text = $("#chat-text").val();
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/chat/"+curr_group+"/post",
		type: 'GET',
		dataType: 'json',
		data: {
			dest: curr_user,
			text: text
		},
		success: function(data) {
			pollMessages();
			$("#chat-text").val("");
		},
		error: function(jqXHR) {
			console.error(jqXHR);
		}
	});
}

$(function(){
	loadNav();
	switchToChat("Game");
	setInterval(pollMessages, 5000);
});