<?php

if (!function_exists('party_cache_process_fast')) {
   function party_cache_process_fast () {
      global $Party;

      $res=false;
         
      $request2ext=$Party['party.request.ext'];
      $cache2md5=$Party['party.cache.md5'];
      $cache2file='';
      $cache2active=false;

      switch ($request2ext) {
         case 'png':
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
            header("Content-Type:image/png");
            $Party['request.content-type']="image/png";
            break;
         case 'jpg':
         case 'jpeg':
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
            header("Content-Type:image/jpeg");
            $Party['request.content-type']="image/jpeg";
            break;
         case 'gif':
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
            header("Content-Type:image/gif");
            $Party['request.content-type']="image/gif";
            break;
         case 'svg':
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
            header("Content-Type:image/svg+xml");
            $Party['request.content-type']="image/svg+xml";
            break;
         case 'pdf':
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
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
            $cache2file=$Party['party.cache.dir']."/image/$cache2md5.$request2ext";
            $cache2active=party_cache_active($cache2file);
            header("Content-Type:application/octet-stream");
            break;
      }

      if ($cache2active) {
         header("X-Robots-Tag:noindex");
         readfile($cache2file);
         $res=true;
      }

      return $res;
 
   }
}



