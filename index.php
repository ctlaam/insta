<?php
header('Content-Type: application/json; charset=utf-8');
require("./instagram.class.php");

$instaApi = new Instagram();

$instaApi->sessionId = "39408242373%3ALh57nkf0S6hU7f%3A14%3AAYd18dtdszRGvG8gFZm2ytAT_qJzvfIQkaZYzdvpdg";

$instaApi->proxy = null;

switch ($_GET["type"]) {

    case "avatar":

        echo json_encode($instaApi->downloadAvatar($_GET["url"]));

        break;

    case "post":

        echo json_encode($instaApi->downloadPost($_GET["url"]));

        break;

    case "reels":

        echo json_encode($instaApi->downloadPost($_GET["url"]));

        break;

    case "stories":

        echo json_encode($instaApi->downloadStories($_GET["url"]));
        
        break;

    case "highlight":

        echo json_encode($instaApi->downloadHighlightStories($_GET["url"]));
    
        break;

}

?>