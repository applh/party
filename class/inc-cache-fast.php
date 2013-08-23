<?php

if (!function_exists('party_cache_process_fast')) {
   function party_cache_process_fast () {
      global $Party;

      $res=false;
         
      $request2ext=$Party['party.request.ext'];

      switch ($request2ext) {
         case 'png':
            header("Content-Type:image/png");
            $Party['request.content-type']="image/png";
            break;
         case 'jpg':
         case 'jpeg':
            header("Content-Type:image/jpeg");
            $Party['request.content-type']="image/jpeg";
            break;
         case 'gif':
            header("Content-Type:image/gif");
            $Party['request.content-type']="image/gif";
            break;
         case 'svg':
            header("Content-Type:image/svg+xml");
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
            $Party['request.content-type']="text/css";
            break;
         case 'js':
            header("Content-Type:text/javascript");
            $Party['request.content-type']="text/javascript";
            break;
         case 'txt':
            header("Content-Type:text/plain");
            $Party['request.content-type']="text/plain";
            break;
            break;
         default:
            if (empty($request2ext)) 
               $request2ext='prt';
            header("Content-Type:application/octet-stream");
            break;
      }

      $cache2active=false;
      if ($request2ext) {
         $cache2md5=$Party['party.cache.md5'];
         $cache2file=$Party['party.cache.dir']."/$cache2md5.$request2ext";
         $cache2active=party_cache_active($cache2file);
      }

      if ($cache2active) {
         header("X-Robots-Tag:noindex");
         readfile($cache2file);
         $res=true;
      }

      return $res;
 
   }
}



