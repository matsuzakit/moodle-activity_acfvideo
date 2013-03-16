<?php

/**
 * acfvideo configuration form
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/acfvideo/locallib.php');

class mod_acfvideo_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $displayoptions = resourcelib_get_displayoptions(array(
        		RESOURCELIB_DISPLAY_EMBED,
        		RESOURCELIB_DISPLAY_POPUP,
        ));
        $mform = $this->_form;

        $config = get_config('acfvideo');

        //General-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //Content-e-learning--------------------------------------------        
        $mform->addElement('header', 'content', get_string('contentheader', 'acfvideo'));
        $mform->addElement('url', 'externalacfvideo', get_string('externalacfvideo', 'acfvideo'), 
        		array('size'=>'60'), array('usefilepicker'=>false));
        $mform->addHelpButton('externalacfvideo', 'externalacfvideotext', 'acfvideo');        
        $mform->addRule('externalacfvideo', null, 'required', null, 'client');
        //Options-------------------------------------------------------
        $mform->addElement('header', 'optionssection', get_string('optionsheader', 'acfvideo'));

        $mform->addElement('text', 'videowidth', get_string('videowidth', 'acfvideo'), array('size'=>3));
        $mform->setType('videowidth', PARAM_INT);
        $mform->setDefault('videowidth', $config->videowidth);
        $mform->setAdvanced('videowidth', $config->videowidth_adv);
        
        $mform->addElement('text', 'videoheight', get_string('videoheight', 'acfvideo'), array('size'=>3));
        $mform->setType('videoheight', PARAM_INT);
        $mform->setDefault('videoheight', $config->videoheight);
        $mform->setAdvanced('videoheight', $config->videoheight_adv);
        
        
        
        
        $options = $displayoptions;
        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'display', get_string('displayselect', 'acfvideo'), $options);
            $mform->addHelpButton('display', 'displayselect', 'acfvideo');
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_POPUP, $options)) {
            $mform->addElement('text', 'popupwidth', get_string('popupwidth', 'acfvideo'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupwidth', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupwidth', PARAM_INT);
            $mform->setDefault('popupwidth', $config->popupwidth);
            $mform->setAdvanced('popupwidth', $config->popupwidth_adv);

            $mform->addElement('text', 'popupheight', get_string('popupheight', 'acfvideo'), array('size'=>3));
            if (count($options) > 1) {
                $mform->disabledIf('popupheight', 'display', 'noteq', RESOURCELIB_DISPLAY_POPUP);
            }
            $mform->setType('popupheight', PARAM_INT);
            $mform->setDefault('popupheight', $config->popupheight);
            $mform->setAdvanced('popupheight', $config->popupheight_adv);
        }

        if (array_key_exists(RESOURCELIB_DISPLAY_AUTO, $options) or
          array_key_exists(RESOURCELIB_DISPLAY_EMBED, $options) or
          array_key_exists(RESOURCELIB_DISPLAY_FRAME, $options)) {
            $mform->addElement('checkbox', 'printheading', get_string('printheading', 'acfvideo'));
            $mform->disabledIf('printheading', 'display', 'eq', RESOURCELIB_DISPLAY_POPUP);
            $mform->disabledIf('printheading', 'display', 'eq', RESOURCELIB_DISPLAY_OPEN);
            $mform->disabledIf('printheading', 'display', 'eq', RESOURCELIB_DISPLAY_NEW);
            $mform->setDefault('printheading', $config->printheading);
            $mform->setAdvanced('printheading', $config->printheading_adv);

            $mform->addElement('checkbox', 'printintro', get_string('printintro', 'acfvideo'));
            $mform->disabledIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_POPUP);
            $mform->disabledIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_OPEN);
            $mform->disabledIf('printintro', 'display', 'eq', RESOURCELIB_DISPLAY_NEW);
            $mform->setDefault('printintro', $config->printintro);
            $mform->setAdvanced('printintro', $config->printintro_adv);
        }

        //-------------------------------------------------------
        $mform->addElement('header', 'parameterssection', get_string('parametersheader', 'acfvideo'));


        if (empty($this->current->parameters)) {
            $parcount = 5;
        } else {
            $parcount = 5 + count(unserialize($this->current->parameters));
            $parcount = ($parcount > 100) ? 100 : $parcount;
        }
        $options = acfvideo_get_variable_options($config);

        for ($i=0; $i < $parcount; $i++) {
            $parameter = "parameter_$i";
            $variable  = "variable_$i";
            $pargroup = "pargoup_$i";
            $group = array(
                $mform->createElement('text', $parameter, '', array('size'=>'12')),
                $mform->createElement('selectgroups', $variable, '', $options),
            );
            $mform->addGroup($group, $pargroup, get_string('parameterinfo', 'acfvideo'), ' ', false);
            $mform->setAdvanced($pargroup);
        }

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values) {
        if (!empty($default_values['displayoptions'])) {
            $displayoptions = unserialize($default_values['displayoptions']);
            if (isset($displayoptions['printintro'])) {
                $default_values['printintro'] = $displayoptions['printintro'];
            }
            if (isset($displayoptions['printheading'])) {
                $default_values['printheading'] = $displayoptions['printheading'];
            }
			/////////////////////////////////////////
            if (!empty($displayoptions['videowidth'])) {
            	$default_values['videowidth'] = $displayoptions['videowidth'];
            }
            if (!empty($displayoptions['videoheight'])) {
            	$default_values['videoheight'] = $displayoptions['videoheight'];
            }
            /////////////////////////////////////////
            
            
           if (!empty($displayoptions['popupwidth'])) {
                $default_values['popupwidth'] = $displayoptions['popupwidth'];
            }
            if (!empty($displayoptions['popupheight'])) {
                $default_values['popupheight'] = $displayoptions['popupheight'];
            }
        }
        if (!empty($default_values['parameters'])) {
            $parameters = unserialize($default_values['parameters']);
            $i = 0;
            foreach ($parameters as $parameter=>$variable) {
                $default_values['parameter_'.$i] = $parameter;
                $default_values['variable_'.$i]  = $variable;
                $i++;
            }
        }
    }

}
