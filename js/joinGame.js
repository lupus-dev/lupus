/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function joinGame() {
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/join",
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