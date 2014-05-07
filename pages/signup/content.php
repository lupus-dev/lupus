<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */
?>
<div class="col-md-4 col-md-offset-4">
    <form class="signup-form" action="#">
        <h1>Registrati</h1>
        <table>
            <tr>
                <td><label for="username">Username</label></td>
                <td><input type="text" class="form-control" name="username" id="username" placeholder="Il tuo nome utente"></td>
            </tr>
            <tr>
                <td><label for="password">Password</label></td>
                <td><input type="password" class="form-control" name="password" id="password" placeholder="La tua password"></td>
            </tr>
            <tr>
                <td><label for="nome">Nome</label></td>
                <td><input type="text" class="form-control" name="nome" id="nome" placeholder="Il tuo nome"></td>
            </tr>
            <tr>
                <td><label for="nome">Cognome</label></td>
                <td><input type="text" class="form-control" name="cognome" id="cognome" placeholder="Il tuo cognome"></td>
            </tr>
        </table>
        <button type="submit" class="btn btn-success btn-lg" id="do-login">Signup</button>
        <p id="status"></p>
    </form>
</div>