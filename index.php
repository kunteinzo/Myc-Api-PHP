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

$uri = explode('/',$uri);

if (isset($_GET['url'])){
  echo json_encode(getMain($_GET['url']));
  exit(0);
}
if (isset($_GET['view'])){
  loadVideo($_GET['view']);
  exit(0);
}
if (isset($_GET['userload'])){
  echo json_encode(getMain(upbyuser($_GET['userload'])));
  exit(0);
}

if ($uri[1]=='search'){
  $url = $url."/search/$uri[2]/page".(($uri[3]!=null)?$uri[3]:1).".html";
} else if ($uri[1]=='latest'){
  $url = $url."/most-recent/";
  if ($uri[2]>1){
    $url = $url."page$uri[2].html";
  }
} else if ($uri[1]=='channels'){
  $url = $url.'/channels';
  $x = request($url);
  $titles = ext($x,'//div[@class="content-block"]//div[@class="inner-block"]//span[@class="title"]');

  $links = ext($x,'//div[@class="content-block"]//div[@class="inner-block"]//a//@href');
  $db = [];
  for ($i = 0;$i < count($titles);$i++){
    $db[$i]['title'] = $titles->item($i)->textContent;
    $db[$i]['link'] = $links->item($i)->textContent;
  }
  echo json_encode($db);
} else if ($uri[1]>1){
  $url = $url."/page$uri[1].html";
}

echo json_encode(getMain($url));

?>