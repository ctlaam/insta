<?php
require("./instagram.class.php");

$instaApi = new Instagram();

$instaApi->sessionId = "39408242373%3ABfpl5LU4CozMRT%3A11%3AAYfExUBLs4fGNX6TXX6H6OSc94VewXp2fUVf20L3nA";

$instaApi->proxy = null;

switch ($_GET["type"]) {

    case "avatar":

        return json_encode($instaApi->downloadAvatar($_GET["url"]));

        break;

    case "post":

        return json_encode($instaApi->downloadPost($_GET["url"]));

        break;

    case "reels":

        return json_encode($instaApi->downloadPost($_GET["url"]));

        break;

    case "stories":

        return json_encode($instaApi->downloadStories($_GET["url"]));
        
        break;

    case "highlight":

        return json_encode($instaApi->downloadHighlightStories($_GET["url"]));
    
        break;

}

?>