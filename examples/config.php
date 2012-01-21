<?php
$config = array(
    'appId' => '151139138249189',
    'secret' => 'c09a88c9093d2a58097c636a2337c2ca',
    'permissionsArray' => array(
        'publish_stream',
        'read_stream',
        'offline_access',
        'user_groups'
    ),
    'afterLoginUrl' => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
);

// Nagpur PHP User group
$sourceId = '248938471791448';
?>