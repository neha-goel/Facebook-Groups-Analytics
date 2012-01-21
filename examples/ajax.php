<?php
require_once '../FbStats.php';
include_once 'config.php';

if (!session_id())
    session_start();

$fb = new FbStats($config);
if( isset($_POST['publish']) ) {
	try {	
			$wall_post = $fb->wallPost($_POST['group'],$_POST['message']);
			echo '<div class="success">Published</div>';
	
	} catch (Exception $e) {
		echo '<div class="alert-message error">'.$e->getMessage().'</div>';
	}
}else{
	$since = 'last Monday';
	$until = 'now';
	
	$feedParams = array(
		'sourceId' => $_POST['group'],
		'limit' => 500,
		'since' => urlencode($since),
		'until' => urlencode($until),
	);

	$dateRange = array(
		'since' => strtotime($since),
		'until' => strtotime($until),
	);

	$stat = $_POST['stats'] ;

	$titles = array(
			'totalStatus' => "Top Posters",
			'gotLikes' => "Most Liked Posts",
			'gotComments' => "Most Commented Posts",
			'totalPictures' => "Top Pictures Uploader",
		);
		
	//get Group Feed
	try {
		$groupFeed = $fb->getFeed($feedParams);
	} catch (Exception $e) {
		echo '<div class="alert-message error">'.$e->getMessage().'</div>';
	}

	$results = $fb->getTopUsers($groupFeed, $stat, 10, $dateRange);
	echo '<label>Title</label><br/>';
	echo '<input type="text" name="title" id="title" value="" size="50" />';
	list($title, $auto_message) = createMsgForWallPost($results, $titles, $dateRange, $stat);
	echo "<br/>".$title.nl2br(strip_tags($auto_message,'<br>'));
	echo '<label>Message</label><br/>';
	echo '<textarea name="custom_message" id="custom_message" cols="60" rows="5"></textarea>';
	echo '<input type="hidden" id="auto_title" value="'.$title.'" />';
	echo '<input type="hidden" id="automessage" value="'.$auto_message.'" />';
	echo '<br/><br/><input type="submit" name="publish_result" id="publish_result" value="Publish Result" />';
}

function createMsgForWallPost($results, $titles, $dateRange, $column=null){
	$message = '';
	if($results){
		$title = $titles[$column] ." from  ". date('d-m-Y',$dateRange['since']). " to ". date('d-m-Y',$dateRange['until']);
		$message = " \n --------------------------------------- \n \n ";
		$i = 1;
		
		$link = "http://www.facebook.com/groups/";
		foreach ($results as $user) {	
			if( $user[$column] ){
				if( $column == 'totalStatus' ){
					$message .= $i++ .". ".$user['name']." with ". $user['totalStatus']. " posts \n ";
				}elseif( $column == 'gotLikes' ){
					$ids = explode('_',$user['id']);
					$message .= $i++ .". ".$user['message']." (".$link.$ids[0]."/permalink/".$ids[1].") :: by ".$user['name']." ". $user['gotLikes']. " likes \n\n ";
				}elseif( $column == 'gotComments' ){
					$ids = explode('_',$user['id']);
					$message .= $i++ .". ".$user['message']." (".$link.$ids[0]."/permalink/".$ids[1].") :: by ".$user['name']." ". $user['gotComments']. " comments \n\n ";
				}elseif( $column == 'totalPictures' ){
					$message .= $i++ .". ".$user['name']." with ". $user['totalStatus']. " posts \n ";
				}			
			}	
		}
		
		$message .= " \n ";
	}
	return array($title, $message);
}
?>