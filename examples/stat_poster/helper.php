<?php
$stats = array(
 'totalStatus',
 //'totalLinks',
 //'totalStatusChars',
 //'didLike',
 'didComment',
 //'didCommentChars',
 'gotLikes',
 'gotComments',
 //'gotLikesOnComments',
 //'gotTags',
 'totalPictures',
);

$messages = array(
 'totalStatus'        => 'Top status updators',
 'totalLinks'         => 'Top link sharer',
 'totalStatusChars'   => 'Longest status message writer',
 'didLike'            => 'Top likers',
 'didComment'         => 'Top commentators',
 'didCommentChars'    => 'Longest comments writers',
 'gotLikes'           => 'Top liked for status messages',
 'gotComments'        => 'Top comments receivers',
 'gotLikesOnComments' => 'Top liked for comments',
 'gotTags'            => 'Top tagged person',
 'totalPictures'      => 'Top pictures uploaders',
);

$units = array(
 'totalStatus'        => 'posts',
 'totalLinks'         => 'links',
 'totalStatusChars'   => 'characters',
 'didLike'            => 'likes',
 'didComment'         => 'comments',
 'didCommentChars'    => 'characters',
 'gotLikes'           => 'likes',
 'gotComments'        => 'comments',
 'gotLikesOnComments' => 'likes',
 'gotTags'            => 'tags',
 'totalPictures'      => 'pictures',
);

$period = array(
 '-1 days'   => 'Yesterday',
 '-2 days'   => 'Last 2 days',
 '-1 weeks'  => 'Last week',
 '-2 weeks'  => 'Last 2 weeks',
 '-1 months' => 'Last month',
 '-2 months' => 'Last 2 months',
 '-3 months' => 'Last 3 months',
 '-6 months' => 'Last 6 months',
);

function getUsersTable($users, $column=null) {

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

   return $table;
}

function time_ago($diff){

    $day=60*60*24;
	$relative_days = round($diff/$day);
	if( $relative_days == 0 ){
		$relative_days = 'now';
	}else{
		$relative_days = '-'.$relative_days.' days';
	}
	return $relative_days;
}

function createMsgForWallPost($results, $titles, $units, $extra_params, $column=null){
	$message = '';
	if($results){
		$title = $titles[$column] ." from  ". date('d M Y',strtotime($extra_params['since'])). " to ". date('d M Y',strtotime($extra_params['until']));
		$message = " \n ---------------------------------------------------------------------- \n \n ";
		$i = 1;
		
		$link = "http://www.facebook.com/groups/";
		foreach ($results as $user) {	
			if( $user[$column] ){
				if( in_array($column, array( 'gotLikes','gotComments')) ){
					$ids = explode('_',$user['id']);
					$message .= $i++ .". ".str_replace('"','',$user['message'])." (".$link.$ids[0]."/permalink/".$ids[1].") :: by ".$user['name']." ". $user[$column]. " ".$units[$column]." \n\n ";
				}else{
					$message .= $i++ .". ". $user['name']." with ". $user[$column]. " ".$units[$column]." \n ";	
				}	
			}	
		}
		$message .= " \n ";
	}
	return array($title, $message);
}
?>