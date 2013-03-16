<?php

/**
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_acfvideo_activity_task
 */

/**
 * Structure step to restore one acfvideo activity
 */
class restore_acfvideo_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $paths[] = new restore_path_element('acfvideo', '/activity/acfvideo');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_acfvideo($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // insert the acfvideo record
        $newitemid = $DB->insert_record('acfvideo', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        // Add acfvideo related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_acfvideo', 'intro', null);
    }
}
