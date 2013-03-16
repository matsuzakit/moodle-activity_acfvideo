<?php

/**
 * Strings for component 'acfvideo', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['clicktoopen'] = 'Click {$a} link to open resource.';
$string['configdisplayoptions'] = 'Select all options that should be available, existing settings are not modified. Hold CTRL key to select multiple fields.';
$string['configframesize'] = 'When a web page or an uploaded file is displayed within a frame, this value is the height (in pixels) of the top frame (which contains the navigation).';
$string['configrolesinparams'] = 'Enable if you want to include localized role names in list of available parameter variables.';
$string['configsecretphrase'] = 'This secret phrase is used to produce encrypted code value that can be sent to some servers as a parameter.  The encrypted code is produced by an md5 value of the current user IP address concatenated with your secret phrase. ie code = md5(IP.secretphrase). Please note that this is not reliable because IP address may change and is often shared by different computers.';
$string['contentheader'] = 'Content';
$string['displayoptions'] = 'Available display options';
$string['displayselect'] = 'Display';
$string['displayselect_help'] = 'This setting, together with the acfvideo file type and whether the browser allows embedding, determines how the acfvideo is displayed. Options may include:
* Embed - The acfvideo is displayed within the page below the navigation bar together with the acfvideo description and any blocks
* In pop-up - The acfvideo is displayed in a new browser window without menus or an address bar';
-$string['displayselectexplain'] = 'Choose display type, unfortunately not all types are suitable for all acfvideos.';

////////////////// e-learning
$string['externalacfvideo'] = 'External Acfvideo';
$string['externalacfvideotext'] = 'External Acfvideo';
$string['externalacfvideotext_help'] = 'URL of video file on Amazon Cloud Front

f.e. http://somevalue.cloudfront.net/avideofile.mp4';


$string['framesize'] = 'Frame height';
$string['chooseavariable'] = 'Choose a variable...';
$string['modulename'] = 'Acfvideo';
$string['modulenameplural'] = 'acfvideos';
$string['modulename_help'] = "The Afcvideo module enables a teacher to provide an Amazon Cloud Front video as a course resource. Amazon Cloud Front is a very cheap environment to supply the video contents and it can prohibit from direct link.
This module use your amazon key and secret key and make temporal value to access the private contents. 

The progress is below.

* Login to Amazon Cloud Front and upload your videos .
* Make the contents private .
* Copy this module under moodle/mod/ directory .
* Install from Moodle and give your amazon's two keys .
* Add the instance and give the video URL .";

$string['neverseen'] = 'Never seen';
$string['optionsheader'] = 'Options';
$string['page-mod-acfvideo-x'] = 'Any acfvideo module page';
$string['parameterinfo'] = 'parameter=variable';
$string['parametersheader'] = 'Parameters';
$string['pluginadministration'] = 'acfvideo module administration';
$string['pluginname'] = 'Acfvideo';

$string['videoheight'] = 'Video height (in pixels)';
$string['videoheightexplain'] = 'Specifies default video height.';

$string['videowidth'] = 'Video width (in pixels)';
$string['videowidthexplain'] = 'Specifies default video width.';

$string['popupheight'] = 'Popup height (in pixels)';
$string['popupheightexplain'] = 'Specifies default height of popup windows.';
$string['popupwidth'] = 'Popup width (in pixels)';
$string['popupwidthexplain'] = 'Specifies default width of popup windows.';
$string['printheading'] = 'Display acfvideo name';
$string['printheadingexplain'] = 'Display acfvideo name above content? Some display types may not display acfvideo name even if enabled.';
$string['printintro'] = 'Display acfvideo description';
$string['printintroexplain'] = 'Display acfvideo description bellow content? Some display types may not display description even if enabled.';
$string['rolesinparams'] = 'Include role names in parameters';
$string['serveracfvideo'] = 'Server acfvideo';
$string['acfvideo:view'] = 'View acfvideo';
/////////////////////////// e-learning
$string['key_pair_id'] = 'key pair id';
$string['private_key_filename'] = 'private key filename';
$string['configkey_pair_id'] = 'key pair id of Amazon f.e. APKXXXXXXXXXXXXXXXXX';
$string['configprivate_key_filename'] = ' private key filename of Amazon  on local OS f.e /myprivate/pk-XXXXXXXXXXXXXXXXXXXX.pem';




