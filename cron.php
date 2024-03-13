<?php
require "vendor/autoload.php";
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

$array = [];

$accounts = json_decode(file_get_contents("./data/account.json"), true);
$proxies = json_decode(file_get_contents("./data/proxies.json"), true);

foreach ($accounts as $data) {
    $username = explode("|", $data)[0];
    $password = explode("|", $data)[1];

    $proxy = $proxies[array_rand($proxies)];

    $headers = array(
        "Host: i.instagram.com",
        "X-Ig-Connection-Type: WiFi",
        "Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
        "X-Ig-Capabilities: 36r/Fx8=",
        "User-Agent: Instagram 159.0.0.28.123 (iPhone8,1; iOS 14_1; en_SA@calendar=gregorian; ar-SA; scale=2.00; 750x1334; 244425769) AppleWebKit/420+",
        "X-Ig-App-Locale: en",
        "X-Mid: Ypg64wAAAAGXLOPZjFPNikpr8nJt",
        "Accept-Encoding: gzip, deflate"
    );

    $data = array(
        "username" => $username,
        "reg_login" => "0",
        "enc_password" => "#PWD_INSTAGRAM:0:&:" . $password,
        "device_id" => gen_uuid(),
        "login_attempt_count" => "0",
        "phone_id" => gen_uuid()
    );


    $client = new Client([
        'base_uri' => 'https://i.instagram.com/api/v1/',
        'headers' => [
            'Host' => 'i.instagram.com',
            'X-Ig-Connection-Type' => 'WiFi',
            'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Ig-Capabilities' => '36r/Fx8=',
            'User-Agent' => 'Instagram 159.0.0.28.123 (iPhone8,1; iOS 14_1; en_SA@calendar=gregorian; ar-SA; scale=2.00; 750x1334; 244425769) AppleWebKit/420+',
            'X-Ig-App-Locale' => 'en',
            'X-Mid' => 'Ypg64wAAAAGXLOPZjFPNikpr8nJt',
            'Accept-Encoding' => 'gzip, deflate'
        ],
        'proxy' => "http://".explode("@", $proxy)[1]."@".explode("@", $proxy)[0],
    ]);


    $response = $client->post('accounts/login/', [
        'form_params' => [
            'username' => $username,
            'reg_login' => '0',
            'enc_password' => "#PWD_INSTAGRAM:0:&:" . $password,
            'device_id' => gen_uuid(),
            'login_attempt_count' => '0',
            'phone_id' => gen_uuid()
        ]
    ]);


    $body = $response->getBody();
    $json = json_decode($body);
    
    if ($response->getStatusCode() == 200) {
        $session_id = $response->getHeader('set-cookie')[0];

        if ($session_id) {

            $sessId = explode("sessionid=", explode(";", $session_id)[0])[1];

            array_push($array, $sessId);

        }

        
    }
    


}

// xử lý
$json_data = json_encode($array, JSON_PRETTY_PRINT);

// Path to the JSON file
$json_file = './data/data.json';

// Write JSON data to the file
file_put_contents($json_file, $json_data);






function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

?>