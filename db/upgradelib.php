<?php

/**
 * acfvideo module upgrade related helper functions
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Migrate acfvideo module data from 1.9 resource_old table to new acfvideo table
 * @return void
 */
function acfvideo_20_migrate() {
    global $CFG, $DB;

    require_once("$CFG->libdir/filelib.php");
    require_once("$CFG->libdir/resourcelib.php");
    require_once("$CFG->dirroot/course/lib.php");

    if (!file_exists("$CFG->dirroot/mod/resource/db/upgradelib.php")) {
        // bad luck, somebody deleted resource module
        return;
    }

    require_once("$CFG->dirroot/mod/resource/db/upgradelib.php");

    // create resource_old table and copy resource table there if needed
    if (!resource_20_prepare_migration()) {
        // no modules or fresh install
        return;
    }

    $candidates = $DB->get_recordset('resource_old', array('type'=>'file', 'migrated'=>0));
    if (!$candidates->valid()) {
        $candidates->close(); // Not going to iterate (but exit), close rs
        return;
    }

    foreach ($candidates as $candidate) {
        $path = $candidate->reference;
        $siteid = get_site()->id;

        if (strpos($path, 'LOCALPATH') === 0) {
            // ignore not maintained local files - sorry
            continue;
        } else if (!strpos($path, '://')) {
            // not acfvideo
            continue;
        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$siteid(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // handled by resource module
            continue;
        } else if (preg_match("|$CFG->wwwroot/file.php(\?file=)?/$candidate->course(/[^\s'\"&\?#]+)|", $path, $matches)) {
            // handled by resource module
            continue;
        }

        upgrade_set_timeout();

        if ($CFG->texteditors !== 'textarea') {
            $intro       = text_to_html($candidate->intro, false, false, true);
            $introformat = FORMAT_HTML;
        } else {
            $intro       = $candidate->intro;
            $introformat = FORMAT_MOODLE;
        }

        $acfvideo = new stdClass();
        $acfvideo->course       = $candidate->course;
        $acfvideo->name         = $candidate->name;
        $acfvideo->intro        = $intro;
        $acfvideo->introformat  = $introformat;
        $acfvideo->externalacfvideo  = $path;
        $acfvideo->timemodified = time();

        $options    = array('printheading'=>0, 'printintro'=>1);
        $parameters = array();
        if ($candidate->options == 'frame') {
            $acfvideo->display = RESOURCELIB_DISPLAY_FRAME;

        } else if ($candidate->options == 'objectframe') {
            $acfvideo->display = RESOURCELIB_DISPLAY_EMBED;

        } else if ($candidate->popup) {
            $acfvideo->display = RESOURCELIB_DISPLAY_POPUP;
            if ($candidate->popup) {
                $rawoptions = explode(',', $candidate->popup);
                foreach ($rawoptions as $rawoption) {
                    list($name, $value) = explode('=', trim($rawoption), 2);
                    if ($value > 0 and ($name == 'width' or $name == 'height')) {
                        $options['popup'.$name] = $value;
                        continue;
                    }
                }
            }

        } else {
            $acfvideo->display = RESOURCELIB_DISPLAY_AUTO;
        }
        $acfvideo->displayoptions = serialize($options);

        if ($candidate->alltext) {
            $rawoptions = explode(',', $candidate->alltext);
            foreach ($rawoptions as $rawoption) {
                list($variable, $parameter) = explode('=', trim($rawoption), 2);
                $parameters[$parameter] = $variable;
            }
        }

        $acfvideo->parameters = serialize($parameters);

        if (!$acfvideo = resource_migrate_to_module('acfvideo', $candidate, $acfvideo)) {
            continue;
        }
    }

    $candidates->close();

    // clear all course modinfo caches
    rebuild_course_cache(0, true);
}
