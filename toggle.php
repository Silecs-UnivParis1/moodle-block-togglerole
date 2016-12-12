<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

global $COURSE, $DB, $SESSION;

/* @var $DB moodle_database */
/* @var $PAGE moodle_page */

require_once('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once(__DIR__ . '/locallib.php');

$courseid = optional_param('courseid', 0, PARAM_INT);
$returnurl  = optional_param('returnurl', '', PARAM_RAW);
$roleid = required_param('roleid', PARAM_INT);

$PAGE->set_url('/blocks/togglerole/toggle.php', ['roleid' => $roleid]);
require_sesskey();

$config = get_config("block_togglerole");
if ($roleid > 0 && $roleid != $config->roleid) {
    $SESSION->blockToggleroleMessage = "invalid target role";
    redirect($returnurl);
    return;
}

if ($returnurl) {
    if (strpos($returnurl, $CFG->wwwroot) !== 0) {
        $returnurl = $CFG->wwwroot . $returnurl;
    }
    $returnurl = clean_param($returnurl, PARAM_URL);
} else {
    $returnurl = new moodle_url('/course/view.php', array('id' => $courseid));
}

if (!block_togglerole_hasaccess($roleid, $courseid, $config->everycourse)) {
    redirect($returnurl);
    return;
}

if ($config->everycourse) {
    block_togglerole_toggleall($roleid);
    if ($config->persistent) {
        // store the toggled-role status in the DB
    }
} else {
    // only this course
    block_togglerole_toggle($courseid, $roleid);
}

redirect($returnurl);
