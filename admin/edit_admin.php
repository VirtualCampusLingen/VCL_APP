<?php

/**
 * Basisverzeichnis ohne abschliessendem Slash
 */
$DOCUMENT_ROOT = dirname(__FILE__);

$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
$dblk = connect();

  if(isset($_GET['mail_submit'])){
    //Beide eingaben gesetzt
    if((isset($_GET['mail_1']) && isset($_GET['mail_2'])) && ("" != $_GET['mail_1'] && "" != $_GET['mail_2'])){

      $mail1 = mysql_real_escape_string($_GET['mail_1']);
      $mail2 = mysql_real_escape_string($_GET['mail_2']);
      
      //Sind die Eingaben gleich ?
      if($mail1 == $mail2){
          $ok = sql("UPDATE  `admin` SET  `E_Mail` =  '".$mail1."' WHERE  `AdminID` =1");
          if($ok)
          {
            $success = "Email wurde erfolgreich geändert";
            $mail_admin = $mail1;
          }
      }
      else {
        $error = "Email stimmen nicht überein!";
      }

    }
    else {
      $error = "Es wurden keine Werte für die Email angegeben!";
    }

  }else if(isset($_GET['pw_submit'])){
    //Beide eingaben gesetzt
    if((isset($_GET['pw1']) && isset($_GET['pw2'])) && ("" != $_GET['pw1'] && "" != $_GET['pw2'])){
      $pw1 = mysql_real_escape_string($_GET['pw1']);
      $pw2 = mysql_real_escape_string($_GET['pw2']);
      
      //Sind die Eingaben gleich ?
      if($pw1 == $pw2){
          $ok = sql("UPDATE  `admin` SET  `Passwort` =  '".$pw1."' WHERE  `AdminID` =1");
          if($ok)
          {
            $success = "Passwort wurde erfolgreich geändert";
          }
      }
      else {
        $error = "Passwörter stimmen nicht überein!";
      }

    }
    else {
      $error = "Es wurden keine Werte für das Passwort angegeben!";
    }

  }
  
  if(!isset($mail_admin)){
    $result = sql("SELECT `E_Mail` FROM `admin` WHERE `AdminID` = 1");
    while ($row = mysql_fetch_assoc($result)) {
        $mail_admin = $row['E_Mail'];
    }
  }


?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="assets/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="assets/css/main.css">

        <script src="assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
 <a class="navbar-brand" href="index.html"><img src="assets/img/Logo.png" width="37" height="20" alt="home"/></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.html">Home</a></li>
            <li class="active"><a href="edit_admin.php">Administration</a></li>
            <li><a href="edit_infotext.php">Infotexte</a></li>
			<li><a href="edit_picture.php">Fotos</a></li>
			<li class="dropdown">
        <a href="edit_admin.php" class="dropdown-toggle" data-toggle="dropdown">Übersichtskarten <b class="caret"></b></a>
         <ul class="dropdown-menu">
          <li><a href="edit_map.php?map_id=1">Halle 1/2</a></li>
          <li><a href="edit_map.php?map_id=2">KE</a></li>
              </ul> 
      </li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container">
		    <h2>Email des Administrators ändern - zurzeit "<?=$mail_admin?>"</h2>
      </div>
    </div>

    <div class="container">
		    <?php if(isset($error)) {?>
        <div style="border:1px solid Red;background-color: #ededed;padding:10px;"><h1><?=$error?><h1></div>
        <?php } ?>
        <?php if(isset($success)) {?>
        <div style="border:1px solid Green;background-color: #ededed;padding:10px;"><h1><?=$success?><h1></div>
        <?php } ?>
    <div>
		
		<form class="navbar-form pull-left">  
		  <b>Neues Email:</b><br/>
		  <input type="text" class="" name="mail_1" placeholder="Email...">
		  <br/>
		  <br/>
		  <b>Neues Email bestätigen:</b><br/>  
		  <input type="text" class="" name="mail_2"placeholder="Email...">
		  <br/>  
		  <br/>
		  <button type="submit" class="btn" name="mail_submit" value="edit">Ändern</button>  
		</form>
		<br style="clear:both;"/>
    <form class="navbar-form pull-left">  
      <b>Neues Passwort:</b><br/>
      <input type="text" class="" name="pw1" placeholder="Passwort...">
      <br/>
      <br/>
      <b>Neues Passwort bestätigen:</b><br/>  
      <input type="text" class="" name="pw2" placeholder="Passwort...">
      <br/>  
      <br/>
      <button type="submit" class="btn" name="pw_submit" value="edit">Ändern</button>  
    </form>
    <br style="clear:both;"/>
		</div>

      <hr>

      <footer>
        <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="assets/js/vendor/bootstrap.min.js"></script>

        <script src="assets/js/main.js"></script>

        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script>
    </body>
</html>
