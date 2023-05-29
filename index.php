<?php
require 'vendor/autoload.php';
require 'utils.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);

// Re-index the array
$uri = array_values(/*Remove empty string elements from array*/array_filter(explode('/',$uri), function($value) {
    return $value !== "";
}));


/*
if($_SERVER['REQUEST_METHOD']=='GET'){
  echo 'This is get';
}*/

$url;
if (isset($_GET['url'])){
  $url = htmlspecialchars($_GET['url']);
} else {
  $url = 'https://theync.com';
}

$xpath = request($url);
$fulldb = [];

$pages = ext($xpath, '//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//a/@href');
$active = ext($xpath, 'string(//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//span[@class="active"])');

$fulldb['active'] = (int)$active;

for ($i = 0;$i < count($pages);$i++) {
  $fulldb['page'][$i] = (int)substr($pages->item($i)->textContent,4,-5);
}

$imgs = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="image"]//img/@src');

$golds = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="border-gold"]');

$titles = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="title"]');

$users = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//span[@class="user"]//a');

$links = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//a[@data-title]');

$upeds = $xpath->evaluate('//div[@class="content-block"]//div[@class="inner-block"]//span[@class="item-info"]//span[@class="added left"]//span[@class="count"]');

for ($i = 0; $i < count($imgs);$i++) {
  $fulldb['data'][$i]["img"] = $imgs->item($i)->textContent;
  $fulldb['data'][$i]["title"] = $titles->item($i)->textContent;
  $fulldb['data'][$i]["user"] = read($users,$i);
  $fulldb['data'][$i]['user_id'] = read($users,$i)? substr($users->item($i)->getAttribute('href'),20): Null;
  $fulldb['data'][$i]['id_gold'] = read($golds,$i);
  #$tid = $users->item($i)->getAttribute('href');
  #$fulldb[$i]['userid'] = (str_contains($tid,'theync')) ?substr($tid,20) : 'null';
  $fulldb['data'][$i]['uploaded_date'] = $upeds->item($i)->textContent;
  $fulldb['data'][$i]['link'] = $links->item($i)->getAttribute('href');
}

echo json_encode($fulldb);
?>