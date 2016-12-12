<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

defined('MOODLE_INTERNAL') || die();

/**
 * @param int $oldversion
 * @param object $block
 */
function xmldb_block_togglerole_upgrade($oldversion, $block) {
    /*
    // Create a new role derived from "teacher"
    if ($oldversion < 2016120101) {
        $roleid = create_role(
            "Enseignant (vue simplifiée)",
            'teacherminimal',
            "Rôle d'enseignant, mais avec beaucoup de fonctionnalités désactivées. Prévu pour être utilisé par le bloc 'togglerole'.",
            'teacher'
        );
        unassign_capability('moodle/tag:editblocks', $roleid);
    }
     */

    return true;
}
