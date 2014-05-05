/* 
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */


function vote() {
	var vote = $("#vote").val();
	$.ajax({
		url: APIdir + "/game/"+room_name+"/"+game_name+"/vote",
		type: 'GET',
		dataType: 'json',
		data: {
			vote: vote
		},
		success: function() {
			location.reload(true);
		},
		error: function(error) {
			console.error(error);
		}
	});
}