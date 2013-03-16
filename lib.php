<?php

/**
 * Mandatory public API of acfvideo module
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in acfvideo module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function acfvideo_supports($feature) {

	switch($feature) {
    	 case FEATURE_GROUPS:                  return true;
        case FEATURE_GROUPINGS:               return true;
        case FEATURE_GROUPMEMBERSONLY:        return true;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;
    	
        default: return null;
    }
}

/**
 * Returns all other caps used in module
 * @return array
 */
function acfvideo_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function acfvideo_reset_userdata($data) {
    return array();
}

/**
 * List of view style log actions
 * @return array
 */
function acfvideo_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 * @return array
 */
/*
function acfvideo_get_post_actions() {
    return array('update', 'add');
}
*/
/**
 * Add acfvideo instance.
 * @param object $data
 * @param object $mform
 * @return int new acfvideo instance id
 */
function acfvideo_add_instance($data, $mform) {
    global $DB;

    $parameters = array();
    for ($i=0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable  = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_POPUP) {
        $displayoptions['popupwidth']  = $data->popupwidth;
        $displayoptions['popupheight'] = $data->popupheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printheading'] = (int)!empty($data->printheading);
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    if (!empty($data->externalacfvideo) && (strpos($data->externalacfvideo, '://') === false) && (strpos($data->externalacfvideo, '/', 0) === false)) {
        $data->externalacfvideo = 'http://'.$data->externalacfvideo;
    }

    $data->timemodified = time();
    $data->id = $DB->insert_record('acfvideo', $data);

    return $data->id;
}

/**
 * Update acfvideo instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function acfvideo_update_instance($data, $mform) {
    global $CFG, $DB;

    $parameters = array();
    for ($i=0; $i < 100; $i++) {
        $parameter = "parameter_$i";
        $variable  = "variable_$i";
        if (empty($data->$parameter) or empty($data->$variable)) {
            continue;
        }
        $parameters[$data->$parameter] = $data->$variable;
    }
    $data->parameters = serialize($parameters);

    $displayoptions = array();
    if ($data->display == RESOURCELIB_DISPLAY_POPUP) {
        $displayoptions['popupwidth']  = $data->popupwidth;
        $displayoptions['popupheight'] = $data->popupheight;
    }
    if (in_array($data->display, array(RESOURCELIB_DISPLAY_AUTO, RESOURCELIB_DISPLAY_EMBED, RESOURCELIB_DISPLAY_FRAME))) {
        $displayoptions['printheading'] = (int)!empty($data->printheading);
        $displayoptions['printintro']   = (int)!empty($data->printintro);
    }
    $data->displayoptions = serialize($displayoptions);

    if (!empty($data->externalacfvideo) && (strpos($data->externalacfvideo, '://') === false) && (strpos($data->externalacfvideo, '/', 0) === false)) {
        $data->externalacfvideo = 'http://'.$data->externalacfvideo;
    }

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record('acfvideo', $data);

    return true;
}

/**
 * Delete acfvideo instance.
 * @param int $id
 * @return bool true
 */
function acfvideo_delete_instance($id) {
    global $DB;

    if (!$acfvideo = $DB->get_record('acfvideo', array('id'=>$id))) {
        return false;
    }

    // note: all context files are deleted automatically

    $DB->delete_records('acfvideo', array('id'=>$acfvideo->id));

    return true;
}

/**
 * Return use outline
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $acfvideo
 * @return object|null
 */
function acfvideo_user_outline($course, $user, $mod, $acfvideo) {
    global $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'acfvideo',
                                              'action'=>'view', 'info'=>$acfvideo->id), 'time ASC')) {

        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $result = new stdClass();
        $result->info = get_string('numviews', '', $numviews);
        $result->time = $lastlog->time;

        return $result;
    }
    return NULL;
}

/**
 * Return use complete
 * @param object $course
 * @param object $user
 * @param object $mod
 * @param object $acfvideo
 */
function acfvideo_user_complete($course, $user, $mod, $acfvideo) {
    global $CFG, $DB;

    if ($logs = $DB->get_records('log', array('userid'=>$user->id, 'module'=>'acfvideo',
                                              'action'=>'view', 'info'=>$acfvideo->id), 'time ASC')) {
        $numviews = count($logs);
        $lastlog = array_pop($logs);

        $strmostrecently = get_string('mostrecently');
        $strnumviews = get_string('numviews', '', $numviews);

        echo "$strnumviews - $strmostrecently ".userdate($lastlog->time);

    } else {
        print_string('neverseen', 'acfvideo');
    }
}

/**
 * Returns the users with data in one acfvideo
 *
 * @todo: deprecated - to be deleted in 2.2
 *
 * @param int $acfvideoid
 * @return bool false
 */
function acfvideo_get_participants($acfvideoid) {
    return false;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function acfvideo_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->dirroot/mod/acfvideo/locallib.php");

    if (!$acfvideo = $DB->get_record('acfvideo', array('id'=>$coursemodule->instance), 'id, name, display, displayoptions, externalacfvideo, parameters')) {
        return NULL;
    }

    $info = new stdClass();
    $info->name = $acfvideo->name;

    //note: there should be a way to differentiate links from normal resources
    $info->icon = acfvideo_guess_icon($acfvideo->externalacfvideo);

    $display = acfvideo_get_final_display_type($acfvideo);

    if ($display == RESOURCELIB_DISPLAY_POPUP) {
        $fullacfvideo = "$CFG->wwwroot/mod/acfvideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $options = empty($acfvideo->displayoptions) ? array() : unserialize($acfvideo->displayoptions);
        $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
        $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
        $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
        $info->extra = "onclick=\"window.open('$fullacfvideo', '', '$wh'); return false;\"";

    } else if ($display == RESOURCELIB_DISPLAY_NEW) {
        $fullacfvideo = "$CFG->wwwroot/mod/acfvideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->extra = "onclick=\"window.open('$fullacfvideo'); return false;\"";

    } else if ($display == RESOURCELIB_DISPLAY_OPEN) {
        $fullacfvideo = "$CFG->wwwroot/mod/acfvideo/view.php?id=$coursemodule->id&amp;redirect=1";
        $info->extra = "onclick=\"window.location.href ='$fullacfvideo';return false;\"";
    }

    return $info;
}

/**
 * This function extends the global navigation for the site.
 * It is important to note that you should not rely on PAGE objects within this
 * body of code as there is no guarantee that during an AJAX request they are
 * available
 *
 * @param navigation_node $navigation The acfvideo node within the global navigation
 * @param stdClass $course The course object returned from the DB
 * @param stdClass $module The module object returned from the DB
 * @param stdClass $cm The course module instance returned from the DB
 */
function acfvideo_extend_navigation($navigation, $course, $module, $cm) {
    /**
     * This is currently just a stub so that it can be easily expanded upon.
     * When expanding just remove this comment and the line below and then add
     * you content.
     */
    $navigation->nodetype = navigation_node::NODETYPE_LEAF;
}

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function acfvideo_page_type_list($pagetype, $parentcontext, $currentcontext) {
    $module_pagetype = array('mod-acfvideo-*'=>get_string('page-mod-acfvideo-x', 'acfvideo'));
    return $module_pagetype;
}
