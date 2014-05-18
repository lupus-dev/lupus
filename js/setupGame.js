/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

function checkGameDescr() {
	var descr = $("#game-desc").val();
	var container = $("#game-desc").parent();
	if (isValidDescr(descr)) {
		container.addClass("has-success").removeClass("has-error");
		$("#game-desc-icon").addClass("glyphicon-ok").removeClass("glyphicon-remove");
		$("#save").removeClass("disabled");
		$("#start").removeClass("disabled");
	}
	else {
		container.removeClass("has-success").addClass("has-error");
		$("#game-desc-icon").removeClass("glyphicon-ok").addClass("glyphicon-remove");
		$("#save").addClass("disabled");
		$("#start").addClass("disabled");
	}
}

function saveGame(start) {
	var game_descr = $("#game-desc").val();
	var num_players = $("#game-num-player").val();
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/setup",
		type: 'GET',
		dataType: 'json',
		data: {
			descr: game_descr,
			num_players: num_players
		},
		success: function(data) {
			console.log(data);
			if (start) 
				ajaxStart();
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}
function startGame() {
	saveGame(true);
}
function ajaxStart() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/start",
		type: 'GET',
		dataType: 'json',
		success: function() {
			location.reload(true);
		},
		error: function(error) {
			console.error(error);
			showError(getErrorMessage(error));
		}
	});
}