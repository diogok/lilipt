<?php

$base = getenv("BASE_URL");
if($base == null) {
  $base = "http://localhost:8080";
}

define('BASE',$base);

$routes=[];
function app($uri,$fun) {
  global $routes;
  $routes[$uri] = $fun;
}

function run() {
  global $routes;
  $reqURI = $_SERVER['REQUEST_URI'];
  $reqURI = str_replace('?'.$_SERVER['QUERY_STRING'],"",$reqURI);
  foreach($routes as $uri=>$fun) {
    if($reqURI === $uri) {
      $fun();
      exit;
    }
  }
  header('HTTP/1.1 404 Not Found');
  echo "404 Not Found";
}

app('',function(){
  echo '<title>Little IPT</title>';
  $dir = opendir(__DIR__.'/data');
  echo '<ul>';
  while($f = readdir($dir)) {
    if($f[0] != ".") {
      $name = str_replace(".zip","",$f);
      echo '<li><a href="'.BASE.'/archive.do?r='.$name.'">'.$name.'</a></li>';
    }
  }
  echo '</ul>';
  echo '<p><a href="'.BASE.'/rss.do">RSS</a></p>';
  closedir($dir);
});

app('/',function(){
  echo '<title>Little IPT</title>';
  $dir = opendir(__DIR__.'/data');
  echo '<ul>';
  while($f = readdir($dir)) {
    if($f[0] != ".") {
      $name = str_replace(".zip","",$f);
      echo '<li><a href="'.BASE.'/archive.do?r='.$name.'">'.$name.'</a></li>';
    }
  }
  echo '</ul>';
  echo '<p><a href="'.BASE.'/rss.do">RSS</a></p>';
  closedir($dir);
});

app("/rss.do",function() {
  header("Content-Type: text/xml");
  echo '<rss xmlns:ipt="http://ipt.gbif.org/" version="2.0">';
  echo '<channel>';
  echo '<title>Little IPT</title>';
  echo '<link>'.BASE.'</link>';

  $dir = opendir(__DIR__.'/data');
  while($f = readdir($dir)) {
    if($f[0] != ".") {
      $name = str_replace(".zip","",$f);
      echo '<item>';
      echo '<title>'.BASE.'</title>';
      echo '<link>'.BASE.'/resource.do?r='.$name.'</link>';
      echo '<description>'.BASE.'</description>';
      echo '<title>'.BASE.'</title>';
      echo '<author>To Do</author>';
      echo '<ipt:eml>To Do</ipt:eml>';
      echo '<ipt:dwca>'.BASE.'/archive.do?r='.$name.'</ipt:dwca>';
      echo '<pubDate>'.date('D, d M Y H:i:s O',filemtime(__DIR__.'/data/'.$f)).'</pubDate>';
      echo '</item>';
    }
  }

  echo '</channel>';
  echo '</rss>';
});

app("/archive.do",function(){
  if(file_exists(__DIR__.'/data/'.$_GET["r"].".zip")) {
    header('Location: '.BASE.'/data/'.$_GET['r'].".zip");
  } else {
    header('HTTP/1.1 404 Not Found');
    echo "404 Not Found";
  }
});

run();

