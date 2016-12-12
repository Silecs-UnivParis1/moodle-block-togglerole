<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

global $COURSE, $DB, $SESSION;

/* @var $DB moodle_database */
/* @var $PAGE moodle_page */

require_once('../../config.php');
require_once($CFG->dirroot . '/course/lib.php');

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

if (has_capability('moodle/role:switchroles', context_system::instance())) {
    // No switch in case of site-wide access
    redirect($returnurl);
    return;
}
if ($config->everycourse) {
    // already switched globally?
    if (empty($SESSION->blockToggleRoleActive)) {
        if ($roleid == 0) {
            redirect($returnurl);
            return;
        }
    } else if ($roleid != 0) {
        redirect($returnurl);
        return;
    }
} else {
    if ($roleid && is_role_switched($courseid)) {
        // already switched in this course, nothing to do
        redirect($returnurl);
        return;
    }
}

if ($config->everycourse) {
    /**
     * @todo Test if the role can be switched on the fly when the block is displayed.
     * If the block is computed before the main content of the page, it would be simpler and faster.
     */
    $roles = get_roles_with_capability('moodle/role:switchroles');
    $rolesids = join(',', array_filter($roles, function($x) {return $x->id;}));
    $DB->get_records_sql(
        "SELECT c.* FROM {context} c "
        . " JOIN {role_assignments} ra ON ra.contextid = c.id"
        . " WHERE ra.roleid IN ($rolesids) AND c.contextlevel = :c",
        [':c' => CONTEXT_COURSE]
    );
    /**
     * @todo Extend this to courses where the user has a role on a category above the course.
     */
    $contexts = [];
    foreach ($contexts as $context) {
        if ($roleid == 0) {
            role_switch(0, $context);
        } else if (has_capability('moodle/role:switchroles', $context)) {
            // Is this role assignable in this course context?
            $aroles = get_switchable_roles($context);
            if (is_array($aroles) && isset($aroles[$roleid])) {
                role_switch($roleid, $context);
            }
        }
    }
    $SESSION->blockToggleroleMessage = "invalid target role";
    $SESSION->blockToggleRoleActive = empty($SESSION->blockToggleRoleActive);
    if ($config->persistent) {
        /**
         * @todo store the toggled-role status in the DB
         */
    }
} else {
    // only this course
    if ($courseid != $SITE->id) {
        $context = context_course::instance($courseid);
        if ($roleid === 0) {
            role_switch(0, $context);
        }
        require_login($courseid);
        if ($roleid > 0 && has_capability('moodle/role:switchroles', $context)) {
            role_switch($roleid, $context);
        }
    }
}

redirect($returnurl);
