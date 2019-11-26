<?php
/*
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

/**
 *
 * @global StdClass $SESSION
 * @param integer $roleid
 * @param integer $courseid
 * @param boolean $everycourse
 * @return boolean
 */
function block_togglerole_hasaccess($roleid, $courseid, $everycourse) {
    global $SESSION;
    if (has_capability('moodle/role:switchroles', context_system::instance())) {
        // No switch in case of site-wide access
        return false;
    }
    if ($everycourse) {
        // already switched globally?
        if (empty($SESSION->blockToggleRoleActive)) {
            if ($roleid == 0) {
                return false;
            }
        } else if ($roleid != 0) {
            return false;
        }
    } else {
        if ($roleid && is_role_switched($courseid)) {
            // already switched in this course, nothing to do
            return false;
        }
    }
    return true;
}

/**
 * @global StdClass $SITE
 * @param integer $courseid
 * @param integer $roleid
 */
function block_togglerole_toggle($courseid, $roleid) {
    global $SITE;
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

/**
 * @global moodle_database $DB
 * @global StdClass $SESSION
 * @param integer $roleid
 */
function block_togglerole_toggleall($roleid) {
    global $DB, $SESSION, $USER;

    if (has_capability('moodle/role:switchroles', context_system::instance())) {
        // No switch in case of site-wide access
        return false;
    }

    /* Go through every course where the current user has a "powerful" role.
     * (If the block was computed before the main content of the page,
     *  it would be simpler and faster, but that's not possible.)
     */
    $roles = get_roles_with_capability('moodle/role:switchroles');
    $rolesids = join(',', array_map(function($x) {return $x->id;}, $roles));
    $directContexts = $DB->get_records_sql(
        "SELECT DISTINCT " . context_helper::get_preload_record_columns_sql('c')
        . " FROM {context} c "
        . " JOIN {role_assignments} ra ON ra.contextid = c.id"
        . " WHERE ra.roleid IN ($rolesids) AND c.contextlevel = :c AND userid = :u",
        ['c' => CONTEXT_COURSE, 'u' => $USER->id]
    );
    foreach ($directContexts as $ctx) {
        $courseid = $ctx->ctxinstance; // this field will be deleted by a vicious side-effect!
        context_helper::preload_from_record($ctx);
        $context = context_course::instance($courseid);
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
    //$SESSION->blockToggleroleMessage = get_string("statuschanged", 'block_togglerole');
    $SESSION->blockToggleRoleActive = empty($SESSION->blockToggleRoleActive);
}
