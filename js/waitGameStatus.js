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
			regPlayers = data.game.registred_players.length;
			pollSuccess();
		},
		error: function(error) {
			location.reload(true);
			showError(getErrorMessage(error));
		}
	});
}
var pollSuccess = function() {};

if (!pollFreq)
	pollFreq = 5000;

setInterval(pollGameStatus, pollFreq);