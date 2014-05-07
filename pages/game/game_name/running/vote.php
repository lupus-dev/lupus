<?php
/*
 * Lupus in Tabula
 * ...un progetto di Edoardo Morassutto
 * Contributors:
 * - 2014 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

$engine = new Engine($game);
$role = Role::fromUser($user, $engine);

if ($day)
    $needVote = $role->needVoteDay();
else
    $needVote = $role->needVoteNight();
?>
<?php if ($needVote): ?>
    <h2>Devi votare!</h2>
    <?= $needVote["pre"] ?>
    <select class="form-control" id="vote">
        <?php foreach ($needVote["votable"] as $vote): ?>
            <option value="<?= $vote ?>"><?= $vote ?></option>
        <?php endforeach; ?>
    </select>
    <p>Cosa aspetti? <button class="btn btn-success" onclick="vote()">Vota!</button></p>    
<?php else: ?>
    <?php if ($day): ?>
        <?php if (Role::getRoleStatus($game, $user->id_user) == RoleStatus::Dead): ?>
            <h2>Sei morto...</h2>
            <p>Per ora sei morto...</p>
        <?php else: ?>
            <h2>Grazie per aver votato!</h2>
            <p>La tua votazione è il frutto di un'attenta politica democratica.</p>
            <p>Il tuo contributo può aiutare a liberare il villaggio dai lupi!</p>
        <?php endif; ?>
    <?php else: ?>
        <?php if (Role::getRoleStatus($game, $user->id_user) == RoleStatus::Dead): ?>
            <h2>Sei morto...</h2>
            <p>Per ora sei morto...</p>
        <?php else: ?>
            <h2>ronf...ronf...</h2>
            <p>Attendi l'alba per tornare al tuo lavoro quotidiano!</p>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>
