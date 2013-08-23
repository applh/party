<?php

$curdir=__DIR__;
$docroot=$_SERVER['DOCUMENT_ROOT'];

$has2index2html=false;
$has2index2htm=false;
$has2index2php=false;
$has2htaccess=false;

if (is_file("$docroot/index.html")) $has2index2html=true;
if (is_file("$docroot/index.htm")) $has2index2html=true;
if (is_file("$docroot/index.php")) $has2index2php=true;
if (is_file("$docroot/.htaccess")) $has2access=true;

// SAVE CURRENT FILES
if ($has2index2htm) {
   $name2=uniqid('index-').'.htm';
   rename("$docroot/index.htm", "$docroot/$name2");
}
if ($has2index2html) {
   $name2=uniqid('index-').'.html';
   rename("$docroot/index.html", "$docroot/$name2");
}
if ($has2index2php) {
   //FIXME
   // CHECK IF FILE IS OK 
   $name2=uniqid('index-').'.php';
   rename("$docroot/index.php", "$docroot/$name2");
}
if ($has2htaccess) {
   //FIXME
   // CHECK IF FILE IS OK 
   $name2=uniqid('htaccess-').'.txt';
   rename("$docroot/.htaccess", "$docroot/$name2");
}

// CREATE NEW FILES AND FOLDER

// FILES

$cur2file="$docroot/index.php";
$cur2content=
<<<CUR2CONTENT
<?php
define('PARTY_START', TRUE);
include("$curdir/index.php");
CUR2CONTENT;
if (!is_file($cur2file)) {
   file_put_contents($cur2file, $cur2content);
}

$cur2file="$docroot/.htaccess";
$cur2content=
<<<CUR2CONTENT

RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]

CUR2CONTENT;
if (!is_file($cur2file)) {
   file_put_contents($cur2file, $cur2content);
}

$cur2file="$docroot/party-config.php";
$cur2content=
<<<'CUR2CONTENT'
<?php

global $Party;

// YOUR WEBSITE SETUP
$Party['src.url.domain']='http://applh.com';

$Party['party.cache.maxsize']=512000;
$Party['party.cache.maxtime']=3600;


CUR2CONTENT;
if (!is_file($cur2file)) {
   file_put_contents($cur2file, $cur2content);
}


$cur2file="$docroot/robots.txt";
$cur2content=
<<<'CUR2CONTENT'
User-agent: *
Disallow:

CUR2CONTENT;
if (!is_file($cur2file)) {
   file_put_contents($cur2file, $cur2content);
}


// FOLDERS

$cur2dir="$docroot/party-cache-zyz";
if (!is_dir($cur2dir)) {
   mkdir($cur2dir);
   touch("$cur2dir/index.php");
}

?>
<!DOCTYPE html>
<html>
   <head>
   <title>PARTY * INSTALL</title>
   </head>
   <body>
   </body>
</html>

