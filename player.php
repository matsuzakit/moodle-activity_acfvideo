<?php 
/**
 * player of acfvideo module
 *
 * @package    mod
 * @subpackage acfvideo
 * @copyright  2012 e-learning co.,ltd.  {@link http://www.e-learning.co.jp}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$dst = $_GET["dst"];
$Signature = $_GET["Signature"];
$KeyPairId = $_GET["Key-Pair-Id"];
$videourl = $dst.'&Signature='.$Signature.'&Key-Pair-Id='.$KeyPairId;
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8" />
  <title>e-learning Video Player</title>

  <link href="player/video-js.min.css" rel="stylesheet" type="text/css">
  <script src="player/video.min.js"></script>
  <script>
    _V_.options.flash.swf = "player/video-js.swf";
  </script>
</head>
<body>
	<video id="el_video" class="video-js vjs-default-skin" controls preload="none" width="640" height="480"      
      data-setup="{}">
    <source src=<?php echo $videourl ?> type='video/mp4' />
    </video>
    <script type="text/javascript" > _V_("el_video").volume(0.2);</script>
    <hr/>
  </body>
</html>
