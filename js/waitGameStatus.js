/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

var preStatus = undefined;
var numPlayers = undefined;
var regPlayers = undefined;
function pollGameStatus() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
			if (preStatus == undefined)
				preStatus = data.game.status;
			if (data.game.status != preStatus)
				location.reload(true);
			numPlayers = data.game.num_players;
			regPlayers = data.game.registred_players;
			pollSuccess();
		},
		error: function() {
			location.reload(true);
		}
	});
}
var pollSuccess = function() {};

setInterval(pollGameStatus, 5000);