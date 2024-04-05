<?php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Bark.php';

$databaseInstance = Database::getInstance();
$barkInstance = Bark::getInstance($databaseInstance);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset ($_POST['search']) && $_POST['search'] !== '') {

    $search = $_POST['search'];

    $filteredBarks = $barkInstance->parseMessage($search);

    $response = array();

    $parsedResults = [];

    if (!empty ($filteredBarks['hashtags_mentions'])) {
        foreach ($filteredBarks['hashtags_mentions'] as $key => $values) {
            if (!isset ($parsedResults['hashtags_mentions'][$key])) {
                $response['hashtags_mentions'][$key] = $values;
                $parsedResults['hashtags_mentions'][$key] = true;
            }
        }
    } else {
        $response['hashtags_mentions'] = array('error' => 'No hashtags or mentions found');
    }

    if (!empty ($filteredBarks['profiles'])) {
        foreach ($filteredBarks['profiles'] as $profile => $users) {
            if (!isset ($parsedResults['profiles'][$profile])) {
                $response['profiles'][$profile] = $users;
                $parsedResults['profiles'][$profile] = true;
            }
        }
    } else {
        $response['profiles'] = array('error' => 'No profiles found');
    }

    if (!empty ($filteredBarks['wholeMessage'])) {
        $response['wholeMessage'] = $filteredBarks['wholeMessage'];
    }

    if (!empty ($filteredBarks['followingTweets'])) {
        foreach ($filteredBarks['followingTweets'] as $mention => $tweets) {
            if (!isset ($parsedResults['followingTweets'][$mention])) {
                $response['followingTweets'][$mention] = $tweets;
                $parsedResults['followingTweets'][$mention] = true;
            }
        }
    }

    $response['message'] = 'Search results';
    $response['global'] = $_POST['search'];

    echo json_encode($response);

} else {
    echo json_encode(array('message' => 'Error during research. Please try again.'));
}
