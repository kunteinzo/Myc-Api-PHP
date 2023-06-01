<?php
require 'vendor/autoload.php';

function request(string $url): DOMXPath{
  $client = new GuzzleHttp\Client();
  $headers = [
  'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/113.0.0.0 Safari/537.36'];
  $request = new GuzzleHttp\Psr7\Request('GET', $url, $headers);
  $res = $client->sendAsync($request)->wait();
  //echo $res->getBody();
  $htmlString = (string) $res->getBody();
  //add this line to suppress any warnings
  libxml_use_internal_errors(true);
  $doc = new DOMDocument();
  $doc->loadHTML($htmlString);
  return new DOMXPath($doc);
}

function ext(DOMXPath $xpath,string $reg): Mixed{
  return $xpath->evaluate($reg);
}

function upbyuser($link){
  if ($link==null){
    return "Error";
  }
  return ext(request($link), 'string(//div[@class="upload-button"]//div[@class="inner-block"]//a/@href)');
}

function loadVideo($url){
  $x = request($url);
  $db = getMain($x);
  $links = ext($x,'//script[@type="text/javascript"]');
  $link = '';
  $thumb = '';
  foreach ($links as $l){
    if (str_contains($l->textContent,'file')){
      $string = $l->textContent;
      preg_match('/file:\s*"(.*?)"/', $string, $m1);
      preg_match('/image:\s*"(.*?)"/', $string, $m2);

      if (isset($m1[1])) {
        $link = $m1[1];
      }
      if (isset($m2[1])) {
        $thumb = $m2[1];
      }
    }
  }
  echo json_encode(array('link'=>$link,'thumb'=>$thumb,'related'=>$db));
}

function getMain($url){
  $xpath = ($url instanceof DOMXPath)? $url:request($url);
  $db = [];

  $pages = ext($xpath, '//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//a/@href');

  $db['active'] = (int)ext($xpath, 'string(//div[@id="pages"]//div[@class="pagination-row"]//div[@class="inner-block"]//span[@class="active"])');
  $pa = [];
  $ip = 0;
  foreach ($pages as $p){
    $pa[$ip] = (int)substr($p->textContent,4,-5);
    $ip++;
  }
  $db['pages'] = max($pa);

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
  return $db;
}

?>