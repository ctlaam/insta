<?php
header('Content-Type: application/json; charset=utf-8');
require("./instagram.class.php");

$instaApi = new Instagram();

$instaApi->sessionId = "";

$instaApi->proxy = null;


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