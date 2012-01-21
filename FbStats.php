<?php
/**
 * Facebook Statistics
 *
 * PHP version 5.3
 *
 * @category Class
 * @package  Facebook-Groups-Analytics
 * @author   Rakesh Tembhurne <rakesh@tembhurne.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://github.com/rakeshtembhurne/Facebook-Groups-Analytics
 */

require 'facebook/facebook.php';

/**
 * Facebook Statistics class
 *
 * @category Class
 * @package  Facebook-Groups-Analytics
 * @author   Rakesh Tembhurne <rakesh@tembhurne.com>
 * @license  http://www.gnu.org/licenses/gpl-3.0.html GPLv3
 * @link     https://github.com/rakeshtembhurne/Facebook-Groups-Analytics
 */
class FbStats
{

    /**
     * Variable used for instance of Facebook class
     *
     * @var object
     */
    public $facebook;

    /**
     * Variable used for facebook user's data
     *
     * @var array
     */
    public $user;

    /**
     * Variable used for storing access token required for api calls.
     *
     * @var string
     */
    public $accessToken;

    /**
     * Variable used for permissions required.
     *
     * @var type
     */
    public $permissions;

    /**
     * Variable used for url to which script will redirect after user is logged in.
     *
     * @var type
     */
    public $afterLoginUrl;


    /**
     * Constructor method
     *
     * This method is used to create the facebook instance, set other important
     * variables, log in and get needed Facebook permissions from user.
     *
     * @param array $params parameters
     *
     * @return void
     */
    public function __construct($params)
    {
        // Creating facebook instance.
        $this->facebook = $this->getFbInstance($params);

        // Set class variables.
        $this->permissions   = $params['permissionsArray'];
        $this->afterLoginUrl = $params['afterLoginUrl'];

        // Login and check for permissions.
        $this->logIn();
        $this->accessToken = $this->facebook->getAccessToken();
        $this->askPermissions();

    }//end __construct()


    /**
     * This static function gets Facebook class instance.
     *
     * @param array $params parameters containing facebook application details.
     *
     * @return object
     */
    public static function getFbInstance($params)
    {
        $fb = new Facebook(
            array(
             'appId'  => $params['appId'],
             'secret' => $params['secret'],
             'cookie' => true,
            )
        );

        return $fb;

    }//end getFbInstance()


    /**
     * Ask Permissions
     *
     * This method is used for getting user permissions from facebook and checking
     * if user has given necessary permissions to the application.
     *
     * @throws Exception
     * @return void
     */
    public function askPermissions()
    {
        // Gets user permissions.
        try {
            $permissionsList = $this->facebook->api(
                '/me/permissions',
                'GET',
                array('access_token' => $this->accessToken)
            );
        } catch (FacebookApiException $e) {
            $result = $e->getResult();
            throw new Exception($result['error']['message']);
        }

        // If any permission is not granted, redirects for getting permissions.
        $permissionsNeeded = $this->permissions;
        foreach ($permissionsNeeded as $perm) {
            if (isset($permissionsList['data'][0][$perm]) === false
                || $permissionsList['data'][0][$perm] !== 1
            ) {
                $loginUrlParams = array(
                                   'scope'     => implode(',', $this->permissions),
                                   'fbconnect' => 1,
                                   'display'   => 'page',
                                   'next'      => $this->afterLoginUrl,
                                  );
                $loginUrl       = $this->facebook->getLoginUrl($loginUrlParams);
                header("Location: {$loginUrl}");
                exit();
            }
        }//end foreach

    }//end askPermissions()


    /**
     * Logs user in to facebook.
     *
     * @return void
     */
    public function logIn()
    {
        $this->user = $this->facebook->getUser();

        if ($this->user === 0) {
            $loginUrlParams = array(
                               'req_perms' => explode(',', $this->permissions),
                               'fbconnect' => 1,
                               'display'   => 'page',
                               'next'      => $this->afterLoginUrl,
                              );
            $loginUrl       = $this->facebook->getLoginUrl($loginUrlParams);

            // Redirect to the login URL on facebook.
            header("Location: {$loginUrl}");
            exit();
        }

    }//end logIn()


    /**
     * Get Feed - calls Facebook api and get feed.
     *
     * @param array $params array of source_id, limit, offset, until, since
     *
     * @throws Exception
     * @return array
     */
    public function getFeed($params)
    {
        if (empty($params) === false) {
            // Source id.
            if (isset($params['sourceId']) === true) {
                $sourceId = $params['sourceId'];
                unset($params['sourceId']);
            } else {
                $sourceId = 'me';
            }

            // Limits number of feed.
            if (isset($params['limit']) === false) {
                $params['limit'] = 50;
            }

            // Offet.
            if (isset($params['offset']) === false) {
                $params['offset'] = 0;
            }

            // Time since feed needed.
            if (isset($params['since']) === false) {
                $params['since'] = 'yesterday';
            }

            // Time until feed needed.
            if (isset($params['until']) === false) {
                $params['until'] = 'now';
            }

            // Create url from paramenters.
            $urlParams = '?';
            foreach ($params as $key => $value) {
                $urlParams .= "{$key}={$value}&";
            }
        }//end if
		//echo $urlParams;
        $data = array();
        try {
            $data = $this->facebook->api(
                "/{$sourceId}/feed{$urlParams}",
                'GET',
                array('access_token' => $this->accessToken)
            );
        } catch (FacebookApiException $e) {
            $result = $e->getResult();
            throw new Exception($result['error']['message']);
        }
		//print_R($data);
        return $data;

    }//end getFeed()

	/**
     * Get Groups - calls Facebook api and get groups.
     * @throws Exception
     * @return array
     */
    public function getGroups()
    {
		$data = array();
        try {
            $data = $this->facebook->api(
                "/me/groups",
                'GET',
                array('access_token' => $this->accessToken)
            );
        } catch (FacebookApiException $e) {
            $result = $e->getResult();
            throw new Exception($result['error']['message']);
        }
        return $data;
	}//end getGroups()

	/**
     * Wall Post - calls Facebook api and post on groups wall.
     * @throws Exception
     * @return boolean
     */
    public function wallPost($sourceId, $message)
    {
		$posted = 0;
		
		$wall_post =  array(
			'access_token' => $this->accessToken, 
			'message' => $message,
        );
        try {
            $data = $this->facebook->api(
                "/{$sourceId}/feed",
                'POST',
                $wall_post
            );
			$posted = 1;
        } catch (FacebookApiException $e) {
            $result = $e->getResult();
            throw new Exception($result['error']['message']);
        }
		return $posted;
	}//end wallPost()
	
    /**
     * Get Info
     *
     * Gets information by facebook api call. It should work with user, group, or
     * page if valid id is passed.
     *
     * @param type $id id
     *
     * @throws Exception
     * @return array
     */
    public function getInfo($id)
    {
        $data = array();
        try {
            $data = $this->facebook->api("/{$id}");
        } catch (FacebookApiException $e) {
            $result = $e->getResult();
            throw new Exception($result['error']['message']);
        }
        return $data;
    }//end getInfo()


    /**
     * Get feed users - extracts array of users with statistics from facebook feed.
     *
     * @param type $feed facebook feed
     *
     * @return array
     */
    public function getFeedUsers($feed, $dateRange=null)
    {
        $userData = array(
						'id'                 => null,
						'name'               => null,
						'totalStatus'        => 0,
						'totalStatusChars'   => 0,
						'totalLinks'         => 0,
						'totalPictures'      => 0,
						'didLike'            => 0,
						'didComment'         => 0,
						'didCommentChars'    => 0,
						'gotLikesOnComments' => 0,
                        'gotTags'            => 0,
					);
        $postData = array(
						'from_id'      		 => null,
						'name'               => null,
						'gotLikes'           => 0,
						'gotComments'        => 0,
						);
	
        foreach ($feed['data'] as $entry) {
            if (isset($entry['from']) === true) {
                if (isset($users[$entry['from']['id']]) === false) {
					$users[$entry['from']['id']] = $userData;
                    // Set user's name.
					$users[$entry['from']['id']]['name'] = $entry['from']['name'];
                    // Set user's id.
					$users[$entry['from']['id']]['id'] = $entry['from']['id'];
                }
				$posts[$entry['id']] = $postData;
				$posts[$entry['id']]['id'] = $entry['id'];
				$posts[$entry['id']]['name'] = $entry['from']['name'];
				$posts[$entry['id']]['from_id'] = $entry['from']['id'];
            }
			if ( $dateRange ){
				if ( strtotime($entry['created_time']) >= $dateRange['since'] && strtotime($entry['created_time']) <= $dateRange['until'] ) {
					// Status messages.
					if ($entry['type'] === 'status') {
						// Add Status count.	
						$users[$entry['from']['id']]['totalStatus']++;
						// Add total characters of status update.
						if (isset($entry['message']) === true) {
							// FIXME: non-english characters make above condition false.
							$users[$entry['from']['id']]['totalStatusChars']
								+= strlen($entry['message']);
							$message = explode(" ", $entry['message']);
							$message_part = implode(" ", array_splice($message, 0, 5));
							$posts[$entry['id']]['message'] = $message_part."..";
						}
					}
				
					// Links.
					if ($entry['type'] === 'link') {
						// Add Status count.
						$users[$entry['from']['id']]['totalStatus']++;
						// Add Link count.
						$users[$entry['from']['id']]['totalLinks']++;
						$message = explode(" ", $entry['message']);
						$message_part = implode(" ", array_splice($message, 0, 5));
						$posts[$entry['id']]['message'] = $message_part."..";
					}
					
					if ($entry['type'] === 'photo') {
						// Add Status count.
						$users[$entry['from']['id']]['totalStatus']++;
						// Add Picture count.
						$users[$entry['from']['id']]['totalPictures']++;
						$message = explode(" ", $entry['message']);
						$message_part = implode(" ", array_splice($message, 0, 5));
						$posts[$entry['id']]['message'] = $message_part."..";
					}

					// Likes.
					if (isset($entry['likes']) === true) {
						if (isset($entry['likes']['data']) === true) {
							//$users[$entry['from']['id']]['gotLikes']
								//+= count($entry['likes']['data']);
							// Loop through likers.
							foreach ($entry['likes']['data'] as $like) {
								// If user is not in the list, add.
								if (isset($users[$like['id']]) === false) {
									$users[$like['id']] = $userData;
									// Set user's name.
									$users[$like['id']]['name'] = $like['name'];
									// Set user's id.
									$users[$like['id']]['id'] = $like['id'];
								}//end if

								// Increase didComment.
								$users[$like['id']]['didLike']++;
							}//end foreach
						}
						if (isset($entry['likes']['count']) === true) {
							$posts[$entry['id']]['gotLikes']
								= (int) $entry['likes']['count'];
						}//end if
					}//end if

					if (isset($entry['comments']) === true) {
						if (isset($entry['comments']['data']) === true) {
							// Increase comment counter.
							//$users[$entry['from']['id']]['gotComments']
								//+= count($entry['comments']['data']);
							// Loop through each comment.
							foreach ($entry['comments']['data'] as $comment) {
								// If commentator is not in the list, add.
								if (isset($users[$comment['from']['id']]) === false) {
									$users[$comment['from']['id']] = $userData;
									// Set user's name.
									$users[$comment['from']['id']]['name']
										= $comment['from']['name'];
									// Set user's id.
									$users[$comment['from']['id']]['id']
										= $comment['from']['id'];
								}

								// Increase didComment for this user.
								$users[$comment['from']['id']]['didComment']++;
								// Increase didCommentChars.
								$users[$comment['from']['id']]['didCommentChars'] +=
									strlen($comment['message']);
								// Got likes on comments?
								if (isset($comment['likes']) === true) {
									$users[$comment['from']['id']]['gotLikesOnComments'] +=
										(int) $comment['likes'];
								}

								// Are people tagged in this message?
								if (isset($comment['message_tags']) === true) {
									foreach ($comment['message_tags'] as $tag) {
										// Check if user exists, otherwise add.
										if (isset($users[$tag['id']]) === false) {
											$users[$tag['id']] = $userData;
											// Set user's name.
											$users[$tag['id']]['name'] = $tag['name'];
											// Set user's id.
											$users[$tag['id']]['id'] = $tag['id'];
										}
									}//end foreach

									$users[$tag['id']]['gotTags']++;
								}
							}//end foreach
						}
						if (isset($entry['comments']['count']) === true) {
							$posts[$entry['id']]['gotComments'] =
								(int) $entry['comments']['count'];
						}//end if
					}//end if
				}//end if
			}
        }//end foreach
        return array ($users, $posts);
    }//end getFeedUsers()
	
    /**
     * Get Top Users functions extracts userf from given feed
     *
     * @param array  $feed     facebook feed
     * @param string $statName statistic name to sort with
     * @param int    $count    number of users to return
     *
     * @return type
     */
    public function getTopUsers($feed, $statName, $count=null, $dateRange=null)
    {
        ${$statName} = array();
        $name        = array();
        $sort_array = array();

        list ($users, $posts) = $this->getFeedUsers($feed, $dateRange);
		if( in_array($statName , array('totalStatus','totalPictures')) ){
			$sort_array = $users;
		}else{
			$sort_array = $posts;
		}
		
		foreach ($sort_array as $key => $value) {
			${$statName}[$key] = $value[$statName];
			$name[$key]        = $value['name'];
        }
		
        array_multisort(${$statName}, SORT_DESC, SORT_NUMERIC, $name, $sort_array);

        if ($count !== null && count($sort_array) > $count) {
            $sort_array = array_slice($sort_array, 0, $count);
        }
        return $sort_array;
    }//end getTopUsers()
}//end class
?>