<?php

global $Party;
if (!is_array($Party)) {
   $Party=array();

   $Party['version']=121;

   $Party['request.content-type']='';

   $Party['party.cache.dir']=$_SERVER['DOCUMENT_ROOT'].'/party-cache-zyz';
   $Party['party.cache.ext']='prt';
   $Party['party.cache.maxsize']=512000;
   $Party['party.cache.maxtime']=3600;

   $Party['party.debug']=10;

   // USER CONFIG
   $Party['hosting']='ovh';
   $Party['src.url.domain']='http://applh.com';
}

if (!function_exists('party_debug')) {
   function party_debug ($msg, $level=5) {
      global $Party;
      if ($level < $Party['party.debug']) {
         $now=time();
         echo "[$now][$msg]";
      }
   }
}

if (!function_exists('party_curl')) {
   function party_curl () {

      global $Party;

      // FIXME
      // request parsing
      $request2url='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
      $request2parse=parse_url($request2url);
      $request2path=$request2parse['path'];
      
      $request2pathinfo=pathinfo($request2path);
      // FIXME
      // NEED SOME MORE SECURITY CHECK ?
      $request2ext=strtolower(trim($request2pathinfo['extension']));
      $request2basename=trim($request2pathinfo['basename']);
      
      // source data
      $src2url2domain=$Party['src.url.domain'];

      // FIXME
      // curl is ok with GET query string inside CURLOPT_URL
      $src2uri=$_SERVER['REQUEST_URI'];

      $src_url="$src2url2domain$src2uri";
      
      // FIXME
      // OVH SPECIAL
      // reset OVH cookies
      if ($Party['hosting'] == 'ovh') {
         unset($_REQUEST['start']);
         unset($_REQUEST['startBAK']);
      }

      $Party['party.request.basename']=$request2basename;
      $Party['party.request.ext']=$request2ext;

      // CACHE HANDLING
      $request2serialize=serialize($src_url)."\n".serialize($_REQUEST);
      $request2md5=md5($request2serialize);

      $Party['party.cache.md5']=$request2md5;
      $Party['party.cache.file']=$Party['party.cache.dir']."/$request2md5.".$Party['party.cache.ext'];
      $Party['party.cache.request']=$Party['party.cache.dir']."/$request2md5-req.txt";
 
      $response2fast=false;
      if (!empty($request2ext) || empty($request2basename)) {
         // HIGHWAY FOR SIMPLE FILES 
         include_once(__DIR__.'/inc-cache-fast.php');
         $response2fast=party_cache_process_fast();
      }

      if (!$response2fast) {
         // CACHE IS NOT READY
         // STANDARD PROCESS
         include_once(__DIR__.'/inc-proxy.php');
         party_proxy($src_url, $request2md5, $request2serialize);

      }

   }

}



if (!function_exists('party')) {
   function party () {
      // FIXME
      $party2config=$_SERVER['DOCUMENT_ROOT']."/party-config.php";
      if (file_exists($party2config)) {
         include($party2config);
      }

      party_curl();
   }
}

// DEV
if (!function_exists('party')) {
   function party () {

      header("Content-Type:text/plain");

      echo(date("H:i:s"));

      $arr = get_defined_functions();

      print_r($arr);

   }
}






