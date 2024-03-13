<?php
header('Content-Type: application/json; charset=utf-8');
require("./instagram.class.php");

$proxies = json_decode(file_get_contents('./data/proxies.json'), true);

$instaApi = new Instagram();

$json_string = file_get_contents('./data/data.json');

// Decode JSON string into a PHP array
$data = json_decode($json_string, true);

// Get a random element from the array
$random_element = $data[array_rand($data)];

$instaApi->sessionId = $random_element;

$instaApi->proxy = array_rand($proxies);


switch ($_GET["type"]) {

    case "avatar":

        echo json_encode($instaApi->downloadAvatar($_GET["url"]));

        break;

    case "post":

        echo json_encode($instaApi->downloadPost($_GET["url"]));

        break;

    case "reels":

        echo json_encode($instaApi->downloadReels($_GET["url"]));

        break;

    case "stories":

        echo json_encode($instaApi->downloadStories($_GET["url"]));
        
        break;

    case "highlight":

        echo json_encode($instaApi->downloadHighlightStories($_GET["url"]));
    
        break;

}


?>