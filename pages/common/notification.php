<?php
/**
 * Lupus in Tabula
 *  ...un progetto di Edoardo Morassutto
 *  Contributors:
 *   - 2016 Edoardo Morassutto <edoardo.morassutto@gmail.com>
 */

?>
<ul class="nav navbar-nav">
    <li class="dropdown" id="notifications">
        <a href="#" id="notifications-toggle" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-record hidden-xs"></span>
            <span class="visible-xs">Notifiche</span>
        </a>
        <div class="dropdown-menu">
            <a id="notifications-refresh" href="#" class="pull-right glyphicon glyphicon-refresh"></a>
            <h4 id="notifications-title">Notifiche</h4>

            <?php $notifications = Notification::getLastNotifications($user); ?>
            <?php if (!$notifications) { ?>
                <p id="notifications-empty">Nessuna notifica recente...</p>
            <?php } else { ?>
                <p id="notifications-empty">Loading...</p>
            <?php } ?>
        </div>
    </li>
</ul>
