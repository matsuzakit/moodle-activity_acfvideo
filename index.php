<?php

/**
 * List of acfvideos in course
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2009 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

add_to_log($course->id, 'acfvideo', 'view all', "index.php?id=$course->id", '');

$stracfvideo       = get_string('modulename', 'acfvideo');
$stracfvideos      = get_string('modulenameplural', 'acfvideo');
$strsectionname  = get_string('sectionname', 'format_'.$course->format);
$strname         = get_string('name');
$strintro        = get_string('moduleintro');
$strlastmodified = get_string('lastmodified');

$PAGE->set_acfvideo('/mod/acfvideo/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname.': '.$stracfvideos);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add($stracfvideos);
echo $OUTPUT->header();

if (!$acfvideos = get_all_instances_in_course('acfvideo', $course)) {
    notice(get_string('thereareno', 'moodle', $stracfvideos), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);
if ($usesections) {
    $sections = get_all_sections($course->id);
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($usesections) {
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($acfvideos as $acfvideo) {
    $cm = $modinfo->cms[$acfvideo->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($acfvideo->section !== $currentsection) {
            if ($acfvideo->section) {
                $printsection = get_section_name($course, $sections[$acfvideo->section]);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $acfvideo->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($acfvideo->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // each acfvideo has an icon in 2.0
        $icon = '<img src="'.$OUTPUT->pix_acfvideo($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" /> ';
    }
    $class = $acfvideo->visible ? '' : 'class="dimmed"'; // hidden modules are dimmed
    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($acfvideo->name)."</a>",
        format_module_intro('acfvideo', $acfvideo, $cm->id));
}

echo html_writer::table($table);
echo $OUTPUT->footer();
