<?php

/**
 * acfvideo module admin settings and defaults
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    require_once("$CFG->libdir/resourcelib.php");
    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('acfvideo/framesize',
        get_string('framesize', 'acfvideo'), get_string('configframesize', 'acfvideo'), 130, PARAM_INT));
    $settings->add(new admin_setting_configcheckbox('acfvideo/requiremodintro',
        get_string('requiremodintro', 'admin'), get_string('configrequiremodintro', 'admin'), 1));
    $settings->add(new admin_setting_configtext('acfvideo/key_pair_id',
    		get_string('key_pair_id', 'acfvideo'), get_string('configkey_pair_id', 'acfvideo'), '', PARAM_TEXT, 30));
    $settings->add(new admin_setting_configtext('acfvideo/private_key_filename',
    		get_string('private_key_filename', 'acfvideo'), get_string('configprivate_key_filename', 'acfvideo'), '', PARAM_TEXT,50));
    $settings->add(new admin_setting_configcheckbox('acfvideo/rolesinparams',
        get_string('rolesinparams', 'acfvideo'), get_string('configrolesinparams', 'acfvideo'), false));
    //--- modedit defaults -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_heading('acfvideomodeditdefaults', get_string('modeditdefaults', 'admin'), get_string('condifmodeditdefaults', 'admin')));

    $settings->add(new admin_setting_configcheckbox_with_advanced('acfvideo/printheading',
        get_string('printheading', 'acfvideo'), get_string('printheadingexplain', 'acfvideo'),
        array('value'=>0, 'adv'=>false)));
    $settings->add(new admin_setting_configcheckbox_with_advanced('acfvideo/printintro',
        get_string('printintro', 'acfvideo'), get_string('printintroexplain', 'acfvideo'),
        array('value'=>1, 'adv'=>false)));

    $settings->add(new admin_setting_configtext_with_advanced('acfvideo/videowidth',
    		get_string('videowidth', 'acfvideo'), get_string('videowidthexplain', 'acfvideo'),
    		array('value'=>640, 'adv'=>true), PARAM_INT, 7));
    $settings->add(new admin_setting_configtext_with_advanced('acfvideo/videoheight',
    		get_string('videoheight', 'acfvideo'), get_string('videoheightexplain', 'acfvideo'),
    		array('value'=>480, 'adv'=>true), PARAM_INT, 7));
     
     $settings->add(new admin_setting_configtext_with_advanced('acfvideo/popupwidth',
        get_string('popupwidth', 'acfvideo'), get_string('popupwidthexplain', 'acfvideo'),
        array('value'=>660, 'adv'=>true), PARAM_INT, 7));
    $settings->add(new admin_setting_configtext_with_advanced('acfvideo/popupheight',
        get_string('popupheight', 'acfvideo'), get_string('popupheightexplain', 'acfvideo'),
        array('value'=>500, 'adv'=>true), PARAM_INT, 7));
}
