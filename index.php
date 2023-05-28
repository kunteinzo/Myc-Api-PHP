ဘ<?php
require 'vendor/autoload.php';
require 'utils.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*
$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$uri = explode('/',$uri);

if($_SERVER['REQUEST_METHOD']=='GET'){
  echo 'This is get';
}*/

$uri = htmlspecialchars($_GET['url']);

$xpath = request($uri);

$imgs = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="image"]//img/@src');

$golds = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="border-gold"]');

$titles = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="title"]');

$users = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//span[@class="user"]//a');

$links = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//a[@data-title]');

$upeds = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//span[@class="item-info"]//span[@class="added left"]//span[@class="count"]');

$fulldb = [];

function read(DOMNodeList $nodelist,int $pos):string{
  $v1 = $nodelist->item($pos);
  return ($v1!=null)? $v1->textContent : 'Null';
}

for ($i = 0; $i < count($imgs);$i++) {
  $fulldb[$i]["img"] = $imgs->item($i)->textContent;
  $fulldb[$i]["title"] = $titles->item($i)->textContent;
  $fulldb[$i]["user"] = /*$users->item($i)->textContent;*/read($users,$i);
  $fulldb[$i]['id_gold'] = $golds->item($i)->textContent;
  #$tid = $users->item($i)->getAttribute('href');
  #$fulldb[$i]['userid'] = (str_contains($tid,'theync')) ?substr($tid,20) : 'null';
  $fulldb[$i]['uploaded_date'] = $upeds->item($i)->textContent;
  $fulldb[$i]['link'] = $links->item($i)->getAttribute('href');
}

echo json_encode($fulldb);
?>