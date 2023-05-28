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

function ext(DOMXPath $xpath,string $reg): DOMNodeList{
  return $xpath->evaluate($reg);
}

?>