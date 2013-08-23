<?php

if (!function_exists('party_curl_exec')) {
   function party_curl_exec ($src2url, $cache2file, $cache2req, $request2serialize) {
      global $Party;
            
      // Création d'une nouvelle ressource cURL
      $ch = curl_init();
         
      if ($ch !== FALSE) {
         // Configuration of URL and other options
         $options = array(
            CURLOPT_URL => $src2url,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
         );

         // FORWARD REQUEST DATA
         if (!empty($_REQUEST)) {
            $options[CURLOPT_POSTFIELDS]=$_REQUEST;
         }

         curl_setopt_array($ch, $options);

         // get URL content
         $Party['response.data']=curl_exec($ch);

         // end of CURL session
         curl_close($ch);

         // cache data 
         if (!empty($Party['response.data'])) {
            $Party['response.curl']=true;

            if (!empty($cache2file)) {
               if (strlen($Party['response.data']) < $Party['party.cache.maxsize']) {
                  file_put_contents($cache2file, $Party['response.data']);
               }
            }
            if (!empty($cache2req)) {
               if (strlen($cache2req) < $Party['party.cache.maxsize']) {
                  file_put_contents($cache2req, $request2serialize);
               }
            }
         }
      }
      else {
         party_debug('CURL PROBLEM');
      }         
   }

}


