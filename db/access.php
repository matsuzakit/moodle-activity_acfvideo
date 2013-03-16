<?php

/**
 * Capability definitions for the acfvideo module.
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$capabilities = array(
    'mod/acfvideo:view' => array(
    		
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'guest' => CAP_ALLOW,
            'user' => CAP_ALLOW,
        )
    ),
		'mod/acfvideo:addinstance' => array(
				'riskbitmask' => RISK_XSS,
		
				'captype' => 'write',
				'contextlevel' => CONTEXT_COURSE,
				'archetypes' => array(
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW
				),
				'clonepermissionsfrom' => 'moodle/course:manageactivities'
		),
);

