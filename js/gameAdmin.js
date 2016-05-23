/*
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$(function() {
    var kickPlayer = function() {
        var player = $("#kick-player").val();

        if (!confirm("Sei sicuro di voler espellere " + player + "?"))
            return;

        $.ajax({
            url: APIdir + "/game/" + room_name + "/" + game_name + "/admin/kick",
            type: 'POST',
            dataType: 'json',
            data: {
                player: player
            },
            success: function() {
                location.href += "/..";
            },
            error: function(error) {
                console.error(error);
                showError(getErrorMessage(error));
            }
        });
    };

    var termGame = function() {
        if (!confirm("Sei sicuro di voler terminare la partita?"))
            return;

        $.ajax({
            url: APIdir + "/game/" + room_name + "/" + game_name + "/admin/term",
            type: 'POST',
            dataType: 'json',
            success: function() {
                location.href += "/..";
            },
            error: function(error) {
                console.error(error);
                showError(getErrorMessage(error));
            }
        });
    };

    $("#do-kick-player").click(kickPlayer);
    $("#do-term-game").click(termGame);
});
