<?php

if (!function_exists('party_cache_active')) {
   function party_cache_active ($cache2file) {
      global $Party;
      $cache2active=false;
      if (!empty($cache2file) && is_file($cache2file)) {
            $cache2mtime=filemtime($cache2file);
            $now=time();
            $cache2age=($now - $cache2mtime);
            if ($cache2age < $Party['party.cache.maxtime']) {
               if (0 < filesize($cache2file))
                  $cache2active=true;
            }
     }
     return $cache2active;
   }
}


if (!function_exists('party_cache_process_fast')) {
   function party_cache_process_fast () {
      global $Party;

      $res=false;
         
      $request2ext=$Party['party.request.ext'];
      $request2basename=$Party['party.request.basename'];

      if (empty($request2basename)) {
         // FIXME
         // Consider URI ending with / as webpages
         header("Content-Type:text/html");
         $Party['request.content-type']="text/html";
      }
      else {
         switch ($request2ext) {
         case 'png':
            header("Content-Type:image/png");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="image/png";
            break;
            case 'jpg':
         case 'jpeg':
            header("Content-Type:image/jpeg");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="image/jpeg";
            break;
         case 'gif':
            header("Content-Type:image/gif");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="image/gif";
            break;
         case 'svg':
            header("Content-Type:image/svg+xml");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="image/svg+xml";
            break;
         case 'pdf':
            header("Content-Type:application/pdf");
            $Party['request.content-type']="application/pdf";
            break;
         case 'htm':
         case 'html':
            header("Content-Type:text/html");
            $Party['request.content-type']="text/html";
            break;
         case 'css':
            header("Content-Type:text/css");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="text/css";
            break;
         case 'js':
            header("Content-Type:text/javascript");
            header("X-Robots-Tag:noindex");
            $Party['request.content-type']="text/javascript";
            break;
         case 'txt':
            header("Content-Type:text/plain");
            $Party['request.content-type']="text/plain";
            break;
            break;
         default:
            header("Content-Type:application/octet-stream");
            break;
         }
      }

      $cache2file=$Party['party.cache.file'];
      $cache2active=party_cache_active($cache2file);

      if ($cache2active) {
         readfile($cache2file);
         $res=true;
      }

      return $res;
 
   }
}



