<?php

  require_once ('conf/config.inc.php');

  function displayBox($type,$msg) {

    echo "<div id=\"displaybox\" class=\"box\"><img style=\"width:32px\" src=\"images/$type.png\"/><div class=\"boxtext\">$msg</div></div>";    

  }

  function isPhone($num) {
  
    if (! is_numeric($num) ) return false;
    if (substr($num,0,2) == "00") return true;

    $prefixes = array("01","02","02","03","04","05","06","07","09");
    foreach($prefixes as $prefix) {

       if (substr($num,0,2) == $prefix) return true;
    }

    return false;

  }

  function genBridgeUUID($length) {
    $randstr = "";
    for($i=0; $i<$length; $i++){
         $randnum = mt_rand(48,57);
         $randstr .= chr($randnum);
    }
    return $randstr;    
  }


  function hasInternationalExt($num) {

    if (!substr($num,0,2) == "00") return false;
    return true;

  }


  if (!isset($_GET['callto']) ) {

    $ERR = "Pas de destinataire";

  }

  else { 

    $callto = $_GET['callto'];  
    if (!isPhone($callto)) $ERR = "Le numéro du destinataire est invalide";
    if (!hasInternationalExt($callto)) {
      $callto = substr($callto,1,strlen($callto)-1);
      $callto = $cn + "0033";
    }

  }

  if ( isset($_POST['callbacknum']) ) { 

    $cn = $_POST['callbacknum'];

    //vérification du numéro de téléphone, et ajout du préfix international si nécéssaire.
    if (!isPhone($cn)) $ERR = "Le numéro entré est invalide";
    if (!hasInternationalExt($cn)) {
      $cn = substr($cn,1,strlen($cn)-1);
      $cn = $cn + "0033";
    }
 
    $MSG = "Nous allons vous appeler dans quelques instants..";

    if (!isset($ERR)) {

      //## Code plivo pour le controle de Freeswitch
      require "lib/plivo/plivohelper.php";

      //instanciation du client plivo
      $client = new PlivoRestClient($REST_API_URL, $AccountSid, $AuthToken, $ApiVersion);

      //$bridge_id = genBridgeUUID(1);
      //$bridge_id = '1234';

      //cree un nouvel uuid dans freeswitch
      $params = array();
      $response = $client->create_uuid($params);
      $bridge_id = $response->Response->Message;

      $params = array( 'command' => "luarun bridge.lua $bridge_id sofia/gateway/$sipconf/$cn sofia/gateway/$sipconf/$callto sofia/gateway/$sipconf/$cn sofia/gateway/$sipconf/$callto lqdn_ann.wav",'bg' => 'true');

      try {
        // Initiate bridge
        $response = $client->command($params);
                
      } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        exit(0);
     }
     //##
  
    }

  }

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>	
    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="pragma" content="no-cache" />
    <link rel="shortcut icon" type="image/png" href="images/favicon.png" />
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript">

      function answer(ans) {

        $('#ans_yes').attr('class','ans');
        $('#ans_no').attr('class','ans');

        $('#ans_' + ans).attr('class','ans_sel');
        $('#ans').val(ans);

      }

    </script>

    <style type="text/css">
      body {

        font-family:Arial,Helvetica,Sans-Serif;
        font-size:12px;
        border:0px;
        padding:0px;
        margin:0px;
        overflow:hidden;
        background: url(images/grad_bg.png) no-repeat;
        background-position: 0px 65px;
       }

       textarea {

          width:400px;
          background:white;
          border: 1px solid #467AD0;
          font-size:13px; 
          color:black;


       }

       #debug {

         background:black;
         color:white;
         height:50px;
         font-size:11px;


       }

       #callback_field {

         width:200px;
         background:white;
         border: 1px solid #467AD0;
         height:30px;
         font-size:20px;
         color:black;
         -moz-border-radius: 4px;
         -webkit-border-radius: 4px;
         

       }

       .step {

         font-size:70px;
         font-weight:bold;
         float:left;
         width:60px;
         color:#333333;


       }

       .step_ct {
         float:left;
         width:400px;
         color:#666666;
         font-size:20px;
         margin-top:10px;
         text-align:left;
         float:left;
       }

       .ans {

       -moz-border-radius: 7px;
        -webkit-border-radius: 7px;
        border: 2px solid white;
        color:white;
        font-weight:bold;
        font-size:13px;
        background: rgba(0,0,0,0.6);
        width:70px;
        height:27px;
        float:left;
        cursor:pointer;    
      

       }

       .ans:hover {
          background: rgba(150,150,150,0.6);
      
       }

       .ans_sel {

       -moz-border-radius: 7px;
        -webkit-border-radius: 7px;
        border: 2px solid white;
        color:white;
        font-weight:bold;
        font-size:13px;
        background: rgba(37,131,209,0.6);
        width:70px;
        height:27px;
        float:left;
        cursor:pointer;    
      
       }


       .box {
         padding:20px;
         text-align:center;
         width:400px;
         -moz-border-radius: 7px;
         -webkit-border-radius: 7px;
         border: 2px solid white;
         background: rgba(0,0,0,0.6);
         position:absolute;
         left:50%;
         margin-left:-220px;
         top:200px;
         color:white;
         font-weight:bold;
         font-size:22px;
       }

       #pi_fct {
         text-align:center;
       }

       #form_ct {
 
         position:relative;
         width:200px;
         left:50%;
         margin-left:-100px;
      }

      .btn {
 
        width:120px;
        background: url(images/btn.png) repeat-x;
        height:33px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        color:white;
        font-weight:bold;
        font-size:15px;
        cursor:pointer;    
        
      }

      .btn_over {
 
        width:120px;
        background: url(images/btn_over.png) repeat-x;
        height:33px;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        color:white;
        font-weight:bold;
        font-size:15px;
        cursor:pointer;        
      }
 
      .boxtext {
        margin-top:15px;
      }

    </style>
  </head>
  <body>

    <div id="preload" style="display:none">
      <img src="images/btn_over.png">
    </div>

    <!-- <div id="debug">Debug</div> -->

    <div id="head" style="text-align:center;height:65px;box-shadow: 0 0 5px #888;">
      
      <img style="margin-top:10px"src="images/piphone.png" />
    </div>


    <div id="retbox" class="box" style="top:180px;font-size:13px;display:none">

     <div id="Question" style="font-size:22px">L'appel s'est-il bien passé ?</div>
    
     
     <div style="margin-left:110px;overflow:hidden;margin-top:10px;width:180px">
       <div class="ans" id="ans_yes" onclick="answer('yes')"><div style="padding-top:5px">Oui</div></div>
       <div class="ans" style="margin-left:30px" id="ans_no"  onclick="answer('no')"><div style="padding-top:5px">Non</div></div>
     </div>


     <form method="POST" name="retFrm" id="retFrm" action="?callto=<?= $_GET['callto'] ?>">
     <div style="width:400px;overflow:hidden;text-align:left;margin-top:15px">
       Commentaire additionnel:<br>
       <textarea name="comment"></textarea>
     </div>
       <input type="hidden" id="ans" name="ans" value="yes" />
       <div class="btn" style="float:right;margin-top:10px" onmouseover="$(this).attr('class','btn_over')" onmouseout="$(this).attr('class','btn');" onclick="$('#retFrm').submit()"><div style="padding-top:7px">Envoyer</div></div>
    </form>
   </div>




    <?php
      if (!isset($ERR) && !isset($MSG) && ! isset($_POST['ans']) ) {

      //debug returnBox
      //exit();
    ?>

    <div id="pi_fct"><h2 style="color:#3798D7">Comment ca marche ?</h2></div>    

      <div style="padding:25px;padding-top:10px">    

        <div style="overflow:hidden">
          <div class="step" style="color:#3798D7">1.</div>
          <div class="step_ct">Entrez votre numéro de téléphone.</div>
        </div>    

        <div style="overflow:hidden;margin-top:20px">
          <div class="step" style="color:#2583d1" >2.</div>
          <div class="step_ct">Notre serveur téléphonique va vous appeler puis vous mettre en relation avec votre représentant.</div>
        </div>  
 
        <div style="overflow:hidden;margin-top:20px;">
          <div class="step" style="color:#095097">3.</div>
          <div class="step_ct">C'est à vous de jouer, détendez-vous ca va bien se passer :)</div>
        </div>
      </div>


    <div id="form_ct">

      <form id="callFrm" method="POST" action="?callto=<?= $_GET['callto'] ?>">
        <b>Numéro de téléphone:</b><br>
        <input id="callback_field" name="callbacknum" /> 
      </form>
   
      <div style="text-align:center;">
        <div class="btn" onclick="$('#callFrm').submit();" style="position:relative;left:50%;margin-left:-60px" onmouseover="$(this).attr('class','btn_over');" onmouseout="$(this).attr('class','btn');"><div style="padding-top:8px">Commencer</div></div>
      </div>

    </div>

    <?php

      }


       //Traitement des données du formulaire retFrm
       else if (isset($_POST['ans'])) {

         $ans = $_POST['ans'];   
         $comment = "";

         if (isset($_POST['comment']) ) $comment = $_POST['comment'];

         $comment = mysql_real_escape_string($comment,$dbhandler) ;
         $dbh = mysql_query("INSERT INTO feedback (date,wasgood,comment) VALUES (NOW(),'$ans','$comment');",$dbhandler);

         if (!$dbh) die(mysql_error());

         displayBox('info','Nous vous remercions pour ce geste citoyen');
         echo '<script type="text/javascript">  setInterval(function(){ window.close()  },2000); </script>';

      }

      else if (isset($ERR)) {
        displayBox('err',$ERR);
      }

      else if (isset($MSG)) {
        displayBox('info',$MSG);

    ?>

     <script type="text/javascript">

        setInterval(function() {

          var ret = $.ajax({
	                url:		'/piphone/async/getBridgeStatus.php?uuid=<?= $bridge_id ?>',
	                type:		'GET',
	                cache:		true,
	                async:	         false});

  
          //$('#debug').html(ret.responseText);
          if ( ret.responseText == 'FINISHED' ) {

             $('#displaybox').hide();
             $('#retbox').show();             

          }

        }, 1000);       


     </script>

    <?php
      }

    ?>

  </body>

</html>
