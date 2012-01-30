<?php
require_once '../../FbStats.php';
include_once '../config.php';
include_once 'helper.php';

if (!session_id())
    session_start();

// Create Facebook Instance
$fb = new FbStats($config);
if( isset($_POST['publish']) ) {
	try {
		$wall_post = $fb->wallPost($_POST['group'],$_POST['message']);
		if( $wall_post) {
			echo '<div class="success">Published</div>';
		}else{
			echo '<div class="alert-message error">Error in publishing result. Please try again.</div>';
		}
	} catch (Exception $e) {
		echo '<div class="alert-message error">'.$e->getMessage().'</div>';
	}
}else{
	// Get submitted data
	$since      = !empty($_POST['from']) ? time_ago(time()-strtotime(urldecode($_POST['from']))) : 'yesterday';
	$until      = !empty($_POST['to']) ? time_ago(time()-strtotime(urldecode($_POST['to']))) : 'now';
	$stat       = $_POST['stat'];
	$usersCount = !empty($_POST['usersCount']) ? $_POST['usersCount'] : 10;
	$selfComments = isset($_POST['selfComments']) ? $_POST['selfComments'] : 0;
	$ignoreLinks = isset($_POST['ignoreLinks']) ? $_POST['ignoreLinks'] : 0;
	$ignorePhotos = isset($_POST['ignorePhotos']) ? $_POST['ignorePhotos'] : 0;
	$minLines = isset($_POST['minLines']) && !empty($_POST['minLines']) ? $_POST['minLines'] : 0;
	$minWords = isset($_POST['minWords']) && !empty($_POST['minWords']) ? $_POST['minWords'] : 0;
	$copy_result = isset($_POST['copy_result']) ? $_POST['copy_result'] : "";
	$extra_params  = array(
		'since' => $since,
		'until' => $until,
		'selfComments' => $selfComments,
		'minLines' => $minLines,
		'minWords' => $minWords,
		'ignoreLinks' => $ignoreLinks,
		'ignorePhotos' => $ignorePhotos,
		'copy_result' => $copy_result,
	);

	$feedParams = array(
		'sourceId' => $_POST['group'],
		'limit' => 500,
		'since' => urlencode($since),
		'until' => urlencode($until),
	);

	//get Group Feed
	try {
		$groupFeed = $fb->getFeed($feedParams);
	} catch (Exception $e) {
		echo '<div class="alert-message error">'.$e->getMessage().'</div>';
	}

	$results = $fb->getTopUsers($groupFeed, $stat, $usersCount, $extra_params);
	list($title, $auto_message) = createMsgForWallPost($results, $messages, $units, $extra_params, $stat);
	if($copy_result){
		echo '<textarea name="statResult" id="statResult" rows="18" class="extra_long">'.$title.$auto_message.'</textarea>';
	}else{
		echo "<strong>Title : </strong>".$title;
		echo '<label>Replace it by this title</label><br/>';
		echo '<input type="text" name="title" id="title" value="" class="long" />';
		echo "<br/><strong>Auto Message : </strong><br/>";
		echo nl2br(strip_tags($auto_message,'<br>'));
		echo '<label>Additional Message</label><br/>';
		echo '<textarea name="additional_message" id="additional_message" rows="8" class="long"></textarea>';
		echo '<input type="hidden" id="auto_title" value="'.$title.'" />';
		echo '<input type="hidden" id="auto_message" value="'.$auto_message.'" />';
		echo '<br/><br/><input type="submit" name="publish_result" id="publish_result" value="Publish Result" class="btn primary" />';
	}
}		
?>