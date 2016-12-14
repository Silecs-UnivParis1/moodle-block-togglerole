<?php
/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

namespace block_togglerole;

defined('MOODLE_INTERNAL') || die();

require_once dirname(__DIR__) . '/locallib.php';

class observers {
    /**
     * @param \core\event\user_loggedin $event
     */
    public static function on_user_loggedin(\core\event\user_loggedin $event) {
        global $DB;

        $edata = (object) $event->get_data(); // See https://docs.moodle.org/dev/Event_2#Properties
        $config = \get_config("block_togglerole");
        if ($config->persistent && $DB->record_exists('block_togglerole', ['userid' => $edata->userid])) {
            $config = \get_config("block_togglerole");
            \block_togglerole_toggleall((int) $config->roleid);
        }
    }
}
