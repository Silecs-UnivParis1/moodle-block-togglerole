<?php
/* 
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL v3
 */

class block_togglerole extends block_base {
    private $dbconfig = [];

    private $enabled = false;

    /**
     * Called by Moodle before the block is shown.
     */
    public function init() {
        global $COURSE, $SESSION;
        $this->dbconfig = get_config("block_togglerole");
        $this->enabled = (!empty($this->dbconfig->everycourse) && !empty($SESSION->blockToggleRoleActive))
            || (isset($COURSE->id) && is_role_switched($COURSE->id));
        $this->title = get_string('togglerole', 'block_togglerole');
    }

    /**
     * Return the HTML content of the block as an assoc array.
     *
     * @return array
     */
    public function get_content() {
        global $COURSE, $OUTPUT, $SESSION;

        if (!$this->enabled &&
            (!has_capability('block/togglerole:view', context_course::instance($COURSE->id))
            || has_capability('moodle/role:switchroles', context_system::instance()))
            ) {
            return null;
        }

        $this->content = new stdClass;
        $this->content->text = $OUTPUT->single_button(
            new moodle_url(
                '/blocks/togglerole/toggle.php',
                [
                    'courseid' => $COURSE->id,
                    'roleid' => $this->enabled ? 0 : $this->dbconfig->roleid,
                    'returnurl' => $this->page->url,
                ]
            ),
            ($this->enabled ?
                ($this->dbconfig->buttontextoff ? $this->dbconfig->buttontextoff : get_string('buttontextoff', 'block_togglerole'))
                : ($this->dbconfig->buttontexton ? $this->dbconfig->buttontexton : get_string('buttontexton', 'block_togglerole')))
        );
        if (!empty($SESSION->blockToggleroleMessage)) {
            $this->content->footer = $SESSION->blockToggleroleMessage;
            unset($SESSION->blockToggleroleMessage);
        } else {
            $this->content->footer = '';
        }
        return $this->content;
    }

    /**
     * Tell Moodle to read "settings.php".
     *
     * @return boolean
     */
    function has_config() {
        return true;
    }

    /**
     * Tell Moodle where displaying an instance is allowed.
     *
     * @return array
     */
    public function applicable_formats() {
        return ['course-view' => true];
    }
}
