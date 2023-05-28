<?php
require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
#header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
echo $uri;
/*
if($_SERVER['REQUEST_METHOD']=='GET'){
  echo 'This is get';
}*/

function request(string $url): DOMXPath {
  $client = new Client();
  $headers = [
  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36'];
  $request = new Request('GET', 'https://theync.com/', $headers);
  $res = $client->sendAsync($request)->wait();
  //echo $res->getBody();
  $htmlString = (string) $res->getBody();
  //add this line to suppress any warnings
  libxml_use_internal_errors(true);
  $doc = new DOMDocument();
  $doc->loadHTML($htmlString);
  return new DOMXPath($doc);
}

$xpath = request('https://www.theync.com/');

$imgs = $xpath->evaluate('//div[@class="today-top"]//div[@class="inner-block"]//a//span[@class="image"]//img/@src');

$titles = $xpath->evaluate('//div[@class="today-top"]//div[@class="inner-block"]//a//span[@class="title"]');

$users = $xpath->evaluate('//div[@class="today-top"]//div[@class="inner-block"]//span[@class="user"]//a');

$links = $xpath->evaluate('//div[@class="today-top"]//div[@class="inner-block"]//a/@href');

$upeds = $xpath->evaluate('//div[@class="today-top"]//div[@class="inner-block"]//span[@class="item-info"]//span[@class="added left"]//span[@class="count"]');

$fulldb = [];

for ($i = 0; $i < count($imgs);$i++) {
  $fulldb[$i]["img"] = $imgs->item($i)->textContent;
  $fulldb[$i]["title"] = $titles->item($i)->textContent;
  $fulldb[$i]["user"] = $users->item($i)->textContent;
  $tid = $users->item($i)->getAttribute('href');
  $fulldb[$i]['userid'] = (str_contains($tid,'theync')) ?substr($tid,20) : 'null';
  $fulldb[$i]['uploaded_date'] = $upeds->item($i)->textContent;
  $fulldb[$i]['link'] = $links->item($i)->textContent;
}

#add to github
//echo '<img src=\"'.$fulldb[5]["img"].'" />'
echo json_encode($fulldb);
/*
$src = $xpath->evaluate("string(//head//title)");
print str_contains(strtolower($src),'theync');*/

?>