<?php
require "vendor/autoload.php";
use PHPHtmlParser\Dom;


class Instagram
{
    // property declaration
    public $proxy = null;

    public $sessionId;

    const INSTAGRAM_DOMAIN = 'instagram.com';

    // method declaration
    public function downloadAvatar($url) {

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {

            $url = "https://www.instagram.com/".str_replace("@", "", $url);

        }

        $url = $this->validateUrl($url);

        if (!$url) {
            return [
                "success" => false,
                "message" => "url/username không hợp lệ"
            ];
        }

        $username = $this->extractUsernameFromInstagramURL($url);



        $result = json_decode($this->curl("https://i.instagram.com/api/v1/users/web_profile_info/?username=".$username, "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Instagram 64.0.0.14.96",
         )));  



        

        if (!isset($result->data->user->id)) {
            return [
                "success" => false,
                "message" => "Không thể lấy info từ instagram"
            ];
        }

        $userId = $result->data->user->id;


        $queryApi = json_decode($this->curl("https://i.instagram.com/api/v1/users/".$userId."/info/", "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Instagram 64.0.0.14.96",
         )));    


        if (!isset($queryApi->user->hd_profile_pic_url_info->url)) {
            return [
                "success" => false,
                "message" => "Không thể lấy info từ instagram"
            ];
        }



        return [
            "success" => true,
            "message" => "Thành công !",
            "imageUrl" => $queryApi->user->hd_profile_pic_url_info->url,
            "type" => "image"
        ];

    
    }

    public function downloadPost($url) {

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {

            return [
                "success" => false,
                "message" => "url không hợp lệ"
            ];

        }

        $url = strtok($url, "?")."?__a=1&__d=dis";

        $result = json_decode($this->curl($url, "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
         )));
        

        if (!isset($result->items[0]->product_type)) {
            return [
                "success" => false,
                "message" => "k lấy được info"
            ];
        }

        $urls = [

        ];


        if ($result->items[0]->product_type == "carousel_container") {

            $type = "image";
            
            foreach ($result->items[0]->carousel_media as $carouselInfo) {

                $urls[] = $carouselInfo->image_versions2->candidates[0]->url;

            }

        }else if ($result->items[0]->product_type == "feed") {

            $type = "image";
            
            $urls[] = $result->items[0]->image_versions2->candidates[0];

        }else if ($result->items[0]->product_type == "clips") { 


            $type = "video";
            
            $urls[] = $result->items[0]->video_versions[0];



        }

        return [
            "success" => true,
            "message" => "success",
            "type" => $type,
            "urls" => $urls
        ];




        
    
    }



    public function downloadStories($url) {

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {

            return [
                "success" => false,
                "message" => "url không hợp lệ"
            ];

        }

        if (str_contains($url, 'stories')) {
            
            $pattern = '/\/stories\/(.*?)\//';

            // Perform the regular expression match
            preg_match($pattern, $url, $matches);

            if (isset($matches[1])) {
                $url = "https://www.instagram.com/".$matches[1]."/";
            } else {
                return [
                    "success" => false,
                    "message" => "url không hợp lệ"
                ];
            }

        }

        $username = $this->extractUsernameFromInstagramURL($url);

        $result = json_decode($this->curl("https://i.instagram.com/api/v1/users/web_profile_info/?username=".$username, "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Instagram 64.0.0.14.96",
         )));  

        if (!isset($result->data->user->id)) {
            return [
                "success" => false,
                "message" => "Không thể lấy info từ instagram"
            ];
        }

        $userId = $result->data->user->id;


        $fetchStories = json_decode($this->curl('https://www.instagram.com/graphql/query/?query_hash=de8017ee0a7c9c45ec4260733d81ea31&variables=%7B%22reel_ids%22%3A%5B%22'.$userId.'%22%5D%2C%22tag_names%22%3A%5B%5D%2C%22location_ids%22%3A%5B%5D%2C%22highlight_reel_ids%22%3A%5B%5D%2C%22precomposed_overlay%22%3Afalse%2C%22show_story_viewer_list%22%3Atrue%2C%22story_viewer_fetch_count%22%3A50%2C%22story_viewer_cursor%22%3A%22%22%7D', "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
         )));


        if (!isset($fetchStories->data->reels_media[0]->items)) {
            return [
                "success" => false,
                "message" => "No stories found"
            ];
        }


        $urls = [



        ];


        foreach ($fetchStories->data->reels_media[0]->items as $infoStory) {

            $urls[] = array(
                "video_url" => $infoStory->video_resources[0]->src,
                "image_url" => end($infoStory->display_resources)->src
            );

            

        }



        return [
            "success" => true,
            "message" => "success",
            "type" => "video",
            "data" => $urls
        ];




        
    
    }



    public function downloadHighlightStories($url) {

        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {

            return [
                "success" => false,
                "message" => "url không hợp lệ"
            ];

        }

        if (str_contains($url, 'stories')) {
            
            $pattern = '/\/stories\/highlights\/([0-9]+)\//';

            // Perform the regular expression match
            preg_match($pattern, $url, $matches);

            if (isset($matches[1])) {
                $highlightId = $matches[1];
            } else {
                return [
                    "success" => false,
                    "message" => "url không hợp lệ"
                ];
            }

        }else {

            return [
                "success" => false,
                "message" => "url không hợp lệ"
            ];

        }


        $fetchStories = json_decode($this->curl('https://i.instagram.com/api/v1/feed/reels_media/?reel_ids=highlight:'.$highlightId, "GET", array(
            "Cookie: sessionid=".$this->sessionId,
            "User-Agent: Instagram 64.0.0.14.96",
         )), true);


        if (!isset($fetchStories["reels"]["highlight:".$highlightId]["items"])) {
            return [
                "success" => false,
                "message" => "No stories found"
            ];
        }


        $urls = [



        ];


        foreach ($fetchStories["reels"]["highlight:".$highlightId]["items"] as $infoStory) {


            $urls[] = array(
                "video_url" => $infoStory["video_versions"][0]["url"],
                "image_url" => $infoStory["image_versions2"]["candidates"][0]["url"]
            );

        }



        return [
            "success" => true,
            "message" => "success",
            "type" => "video",
            "data" => $urls
        ];




        
    
    }





    public function extractUsernameFromInstagramURL($url) {
        $username = '';
    
        // Find the position of "instagram.com/" in the URL
        $start = strpos($url, "instagram.com/");
        
        if ($start !== false) {
            // Adjust the start position to point to the beginning of the username
            $start += strlen("instagram.com/");
            
            // Find the end position of the username
            $end = strpos($url, '/', $start);
            
            if ($end === false) {
                // If no slash found, the username is till the end of the string
                $username = substr($url, $start);
            } else {
                // Extract the username substring
                $username = substr($url, $start, $end - $start);
            }
        }
    
        return $username;
    }


    public function get_string_between($string, $start, $end)
	{
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

    public function validateUrl ($urlOriginal) {

        $url = parse_url($urlOriginal);

        if (empty($url['host'])) {
            return false;
        }

        $url['host'] = strtolower($url['host']);

    
        if ($url['host'] != self::INSTAGRAM_DOMAIN && $url['host'] != 'www.' . self::INSTAGRAM_DOMAIN) {

            return str_replace($url['host'], self::INSTAGRAM_DOMAIN, $url);

        }

        return $urlOriginal;

    }

    private function curl($url, $method = 'GET', $header = null, $postdata = null, $timeout = 60)
	{
		$s = curl_init();
		// initialize curl handler 
 
		curl_setopt($s,CURLOPT_URL, $url);
		//set option  URL of the location 
		if ($header) curl_setopt($s,CURLOPT_HTTPHEADER, $header);
		//set headers if presents
		curl_setopt($s,CURLOPT_TIMEOUT, $timeout);
		//time out of the curl handler  		
		curl_setopt($s,CURLOPT_CONNECTTIMEOUT, $timeout);
		//time out of the curl socket connection closing 
		curl_setopt($s,CURLOPT_MAXREDIRS, 3);
		//set maximum URL redirections to 3 
		curl_setopt($s,CURLOPT_RETURNTRANSFER, true);
		// set option curl to return as string ,don't output directly
		curl_setopt($s,CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($s,CURLOPT_COOKIEJAR, 'cookie.txt');
        curl_setopt($s,CURLOPT_COOKIEFILE, 'cookie.txt'); 
		//set a cookie text file, make sure it is writable chmod 777 permission to cookie.txt
 
		if(strtolower($method) == 'post')
		{
			curl_setopt($s,CURLOPT_POST, true);
			//set curl option to post method
			curl_setopt($s,CURLOPT_POSTFIELDS, $postdata);
			//if post data present send them.
		}
		else if(strtolower($method) == 'delete')
		{
			curl_setopt($s,CURLOPT_CUSTOMREQUEST, 'DELETE');
			//file transfer time delete
		}
		else if(strtolower($method) == 'put')
		{
			curl_setopt($s,CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($s,CURLOPT_POSTFIELDS, $postdata);
			//file transfer to post ,put method and set data
		}

        if ($this->proxy) {
            curl_setopt($s, CURLOPT_HTTPPROXYTUNNEL, 1);
            curl_setopt($s, CURLOPT_PROXY, explode("@", $this->proxy)[0]);
            curl_setopt($s, CURLOPT_PROXYUSERPWD, explode("@", $this->proxy)[1]);
        }
 
		curl_setopt($s,CURLOPT_HEADER, 0);			 
		// curl send header 
		curl_setopt($s,CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1');
		//proxy as Mozilla browser 
		curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false);
		// don't need to SSL verify ,if present it need openSSL PHP extension
 
		$html = curl_exec($s);
		//run handler
 
		$status = curl_getinfo($s, CURLINFO_HTTP_CODE);
		// get the response status
 
		curl_close($s);
		//close handler
 
		return $html;
		//return output
    }


}


?>