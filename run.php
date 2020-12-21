<?php

// library
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/libraries/ip-vanish/module.php';

// implements
use Curl\Curl;
$IPVanish = new IPVanish();

start:

// configuration
include('config/configuration.php');

$curl = new Curl();
$curl->setProxy($servers, $port, $username, $password);
$curl->setProxyType(CURLPROXY_SOCKS5);
$curl->get('http://checkip.dyndns.org/');

function yarzCurl($url, $fields=false, $cookie=false, $httpheader=false, $encoding=false, $servers=false, $port=false, $username, $password)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_PROXYPORT, $port);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    curl_setopt($ch, CURLOPT_PROXY, $servers);
    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $username . ':' . $password);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if($fields !== false)
    {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    }
    if($encoding !== false)
    {
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    }
    if($cookie !== false)
    {
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    }
    if($httpheader !== false)
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
    }
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

if ($curl->error) {
    echo '[-] Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n\n";

    goto start;
} else {
    preg_match_all('/IP Address: (.*)<\/body><\/html>/', $curl->response, $ip_address);

    echo "[+] Connected " . $servers . ' | ' . $ip_address[1][0] . "\n";

    $headers = array();
    $headers[] = 'Cookie: '.$cookie;
    $headers[] = "Origin: https://twitter.com";
    $headers[] = "Accept-Encoding: gzip, deflate, br";
    $headers[] = "Accept-Language: en-US,en;q=0.9";
    $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36";
    $headers[] = "Content-Type: application/x-www-form-urlencoded";
    $headers[] = "Accept: application/json, text/javascript, */*; q=0.01";
    $headers[] = "Referer: https://twitter.com/";
    $headers[] = "Authority: twitter.com";
    $headers[] = "X-Requested-With: XMLHttpRequest";
    $headers[] = "X-Twitter-Active-User: yes";
    $url = 'https://twitter.com/i/tweet/create';
    $post = 'authenticity_token='.$auth.'&batch_mode=off&is_permalink_page=false&place_id=&status='.rand(1111,9999).' Happy+anniversary+yang+ke+8th+%F0%9F%8E%89%0A%23AnniversarySGBTeam8th%0A%23SGBTeam%0A%23SGBTeamAnniversary8th%0A%23SGBTeamAnniversary8&tagged_users=';

    $post = json_decode(yarzCurl($url, $post, false, $headers, true, $servers, $port, $username, $password));
    if(isset($post->tweet_id))
    {
        echo "[+] Success | Tweet ID : ".$post->tweet_id."\n\n";
        sleep($sleep);

        goto start;
    } else {
        echo "[-] Error for authentication.\n";
        print_r($post);
        echo "\n\n";

        goto start;
    }
}