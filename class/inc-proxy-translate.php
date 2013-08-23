<?php

if (!function_exists('party_proxy_translate')) {
   function party_proxy_translate () {
      global $Party;

      $cache2file=$Party['proxy.cache.file'];
      $header2accept=$Party['proxy.header.accept'];

      if (!empty($Party['response.data'])) {

         $src2url2domain=$Party['src.url.domain'];

         $translate=array();
         // replace domain name
         $translate[$src2url2domain]="http://".$_SERVER['SERVER_NAME'];

         $from=array_keys($translate);
         $to=array_values($translate);

         $Party['response.result']=str_replace($from, $to, $Party['response.data']);

         if (!empty($request2ext)) {
            // FIXME
         }
         else if (stripos($header2accept, "text/html") !== FALSE) {
            header("Content-Type:text/html");
         }
         else if (stripos($header2accept, "text/css") !== FALSE) {
            header("Content-Type:text/css");
         }
         else if (stripos($header2accept, "text/javascript") !== FALSE) {
            header("Content-Type:text/javascript");
         }

         // SAVE UPDATED CONTENT
         if (!empty($cache2file)) {
            if (strlen($Party['response.result']) < $Party['party.cache.maxsize']) {
               file_put_contents($cache2file, $Party['response.result']);
            }
         }
      }
          
   }

}


