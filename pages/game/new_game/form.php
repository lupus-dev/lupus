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
    <button class="btn btn-success btn-lg" onclick="newGame()">Crea!</button>
</div>