<?php
require 'vendor/autoload.php';
require 'utils.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
//error_reporting(0);

$uri = parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$url = 'https://theync.com';

// Re-index the array
$uri = explode('/',$uri);

if ($uri[1]=='search'){
  $url = $url."/search/$uri[2]/page".(($uri[3]!=null)?$uri[3]:1).".html";
} else if ($uri[1]=='latest'){
  $url = $url."/most-recent/";
}

/*
if($_SERVER['REQUEST_METHOD']=='GET'){
  echo 'This is get';
}*/


if (isset($_GET['search'])){
  $kw = htmlspecialchars($_GET['search']);
  $turl = "/search/$kw/page1.html";
  $tpage;
  if (isset($_GET['page'])){
    $tpage = htmlspecialchars($_GET['page']);
    $turl = "/search/$kw"."/page$tpage.html";
  }
  $url = $url.$turl;
}

$xpath = request($url);
$db = [];

$pages = ext($xpath, '//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//a/@href');

$db['active'] = (int)ext($xpath, 'string(//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//span[@class="active"])');

for ($i = 0;$i < count($pages);$i++) {
  $db['page'][$i] = (int)substr($pages->item($i)->textContent,4,-5);
}

$imgs = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="image"]//img/@src');

$golds = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="border-gold"]');

$titles = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//a//span[@class="item-info"]//span[@class="title"]');

$users = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//span[@class="user"]//a');

$links = ext($xpath, "//div[@class=\"content-block\"]//div[@class=\"inner-block\"]//a[@data-title]/@href");

$upeds = ext($xpath, '//div[@class="content-block"]//div[@class="inner-block"]//span[@class="item-info"]//span[@class="added left"]//span[@class="count"]');

for ($i = 0; $i < count($imgs);$i++) {
  $db['data'][$i]["img"] = $imgs->item($i)->textContent;
  $db['data'][$i]["title"] = $titles->item($i)->textContent;
  $user = $users->item($i);
  
  $db['data'][$i]["user"] = ($user!=null)? $user->textContent:'';
  
  $db['data'][$i]['userpath'] = ($user!=null)? $user->getAttribute('href'):'';

  $db['data'][$i]['isexternal'] = str_contains($db['data'][$i]['user'],'theync');

  $db['data'][$i]['isgold'] = ($golds->item($i)!=null);
  
  $db['data'][$i]['uploaded_date'] = $upeds->item($i)->textContent;
  
  $db['data'][$i]['link'] = ext($xpath, 'string(//div[@class="content-block"]//div[@class="inner-block"]//a[@title="'.$db["data"][$i]["title"].'"]/@href)');
}

echo json_encode($db);
?>