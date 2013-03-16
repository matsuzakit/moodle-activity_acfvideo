<?php

/**
 * Define all the backup steps that will be used by the backup_acfvideo_activity_task
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2010 onwards Andrew Davis
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

 /**
 * Define the complete acfvideo structure for backup, with file and id annotations
 */
class backup_acfvideo_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        //the acfvideo module stores no user info

        // Define each element separated
        $acfvideo = new backup_nested_element('acfvideo', array('id'), array(
            'name', 'intro', 'introformat', 'externalacfvideo',
            'display', 'displayoptions', 'parameters', 'timemodified'));


        // Build the tree
        //nothing here for acfvideos

        // Define sources
        $acfvideo->set_source_table('acfvideo', array('id' => backup::VAR_ACTIVITYID));

        // Define id annotations
        //module has no id annotations

        // Define file annotations
        $acfvideo->annotate_files('mod_acfvideo', 'intro', null); // This file area hasn't itemid

        // Return the root element (acfvideo), wrapped into standard activity structure
        return $this->prepare_activity_structure($acfvideo);

    }
}
