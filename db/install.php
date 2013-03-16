<?php

/**
 * Post installation and migration code.
 *
 * This file replaces:
 *   - STATEMENTS section in db/install.xml
 *   - lib.php/modulename_install() post installation hook
 *   - partially defaults.php
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

function xmldb_acfvideo_install() {
    global $CFG;

    // migrate settings if present
    if (!empty($CFG->resource_secretphrase)) {
        set_config('secretphrase', $CFG->resource_secretphrase, 'acfvideo');
    }
    unset_config('resource_secretphrase');

    // Upgrade from old resource module type if needed
    require_once("$CFG->dirroot/mod/acfvideo/db/upgradelib.php");
    acfvideo_20_migrate();
}

function xmldb_acfvideo_install_recovery() {
    global $CFG;

    // Upgrade from old resource module type if needed
    require_once("$CFG->dirroot/mod/acfvideo/db/upgradelib.php");
    acfvideo_20_migrate();
}
