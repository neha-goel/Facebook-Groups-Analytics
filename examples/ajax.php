<?php
require_once '../FbStats.php';
include_once 'config.php';

if (!session_id())
    session_start();

$fb = new FbStats($config);
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

$stats = array(
	'totalStatus',
);
		 
//get Group Feed
try {
	$groupFeed = $fb->getFeed($feedParams);
} catch (Exception $e) {

	echo '<div class="alert-message error">'.$e->getMessage().'</div>';
}
//print_r( $groupFeed);

foreach($stats as $stat) {
	$users = $fb->getTopUsers($groupFeed, $stat, 10, $dateRange);
	displayUsersTable($users, $stat);
	if($users){
		$message = "Top Posters of this week \n --------------------------------------- \n ";
		$i = 1;
		foreach ($users as $user) {	
			if( $user['totalStatus'] ){
				$message .= " \n ". $i++ .". @[".$user['id'].":0:".$user['name']."] ".$user['name']." with ". $user['totalStatus']. " posts ";
			}
		}
	}
}
try {
	if( isset($_POST['publish']) ) {
		$wall_post = $fb->wallPost($_POST['group'],$message);
		echo '<div class="success">Published</div>';
	}
} catch (Exception $e) {
	echo '<div class="alert-message error">'.$e->getMessage().'</div>';
}
		
function displayUsersTable($users, $column=null) {

    $table = '<table class="bordered-table">';
    foreach ($users as &$user) {
        foreach ($user as $stat => $value) {
            if (in_array($stat, array('id', 'name', $column)) === false) {
                unset($user[$stat]);
            }
        }
    }

    unset($user);

    foreach ($users as $user) {
        $table .= '<tr>';
        foreach ($user as $stat => $value) {
			$table .= ($stat === 'id') ? "<td><img src='https://graph.facebook.com/{$value}/picture' /></td>" : "<td>{$value}</td>";
        }
        $table .= '</tr>';
    }
    $table .= '</table>';

   echo "<div class='span10'>{$table}</div>";
}
?>