<?php

require_once 'session.php';
require_once 'queries.php';

$gid = $_GET['gid'];
$updatedTime = $_GET['updatedTime'];

// Get FB user id.
$uid = $fbSession->getUser();

$constraints = array();

// Create the constraints array.
$constraints[] = array(
    'field' => 'actor_id',
    'operator' => '=',
    'value' => $uid
);

// Allow everything younger than one month.
$oldestAllowed = strtotime('-1 month');

// Grab the initial batch and save the oldest post's updated time.
$posts = streamQuery($fbSession, $gid, $constraints, $updatedTime, 100);

if (count($posts) > 0) {
    $oldest = $posts[count($posts) - 1]['updated_time'];

    // Keep getting more posts until the old post is older than $oldestAllowed
    while ($oldest >= $oldestAllowed) {
        // Build the array of post objects.
        $batch = streamQuery($fbSession, $gid, $constraints, $oldest, 100);

        if (count($batch) > 0) {
            $oldest = $batch[count($batch) - 1]['updated_time'];

            // Add the batch to what posts we already have.
            $posts = array_merge($posts, $batch);
        } else {
            // Break the loop.
            // This assumes that when streamQuery returns 0 results that no older posts
            // matching the given criteria exist.
            $oldest = 0;
        }
    }
}

echo json_encode($posts);
