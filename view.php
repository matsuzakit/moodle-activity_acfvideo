<?php

/**
 * acfvideo module main user interface
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');
require_once("$CFG->dirroot/mod/acfvideo/locallib.php");
require_once($CFG->libdir . '/completionlib.php');
require_once('./elcflib.php');

$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$u        = optional_param('u', 0, PARAM_INT);         // acfvideo instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $acfvideo = $DB->get_record('acfvideo', array('id'=>$u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('acfvideo', $acfvideo->id, $acfvideo->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('acfvideo', $id, 0, false, MUST_EXIST);
    $acfvideo = $DB->get_record('acfvideo', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/acfvideo:view', $context);

add_to_log($course->id, 'acfvideo', 'view', 'view.php?id='.$cm->id, $acfvideo->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/acfvideo/view.php', array('id' => $cm->id));

$restrictedpath = getcfurl($acfvideo->externalacfvideo);

if (acfvideo_get_final_display_type($acfvideo) == RESOURCELIB_DISPLAY_EMBED){
	$acfvideo->externalacfvideo = './player.php?dst='.$restrictedpath;
	acfvideo_display_embed($acfvideo, $cm, $course); 
}else{
		redirect('./player.php?dst='.$restrictedpath);
}
