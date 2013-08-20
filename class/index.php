<?php

global $Party;
if (!is_array($Party)) {
   $Party=array();
   $Party['src.url.domain']='http://applh.com';
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

      // Création d'une nouvelle ressource cURL
      $ch = curl_init();

      // Configuration de l'URL et d'autres options
      $options = array(
         CURLOPT_URL => $src_url,
         CURLOPT_HEADER => false,
         CURLOPT_RETURNTRANSFER => true,
      );

      curl_setopt_array($ch, $options);

      // Récupération de l'URL et affichage sur le naviguateur
      $result=curl_exec($ch);

      // Fermeture de la session cURL
      curl_close($ch);

      
      if (stripos($headers['Accept'], "text/") !== FALSE) {

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
      else if (stripos($headers['Accept'], "image/") !== FALSE) {

         switch ($request2ext) {
            case 'png':
               header("Content-Type:image/png");
               echo $result;
               break;
            case 'jpg':
            case 'jpeg':
               header("Content-Type:image/jpeg");
               echo $result;
               break;
            case 'gif':
               header("Content-Type:image/gif");
               echo $result;
               break;
            default:
               break;
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


