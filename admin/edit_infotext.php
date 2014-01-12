<?php
$DOCUMENT_ROOT = dirname(__FILE__);
$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
$dblk = connect();

$notifications = array("success" => array(),  "error" => array(), "warning" => array());

//List of all
$infotext_sql = sql("SELECT * FROM infotext ORDER BY  `infotext`.`infoTextID` ASC");
$i = 0;
while($row = mysql_fetch_assoc($infotext_sql)){
  $index = $i;
  $infotext[$index]["text"] = $row["text"];
  $infotext[$index]["name"] = $row["name"];
  $infotext[$index]["date_of_update"] = $row["date_of_update"];
  $i++;
}

//Update infotext
if(isset($_POST['update_infotext_id'])){
  $p_id = mysql_real_escape_string($_POST['update_infotext_id']);
  $p_name = mysql_real_escape_string($_POST['name']);
  $p_description = mysql_real_escape_string($_POST['text']);
  $res = sql("UPDATE infotext SET name='".$p_name."', text='".$p_description."' WHERE infoTextID='".$p_id."'");
  respondeToSql($res);
}

//Delete infotext
if (isset($_POST['delete_infotext'])){
  $del_infotext_id = mysql_real_escape_string($_POST['delete_infotext']);
  $res = sql("DELETE FROM infotext WHERE infoTextID='".$$del_infotext_id."'");
  respondeToSql($res);
}


function respondeToSql($sql_statement){
  global $notifications;
  if(!$sql_statement){
    //internal server error
    array_push($notifications["error"], "Ein Fehler ist aufgetreten");
    http_response_code(500);
  }else if(mysql_affected_rows() == 0){
    //no row affected
    array_push($notifications["warning"], "Keine Änderungen vorgenommen");
    http_response_code(304);
  }
  else{  
    //sql success
    array_push($notifications["success"], "Erfolgreich");
    http_response_code(200);
  }
}

//display notifications
foreach ($notifications as $type => $notfiy_array) {
  foreach($notfiy_array as $msg){
    echo "<script>window.onload = function(){setFlash('".$type."','".$msg."')};</script>";
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
		
		<script>
		      function toggleEditRow(photo_id){
				$("#infotext_row_edit_"+photo_id).toggle()
				$("#infotext_row_"+photo_id).toggle()
			  };
		</script>
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
            <li><a href="edit_admin.php">Administration</a></li>
            <li class="active"><a href="edit_infotext.php">Infotexte</a></li>
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

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container" >
		<h2>Infotexte verwalten</h2>
      </div>
    </div>
    <div class="container">
		<table>
		    <?php
            foreach($infotext as $key => $value){
              echo("
                <tr id='infotext_row_".$key."'>
                  <td id='infotext_name'><strong>".htmlspecialchars($value["name"])."</strong></td>
                  <td id='uploaded_at'>".htmlspecialchars($value["date_of_update"])."</td>
                  <td>
                  <span class='glyphicon glyphicon-edit pointer' onclick='toggleEditRow(".$key.")'></span>
                  ||
                  <span class='glyphicon glyphicon-trash pointer' onclick='deleteinfotext(".$key.")'></span>
                  </td>
                </tr>
				<tr id='infotext_row_edit_".$key."' class='edit_row_toggle'>
                  <form name='update_infotext_row_".$key."' method='POST'>
                    <input type='hidden' name='update_infotext_id' value='".$key."'></input>
                    <td><span>ändern</span></td>
                    <td><input name='infotext_name' value='".htmlspecialchars($value["name"])."'></input></td>
                    <td><textarea name='infotext_text' >".htmlspecialchars($value["text"])."</textarea></td>
                    <td><button type='submit' class='btn-success btn btn-xs'>aktualisieren</button></td>
                  </form>
                </tr>
				"
                );
            }
          ?>
		</table>
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
