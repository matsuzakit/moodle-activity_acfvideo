<?php

/**
 * Definition of log events
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2010 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$logs = array(
    array('module'=>'acfvideo', 'action'=>'view', 'mtable'=>'acfvideo', 'field'=>'name'),
    array('module'=>'acfvideo', 'action'=>'view all', 'mtable'=>'acfvideo', 'field'=>'name'),
    array('module'=>'acfvideo', 'action'=>'update', 'mtable'=>'acfvideo', 'field'=>'name'),
    array('module'=>'acfvideo', 'action'=>'add', 'mtable'=>'acfvideo', 'field'=>'name'),
);