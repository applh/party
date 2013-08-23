<?php

if (!function_exists('party_proxy')) {
   function party_proxy ($src2url, $request2md5, $request2serialize) {
      global $Party;

      if (!empty($src2url)) {
         $header2accept=$_SERVER['HTTP_ACCEPT'];
         $request2image=false;

         if (!empty($Party['request.content-type'])) {
               $header2accept=$Party['request.content-type'];
         }

         if ($header2accept) {
            $Party['proxy.header.accept']=$header2accept;

            $request2text=stripos($header2accept, "text/");
            if (!$request2text) {
               $request2image=stripos($header2accept, "image/");
            }

            if ($request2text !== FALSE) {
               $cache2ext='txt';
               $Party['proxy.translate']=true;
            }
            else if ($request2image !== FALSE) {
               $cache2ext='png';
            }

            if (!empty($request2ext)) {
               $cache2ext=$request2ext;
            }
         
            if (!empty($src2url)) {
               $cache2req=$Party['party.cache.dir']."/$request2md5-req.txt";
               $cache2file=$Party['party.cache.dir']."/$request2md5.$cache2ext";

               $Party['proxy.cache.file']=$cache2file;

               include_once(__DIR__.'/inc-curl.php');
               party_curl_exec($src2url, $cache2file, $cache2req, $request2serialize);
            }
 
         }
      }
 
   }

}


