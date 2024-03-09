<?php
header('Content-Type: application/json; charset=utf-8');
require("./instagram.class.php");

$instaApi = new Instagram();

$instaApi->sessionId = "39408242373%3ABfpl5LU4CozMRT%3A11%3AAYfXm2lyFZj3YIto8RMXu1zbm8greyAmX08SYA0Uwg;";

$instaApi->proxy = "207.228.3.97:46889@eo4iTcxrgWzYt0q:33EhWsSqbseKZZW";


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