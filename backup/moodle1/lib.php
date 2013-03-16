<?php

/**
 * Provides support for the conversion of moodle1 backup to the moodle2 format
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2011 Andrew Davis <andrew@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * acfvideo conversion handler. This resource handler is called by moodle1_mod_resource_handler
 */
class moodle1_mod_acfvideo_handler extends moodle1_resource_successor_handler {

    /** @var moodle1_file_manager instance */
    protected $fileman = null;

    /**
     * Converts /MOODLE_BACKUP/COURSE/MODULES/MOD/RESOURCE data
     * Called by moodle1_mod_resource_handler::process_resource()
     */
    public function process_legacy_resource($data) {

        // get the course module id and context id
        $instanceid = $data['id'];
        $cminfo     = $this->get_cminfo($instanceid, 'resource');
        $moduleid   = $cminfo['id'];
        $contextid  = $this->converter->get_contextid(CONTEXT_MODULE, $moduleid);

        // prepare the new acfvideo instance record
        $acfvideo                 = array();
        $acfvideo['id']           = $data['id'];
        $acfvideo['name']         = $data['name'];
        $acfvideo['intro']        = $data['intro'];
        $acfvideo['introformat']  = $data['introformat'];
        $acfvideo['externalacfvideo']  = $data['reference'];
        $acfvideo['timemodified'] = $data['timemodified'];

        // populate display and displayoptions fields
        $options = array('printheading' => 0, 'printintro' => 1);
        if ($data['options'] == 'frame') {
            $acfvideo['display'] = RESOURCELIB_DISPLAY_FRAME;

        } else if ($data['options'] == 'objectframe') {
            $acfvideo['display'] = RESOURCELIB_DISPLAY_EMBED;

        } else if ($data['popup']) {
            $acfvideo['display'] = RESOURCELIB_DISPLAY_POPUP;
            $rawoptions = explode(',', $data['popup']);
            foreach ($rawoptions as $rawoption) {
                list($name, $value) = explode('=', trim($rawoption), 2);
                if ($value > 0 and ($name == 'width' or $name == 'height')) {
                    $options['popup'.$name] = $value;
                    continue;
                }
            }

        } else {
            $acfvideo['display'] = RESOURCELIB_DISPLAY_AUTO;
        }
        $acfvideo['displayoptions'] = serialize($options);

        // populate the parameters field
        $parameters = array();
        if ($data['alltext']) {
            $rawoptions = explode(',', $data['alltext']);
            foreach ($rawoptions as $rawoption) {
                list($variable, $parameter) = explode('=', trim($rawoption), 2);
                $parameters[$parameter] = $variable;
            }
        }
        $acfvideo['parameters'] = serialize($parameters);

        // convert course files embedded into the intro
        $this->fileman = $this->converter->get_file_manager($contextid, 'mod_acfvideo', 'intro');
        $acfvideo['intro'] = moodle1_converter::migrate_referenced_files($acfvideo['intro'], $this->fileman);

        // write acfvideo.xml
        $this->open_xml_writer("activities/acfvideo_{$moduleid}/acfvideo.xml");
        $this->xmlwriter->begin_tag('activity', array('id' => $instanceid, 'moduleid' => $moduleid,
            'modulename' => 'acfvideo', 'contextid' => $contextid));
        $this->write_xml('acfvideo', $acfvideo, array('/acfvideo/id'));
        $this->xmlwriter->end_tag('activity');
        $this->close_xml_writer();

        // write inforef.xml
        $this->open_xml_writer("activities/acfvideo_{$moduleid}/inforef.xml");
        $this->xmlwriter->begin_tag('inforef');
        $this->xmlwriter->begin_tag('fileref');
        foreach ($this->fileman->get_fileids() as $fileid) {
            $this->write_xml('file', array('id' => $fileid));
        }
        $this->xmlwriter->end_tag('fileref');
        $this->xmlwriter->end_tag('inforef');
        $this->close_xml_writer();
    }
}
