<?php 

/**
 * API of acfvideo module
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define("USAGEHOUR", 1 * 60 * 60); //valid time of video hours * minutes * seconds
/**
 * Return whether ringht user or not
 * @param string $userid
 * @return bool isrightuser
 */

/**
 * show player
 * @param object $acfvideo
 * @param object $cm
 * @param object $course
 * @return does not return
 */
function acfvideo_showplayer($acfvideo, $cm, $course) {
	global $OUTPUT;
	acfvideo_print_header($acfvideo, $cm, $course);
	acfvideo_print_heading($acfvideo, $cm, $course, true);
	acfvideo_print_intro($acfvideo, $cm, $course, true);

	$fullacfvideo = acfvideo_get_full_acfvideo($acfvideo, $cm, $course);

	$display = acfvideo_get_final_display_type($acfvideo);
	if ($display == RESOURCELIB_DISPLAY_POPUP) {
		$options = empty($acfvideo->displayoptions) ? array() : unserialize($acfvideo->displayoptions);
		$width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
		$height = empty($options['popupheight']) ? 450 : $options['popupheight'];
		$wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
		$extra = "onclick=\"window.open('$fullacfvideo', '', '$wh'); return false;\"";
	} else {
		$extra = '';
	}

	echo '<div class="acfvideoworkaround">';
	print_string('clicktoopen', 'acfvideo', "<a href=\"$fullacfvideo\" $extra>$fullacfvideo</a>");
	echo '</div>';

	echo $OUTPUT->footer();
	die;
}
/**
 * Return amazon cloud front url with time limited parameters
 * @param string $video_path
 * @return string acfvideo
 */
function getcfurl( $video_path ){
	$config = get_config('acfvideo');
	$key_pair_id = $config->key_pair_id; 
	$private_key_filename = $config->private_key_filename;
	date_default_timezone_set('UTC'); //UTCで計算する
	$expires = time() + USAGEHOUR;
	$theurl = get_canned_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $expires);

	return $theurl;
}
/**
 * Return amazon cloud front signature
 * @param string $policy
 * @param string $private_key_filename
 * @return string signature
 */
function rsa_sha1_sign($policy, $private_key_filename) {
   $signature = "";

   // load the private key
   $fp = fopen($private_key_filename, "r");
   $priv_key = fread($fp, 8192);
   fclose($fp);
   $pkeyid = openssl_get_privatekey($priv_key);

   // compute signature
   openssl_sign($policy, $signature, $pkeyid);

   // free the key from memory
   openssl_free_key($pkeyid);

   return $signature;
 }
 /**
  * Return base64 encoded value
  * @param string $value
  * @return string encodedvalue
  */
function url_safe_base64_encode($value) {
    $encoded = base64_encode($value);
    // replace unsafe characters +, = and / with 
    // the safe characters -, _ and ~
    return str_replace(
        array('+', '=', '/'),
        array('-', '_', '~'),
        $encoded);
 }
 /**
  * Return canned policy stream name
  * @param string $video_path
  * @param string $private_key_filename
  * @param string $key_pair_id
  * @param string $expires
  * @return string canned policy stream name
  */
function get_canned_policy_stream_name($video_path, $private_key_filename, $key_pair_id, $expires) {
    // this policy is well known by CloudFront, but you still need to sign it, 
    // since it contains your parameters
    $canned_policy = '{"Statement":[{"Resource":"' . $video_path . '","Condition":{"DateLessThan":{"AWS:EpochTime":'. $expires . '}}}]}';
    // the policy contains characters that cannot be part of a URL, 
    // so we Base64 encode it
    $encoded_policy = url_safe_base64_encode($canned_policy);
    // sign the original policy, not the encoded version
    $signature = rsa_sha1_sign($canned_policy, $private_key_filename);
    // make the signature safe to be included in a url
    $encoded_signature = url_safe_base64_encode($signature);

    // combine the above into a stream name
    //$stream_name = create_stream_name($video_path, null, $encoded_signature, $key_pair_id, $expires);
    // url-encode the query string characters to work around a flash player bug
    //return encode_query_params($stream_name);
        return $video_path . 
            "?Expires=" . 
            $expires . 
            "&Signature=" .
            $encoded_signature .
            "&Key-Pair-Id=" .
            $key_pair_id;
}