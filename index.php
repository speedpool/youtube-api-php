<?php

set_include_path('./library');

require_once 'Youtube.php';
require_once 'Request.php';

$config = include 'config/local.php';
$youtube = new Youtube($config);
$request = new Request();

$username = $request->getFromQuery('username');
$maxResults = $request->getFromQuery('max_results', 10);

$channelsCacheFile = 'cache/' . $username . '_channels.json';
$videoIdsCacheFile = 'cache/' . $username . '_video_ids.json';

if (file_exists($channelsCacheFile) && (time() - filemtime($channelsCacheFile) < 600)) {
    $channels = json_decode(file_get_contents($channelsCacheFile), true);
} else {

    $channels = $youtube->listChannels('contentDetails', array(
        'forUsername' => $username,
        'maxResults' => 1,
        'fields' => 'items/contentDetails/relatedPlaylists/uploads',
    ));

    file_put_contents($channelsCacheFile, json_encode($channels));

}

$uploadsListId = $channels['items'][0]['contentDetails']['relatedPlaylists']['uploads'];

if (file_exists($videoIdsCacheFile) && (time() - filemtime($videoIdsCacheFile) < 600)) {
    $videoIds = json_decode(file_get_contents($videoIdsCacheFile), true);
} else {

    $playlistItems = $youtube->listPlaylistItems('snippet', array(
        'playlistId' => $uploadsListId,
        'maxResults' => $maxResults,
        'fields' => 'items/snippet/resourceId/videoId',
    ));

    $videoIds = array();
    foreach ($playlistItems['items'] as $item) {
        $videoIds[]['videoId'] = $item['snippet']['resourceId']['videoId'];
    }

    file_put_contents($videoIdsCacheFile, json_encode($videoIds));

}

header('Content-Type: application/json');
echo json_encode($videoIds);

