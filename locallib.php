<?php

/**
 * Private acfvideo module utility functions
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/acfvideo/lib.php");

/**
 * Return full acfvideo with all extra parameters
 * @param string $acfvideo
 * @param object $cm
 * @param object $course
 * @return string acfvideo
 */
function acfvideo_get_full_acfvideo($acfvideo, $cm, $course, $config=null) {

    $parameters = empty($acfvideo->parameters) ? array() : unserialize($acfvideo->parameters);

    if (empty($parameters)) {
        // easy - no params
        return $acfvideo->externalacfvideo;
    }

    if (!$config) {
        $config = get_config('acfvideo');
    }
    $paramvalues = acfvideo_get_variable_values($acfvideo, $cm, $course, $config);

    foreach ($parameters as $parse=>$parameter) {
        if (isset($paramvalues[$parameter])) {
            $parameters[$parse] = acfvideoencode($parse).'='.acfvideoencode($paramvalues[$parameter]);
        } else {
            unset($parameters[$parse]);
        }
    }

    if (empty($parameters)) {
        // easy - no params available
        return $acfvideo->externalacfvideo;
    }

    if (stripos($acfvideo->externalacfvideo, 'teamspeak://') === 0) {
        return $acfvideo->externalacfvideo.'?'.implode('?', $parameters);
    } else {
        $join = (strpos($acfvideo->externalacfvideo, '?') === false) ? '?' : '&amp;';
        return $acfvideo->externalacfvideo.$join.implode('&amp;', $parameters);
    }
}

/**
 * Print acfvideo header.
 * @param object $acfvideo
 * @param object $cm
 * @param object $course
 * @return void
 */
function acfvideo_print_header($acfvideo, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname.': '.$acfvideo->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($acfvideo);
    echo $OUTPUT->header();
}

/**
 * Print acfvideo heading.
 * @param object $acfvideo
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function acfvideo_print_heading($acfvideo, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($acfvideo->displayoptions) ? array() : unserialize($acfvideo->displayoptions);

    if ($ignoresettings or !empty($options['printheading'])) {
        echo $OUTPUT->heading(format_string($acfvideo->name), 2, 'main', 'acfvideoheading');
    }
}

/**
 * Print acfvideo introduction.
 * @param object $acfvideo
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function acfvideo_print_intro($acfvideo, $cm, $course, $ignoresettings=false) {
    global $OUTPUT;

    $options = empty($acfvideo->displayoptions) ? array() : unserialize($acfvideo->displayoptions);
    if ($ignoresettings or !empty($options['printintro'])) {
        if (trim(strip_tags($acfvideo->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'acfvideointro');
            echo format_module_intro('acfvideo', $acfvideo, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}


/**
 * Display embedded acfvideo file.
 * @param object $acfvideo
 * @param object $cm
 * @param object $course
 * @param stored_file $file main file
 * @return does not return
 */
function acfvideo_display_embed($acfvideo, $cm, $course) {
    global $CFG, $PAGE, $OUTPUT;

    $mimetype = resourcelib_guess_url_mimetype($acfvideo->externalacfvideo);
    $fullacfvideo  = acfvideo_get_full_acfvideo($acfvideo, $cm, $course);
    $title    = $acfvideo->name;

    $link = html_writer::tag('a', $fullacfvideo, array('href'=>str_replace('&amp;', '&', $fullacfvideo)));
    $clicktoopen = get_string('clicktoopen', 'acfvideo', $link);

    $extension = resourcelib_get_extension($acfvideo->externalacfvideo);

    $mediarenderer = $PAGE->get_renderer('core', 'media');
    $embedoptions = array(
    		core_media::OPTION_TRUSTED => true,
    		core_media::OPTION_BLOCK => true
    );
    
    $code = resourcelib_embed_general($fullacfvideo, $title, $clicktoopen, $mimetype);
    acfvideo_print_header($acfvideo, $cm, $course);
    acfvideo_print_heading($acfvideo, $cm, $course);

    echo $code;

    acfvideo_print_intro($acfvideo, $cm, $course);

    echo $OUTPUT->footer();
    die;
}

/**
 * Decide the best diaply format.
 * @param object $acfvideo
 * @return int display type constant
 */
function acfvideo_get_final_display_type($acfvideo) {
    global $CFG;

    if ($acfvideo->display != RESOURCELIB_DISPLAY_AUTO) {
        return $acfvideo->display;
    }

    // detect links to local moodle pages
    if (strpos($acfvideo->externalacfvideo, $CFG->wwwroot) === 0) {
        if (strpos($acfvideo->externalacfvideo, 'file.php') === false and strpos($acfvideo->externalacfvideo, '.php') !== false ) {
            // most probably our moodle page with navigation
            return RESOURCELIB_DISPLAY_OPEN;
        }
    }

    static $download = array('application/zip', 'application/x-tar', 'application/g-zip',     // binary formats
                             'application/pdf', 'text/html');  // these are known to cause trouble for external links, sorry
    static $embed    = array('image/gif', 'image/jpeg', 'image/png', 'image/svg+xml',         // images
                             'application/x-shockwave-flash', 'video/x-flv', 'video/x-ms-wm', // video formats
                             'video/quicktime', 'video/mpeg', 'video/mp4',
                             'audio/mp3', 'audio/x-realaudio-plugin', 'x-realaudio-plugin',   // audio formats,
                            );

    $mimetype = resourcelib_guess_url_mimetype($acfvideo->externalacfvideo);

    if (in_array($mimetype, $download)) {
        return RESOURCELIB_DISPLAY_DOWNLOAD;
    }
    if (in_array($mimetype, $embed)) {
        return RESOURCELIB_DISPLAY_EMBED;
    }

    // let the browser deal with it somehow
    return RESOURCELIB_DISPLAY_OPEN;
}

/**
 * Get the parameters that may be appended to acfvideo
 * @param object $config acfvideo module config options
 * @return array array describing opt groups
 */
function acfvideo_get_variable_options($config) {
    global $CFG;

    $options = array();
    $options[''] = array('' => get_string('chooseavariable', 'acfvideo'));

    $options[get_string('course')] = array(
        'courseid'        => 'id',
        'coursefullname'  => get_string('fullnamecourse'),
        'courseshortname' => get_string('shortnamecourse'),
        'courseidnumber'  => get_string('idnumbercourse'),
        'coursesummary'   => get_string('summary'),
        'courseformat'    => get_string('format'),
    );

    $options[get_string('modulename', 'acfvideo')] = array(
        'acfvideoinstance'     => 'id',
        'acfvideocmid'         => 'cmid',
        'acfvideoname'         => get_string('name'),
        'acfvideoidnumber'     => get_string('idnumbermod'),
    );

    $options[get_string('miscellaneous')] = array(
        'sitename'        => get_string('fullsitename'),
        'serveracfvideo'       => get_string('serveracfvideo', 'acfvideo'),
        'currenttime'     => get_string('time'),
        'lang'            => get_string('language'),
    );
    if (!empty($config->secretphrase)) {
        $options[get_string('miscellaneous')]['encryptedcode'] = get_string('encryptedcode');
    }

    $options[get_string('user')] = array(
        'userid'          => 'id',
        'userusername'    => get_string('username'),
        'useridnumber'    => get_string('idnumber'),
        'userfirstname'   => get_string('firstname'),
        'userlastname'    => get_string('lastname'),
        'userfullname'    => get_string('fullnameuser'),
        'useremail'       => get_string('email'),
        'usericq'         => get_string('icqnumber'),
        'userphone1'      => get_string('phone').' 1',
        'userphone2'      => get_string('phone2').' 2',
        'userinstitution' => get_string('institution'),
        'userdepartment'  => get_string('department'),
        'useraddress'     => get_string('address'),
        'usercity'        => get_string('city'),
        'usertimezone'    => get_string('timezone'),
        'useracfvideo'         => get_string('webpage'),
    );

    if ($config->rolesinparams) {
        $roles = get_all_roles();
        $roleoptions = array();
        foreach ($roles as $role) {
            $roleoptions['course'.$role->shortname] = get_string('yourwordforx', '', $role->name);
        }
        $options[get_string('roles')] = $roleoptions;
    }

    return $options;
}

/**
 * Get the parameter values that may be appended to acfvideo
 * @param object $acfvideo module instance
 * @param object $cm
 * @param object $course
 * @param object $config module config options
 * @return array of parameter values
 */
function acfvideo_get_variable_values($acfvideo, $cm, $course, $config) {
    global $USER, $CFG;

    $site = get_site();

    $values = array (
        'courseid'        => $course->id,
        'coursefullname'  => format_string($course->fullname),
        'courseshortname' => $course->shortname,
        'courseidnumber'  => $course->idnumber,
        'coursesummary'   => $course->summary,
        'courseformat'    => $course->format,
        'lang'            => current_language(),
        'sitename'        => format_string($site->fullname),
        'serveracfvideo'       => $CFG->wwwroot,
        'currenttime'     => time(),
        'acfvideoinstance'     => $acfvideo->id,
        'acfvideocmid'         => $cm->id,
        'acfvideoname'         => format_string($acfvideo->name),
        'acfvideoidnumber'     => $cm->idnumber,
    );

    if (isloggedin()) {
        $values['userid']          = $USER->id;
        $values['userusername']    = $USER->username;
        $values['useridnumber']    = $USER->idnumber;
        $values['userfirstname']   = $USER->firstname;
        $values['userlastname']    = $USER->lastname;
        $values['userfullname']    = fullname($USER);
        $values['useremail']       = $USER->email;
        $values['usericq']         = $USER->icq;
        $values['userphone1']      = $USER->phone1;
        $values['userphone2']      = $USER->phone2;
        $values['userinstitution'] = $USER->institution;
        $values['userdepartment']  = $USER->department;
        $values['useraddress']     = $USER->address;
        $values['usercity']        = $USER->city;
        $values['usertimezone']    = get_user_timezone_offset();
        $values['useracfvideo']         = $USER->acfvideo;
    }

    // weak imitation of Single-Sign-On, for backwards compatibility only
    // NOTE: login hack is not included in 2.0 any more, new contrib auth plugin
    //       needs to be createed if somebody needs the old functionality!
    if (!empty($config->secretphrase)) {
        $values['encryptedcode'] = acfvideo_get_encrypted_parameter($acfvideo, $config);
    }

    //hmm, this is pretty fragile and slow, why do we need it here??
    if ($config->rolesinparams) {
        $roles = get_all_roles();
        $coursecontext = get_context_instance(CONTEXT_COURSE, $course->id);
        $roles = role_fix_names($roles, $coursecontext, ROLENAME_ALIAS);
        foreach ($roles as $role) {
            $values['course'.$role->shortname] = $role->localname;
        }
    }

    return $values;
}

/**
 * BC internal function
 * @param object $acfvideo
 * @param object $config
 * @return string
 */
function acfvideo_get_encrypted_parameter($acfvideo, $config) {
    global $CFG;

    if (file_exists("$CFG->dirroot/local/externserverfile.php")) {
        require_once("$CFG->dirroot/local/externserverfile.php");
        if (function_exists('extern_server_file')) {
            return extern_server_file($acfvideo, $config);
        }
    }
    return md5(getremoteaddr().$config->secretphrase);
}

/**
 * Optimised mimetype detection from general acfvideo
 * @param $fullacfvideo
 * @return string mimetype
 */
function acfvideo_guess_icon($fullacfvideo) {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    if (substr_count($fullacfvideo, '/') < 3 or substr($fullacfvideo, -1) === '/') {
        // most probably default directory - index.php, index.html, etc.
        return 'f/web';
    }

    $icon = mimeinfo('icon', $fullacfvideo);
    $icon = 'f/'.str_replace(array('.gif', '.png'), '', $icon);

    if ($icon === 'f/html' or $icon === 'f/unknown') {
        $icon = 'f/web';
    }

    return $icon;
}
