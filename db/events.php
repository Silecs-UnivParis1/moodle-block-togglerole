<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

$observers = array(
    [
        'eventname'   => '\core\event\user_loggedin',
        'callback'    => '\block_togglerole\observers::on_user_loggedin',
        'includefile' => 'blocks/togglerole/classes/observers.php',
    ]
);
