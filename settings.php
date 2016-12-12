<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

/* @var $ADMIN admin_root */
/* @var $DB moodle_database */

defined('MOODLE_INTERNAL') || die;

$settings->add(
    new admin_setting_configtext(
        'block_togglerole/buttontexton',
        get_string('labelbuttontexton', 'block_togglerole'),
        get_string('descrbuttontexton', 'block_togglerole'),
        ''
    )
);

$settings->add(
    new admin_setting_configtext(
        'block_togglerole/buttontextoff',
        get_string('labelbuttontextoff', 'block_togglerole'),
        get_string('descrbuttontextoff', 'block_togglerole'),
        ''
    )
);

$roles = $DB->get_records_sql_menu(
    "SELECT r.id, r.shortname FROM {role} r JOIN {role_context_levels} rcl ON rcl.roleid = r.id"
    . " WHERE rcl.contextlevel = :contextlevel ORDER BY r.shortname ASC",
    ['contextlevel' => CONTEXT_COURSE]
);
$defaultrole = $DB->get_record('role', ['shortname' => 'teacher']);
$settings->add(
    new admin_setting_configselect(
        'block_togglerole/roleid',
        get_string('labelroleid', 'block_togglerole'),
        get_string('descrroleid', 'block_togglerole'),
        (empty($defaultrole) ? 0 :$defaultrole->id),
        $roles
    )
);

$settings->add(
    new admin_setting_configcheckbox(
        'block_togglerole/everycourse',
        get_string('labeleverycourse', 'block_togglerole'),
        get_string('descreverycourse', 'block_togglerole'),
        0
    )
);

$settings->add(
    new admin_setting_configcheckbox(
        'block_togglerole/persistent',
        get_string('labelpersistent', 'block_togglerole'),
        get_string('descrpersistent', 'block_togglerole'),
        0
    )
);
