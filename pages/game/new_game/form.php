<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
?>
<div class="col-md-6">
    <div class="has-error has-feedback short-name">
        <label for="game-name">Nome della partita</label>
        <input class="form-control" id="game-name" onchange="checkGameName()">
        <span class="glyphicon glyphicon-remove form-control-feedback" id="game-name-icon"></span>
    </div>    
    <div class="has-error has-feedback">
        <label for="game-desc">Descrizione della partita</label>
        <input class="form-control" id="game-desc" onchange="checkGameDescr()">
        <span class="glyphicon glyphicon-remove form-control-feedback" id="game-desc-icon"></span>
    </div>
    <div class="short-name">
        <label for="game-num-player">Numero di giocatori</label>
        <select class="form-control" id="game-num-player">
            <option value="8">8</option>
            <option value="9">9</option>
            <option value="10">10</option>
            <option value="11">11</option>
            <option value="12">12</option>
            <option value="13">13</option>
            <option value="14">14</option>
            <option value="15">15</option>
            <option value="16">16</option>
            <option value="17">17</option>
            <option value="18">18</option>
        </select>
    </div>    
    <br>
    <button class="btn btn-success btn-lg disabled" id="create" onclick="newGame()">Crea!</button>
</div>
<script>
    var mexName = "<ul><li>Il nome della partita non può essere più lungo di 10 caratteri\
<li>E' formato da sole lettere maiuscole/minuscole e numeri\
<li>Il primo carattere deve essere una lettere\
<li>Il nome di una partita è unico all'interno della stanza";
    var mexDesc = "<ul><li>La descrizione di una partita non può essere più lunga di 45 caratteri\
<li>Deve essere lunga almeno 2 caratteri\
<li>Può essere formata solo da lettere maiuscole/minuscole e numeri";
    $("#game-name").popover({
        html: true,
        content: mexName,
        title: "Errore nel formato",
        container: "body",
        trigger: "manual"
    });
    $("#game-desc").popover({
        html: true,
        content: mexDesc,
        title: "Errore nel formato",
        container: "body",
        trigger: "manual"
    });
</script>