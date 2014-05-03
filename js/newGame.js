/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var name_ok = false;
var descr_ok = false;

function checkGameName() {
	var game_name = $("#game-name").val();
	$.ajax({
		url: APIdir + "/checkGameName",
		type: 'GET',
		dataType: 'json',
		data: {
			room_name: room_name,
			game_name: game_name
		},
		success: function(data) {
			var status = data.status;
			var container = $("#game-name").parent();
			if (status) {
				container.addClass("has-success").removeClass("has-error");
				$("#game-name-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");				
				$("#game-name").popover("hide");
				name_ok = true;
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#game-name-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
				$("#game-name").popover("show");
				name_ok = false;
			}
			if (name_ok && descr_ok)
				$("#create").removeClass("disabled");
			else
				$("#create").addClass("disabled");
		},
		error: function(data) {
			console.error(data);
			var container = $("#game-name").parent();
			container.removeClass("has-success").addClass("has-error");
			$("#game-name-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		}
	});
}
function checkGameDescr() {
	var game_descr = $("#game-desc").val();
	$.ajax({
		url: APIdir + "/checkGameDescr",
		type: 'GET',
		dataType: 'json',
		data: {
			game_descr: game_descr
		},
		success: function(data) {
			var status = data.status;
			var container = $("#game-desc").parent();
			if (status) {
				container.addClass("has-success").removeClass("has-error");
				$("#game-desc-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");
				$("#game-desc").popover("hide");
				descr_ok = true;
			} else {
				container.removeClass("has-success").addClass("has-error");
				$("#game-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
				$("#game-desc").popover("show");
				descr_ok = false;
			}
			if (name_ok && descr_ok)
				$("#create").removeClass("disabled");
			else
				$("#create").addClass("disabled");
		},
		error: function() {
			var container = $("#game-name").parent();
			container.removeClass("has-success").addClass("has-error");
			$("#game-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		}
	});
}

function newGame() {
	if (!(name_ok && descr_ok))
		return;
	var game_name = $("#game-name").val();
	var game_descr = $("#game-desc").val();
	var num_players = $("#game-num-player").val();

	data = {
		descr: game_descr,
		num_players: num_players
	};
	if ($("#private").length > 0 && $("#private").prop("checked"))
		data.private = true;

	$.ajax({
		url: APIdir + "/new_game/" + room_name + "/" + game_name,
		type: 'GET',
		dataType: 'json',
		data: data,
		success: function() {
			document.location.href = game_name;
		},
		error: function(jqXHR) {
			console.error(jqXHR);
		}
	});
}