<?php

global $Party;
if (!is_array($Party)) {
   $Party=array();

   $Party['version']=101;

   $Party['party.cache.dir']=$_SERVER['DOCUMENT_ROOT'].'/party-cache-zyz';
   $Party['party.cache.maxsize']=51200;
   $Party['party.cache.maxtime']=3600;

   $Party['party.debug']=0;

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

if (!function_exists('apache_request_headers')) {
   function apache_request_headers() {
      foreach($_SERVER as $key=>$value) {
         if (substr($key,0,5)=="HTTP_") {
            $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
            $out[$key]=$value;
         }
      }
      return $out;
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
      $request2ext=strtolower(trim($request2pathinfo['extension']));
      
      // source data
      $src2url2domain=$Party['src.url.domain'];

      // FIXME
      // curl is ok with GET query string inside CURLOPT_URL
      $src2uri=$_SERVER['REQUEST_URI'];

      $src_url="$src2url2domain$src2uri";

      $headers = apache_request_headers();

      // FIXME
      // OVH SPECIAL
      // reset OVH cookies
      if ($Party['hosting'] == 'ovh') {
         $_REQUEST['start']='';
         $_REQUEST['startBAK']='';
      }

      // CACHE HANDLING
      $request2serialize=serialize($src_url)."\n".serialize($_REQUEST);
      $request2md5=md5($request2serialize);

      $request2text=false;
      $request2image=false;

      $request2text=stripos($headers['Accept'], "text/");
      if (!$request2text) {
         $request2image=stripos($headers['Accept'], "image/");
      }

      $cache2active=false;
      $cache2file="";
      $cache2req="";
      if ($request2text !== FALSE) {
         $cache2file=$Party['party.cache.dir']."/text/$request2md5.txt";
         $cache2req=$Party['party.cache.dir']."/text/$request2md5-req.txt";
      }
      else if ($request2image !== FALSE) {
         $cache2ext='png';
         if (!empty($request2ext)) {
            $cache2ext=$request2ext;
         }
         $cache2file=$Party['party.cache.dir']."/image/$request2md5.$cache2ext";
      }

      if (!empty($cache2file) && is_file($cache2file)) {
         $cache2mtime=filemtime($cache2file);
         $now=time();
         $cache2age=($now - $cache2mtime);
         if ($cache2age < $Party['party.cache.maxtime']) 
            $cache2active=true;
      }

      if ($cache2active) {
         $result=file_get_contents($cache2file);
      }
      else {
         // Création d'une nouvelle ressource cURL
         $ch = curl_init();
         
         if ($ch !== FALSE) {
            // Configuration of URL and other options
            $options = array(
               CURLOPT_URL => $src_url,
               CURLOPT_HEADER => false,
               CURLOPT_RETURNTRANSFER => true,
               CURLOPT_FOLLOWLOCATION => true,
               CURLOPT_POSTFIELDS => $_REQUEST,
            );

            curl_setopt_array($ch, $options);

            // get URL content
            $result=curl_exec($ch);

            // end of CURL session
            curl_close($ch);
            // cache data 
            if (!empty($cache2file)) {
               if (strlen($result) < $Party['party.cache.maxsize']) {
                  file_put_contents($cache2file, $result);
               }
            }
            if (!empty($cache2req)) {
               if (strlen($cache2req) < $Party['party.cache.maxsize']) {
                  file_put_contents($cache2req, $request2serialize);
               }
            }
 
         }
         else {
            party_debug('CURL PROBLEM');
         }
         
     }

      if (!empty($request2ext)) {
         switch ($request2ext) {
            case 'png':
               header("Content-Type:image/png");
               header("X-Robots-Tag:noindex");
               echo $result;
               $request2text=false;
               break;
            case 'jpg':
            case 'jpeg':
               header("Content-Type:image/jpeg");
               header("X-Robots-Tag:noindex");
               echo $result;
               $request2text=false;
               break;
            case 'gif':
               header("Content-Type:image/gif");
               header("X-Robots-Tag:noindex");
               echo $result;
               $request2text=false;
               break;
            case 'svg':
               header("Content-Type:image/svg+xml");
               header("X-Robots-Tag:noindex");
               echo $result;
               $request2text=false;
               break;
            case 'pdf':
               header("Content-Type:application/pdf");
               echo $result;
               $request2text=false;
               break;
            default:
               break;
         }
      }

      if ($request2text !== false) {

         $translate=array();
         $translate[$src2url2domain]="http://".$_SERVER['SERVER_NAME'];

         $from=array_keys($translate);
         $to=array_values($translate);

         $result=str_replace($from, $to, $result);

         if (!empty($request2ext)) {
            switch ($request2ext) {
               case 'txt':
                  header("Content-Type:text/plain");
                  echo $result;
                  break;
               case 'css':
                  header("Content-Type:text/css");
                  echo $result;
                  break;
               case 'js':
                  header("Content-Type:text/javascript");
                  echo $result;
                  break;
               case 'htm':
               case 'html':
                  header("Content-Type:text/html");
                  echo $result;
                  break;
               default:
                  header("Content-Type:text/html");
                  echo $result;
                  // DEBUG
                  //header("Content-Type:text/plain");
                  //print_r($request2parse);
                  //print_r($request2pathinfo);
                  break;
            }
         }
         else if (stripos($headers['Accept'], "text/html") !== FALSE) {
            header("Content-Type:text/html");
            echo $result;
         }
         else if (stripos($headers['Accept'], "text/css") !== FALSE) {
            header("Content-Type:text/css");
            echo $result;
         }
         else if (stripos($headers['Accept'], "text/javascript") !== FALSE) {
            header("Content-Type:text/javascript");
            echo $result;
         }
      }

   }
}


if (!function_exists('party')) {
   function party () {
      // FIXME
      $party2config=dirname(dirname(__DIR__))."/party-config.php";
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




