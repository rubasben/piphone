<?php

   //echo "##DEBUG###";
   if (!isset($_GET['uuid']) ) die("Error: no uuid.");
   $uuid = $_GET['uuid'];
   
   require "../lib/plivo/plivohelper.php";
   $REST_API_URL = 'http://127.0.0.1:8088';
   $ApiVersion = 'v0.1';
   $AccountSid = 'pheingahg4keeHi8eghaeroish5Ahh';
   $AuthToken = 'ye1Eeseozio9aeHengouSaifood8la';

   //instanciation du client plivo
   $client = new PlivoRestClient($REST_API_URL, $AccountSid, $AuthToken, $ApiVersion);
   
   $params = array( 'UUID' => $uuid , 'varName' => 'hangup_cause' );
   try {
   
      $res = $client->get_var($params);
   
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit(0);
    }

  if (!$res) die('Response object = NULL');
  
  if ( strpos($res->Response->Message,"-ERR") === 0 ) echo "FINISHED";
  else echo "OK"

 
?>
