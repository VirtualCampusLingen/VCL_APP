<?php
$DOCUMENT_ROOT = dirname(__FILE__);
$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
$dblk = connect();

$notifications = array("success" => array(),  "error" => array(), "warning" => array());

//Update infotext
if(isset($_POST['update_infotext_id'])){
  $p_id = mysql_real_escape_string($_POST['update_infotext_id']);
  $p_title = mysql_real_escape_string($_POST['infotext_title']);
  $p_description = mysql_real_escape_string($_POST['infotext_text']);
  $res = sql("UPDATE infotext SET title='".$p_title."', text='".$p_description."' WHERE infotext_id='".$p_id."'");
  respondeToSql($res);
}


//List of all
$infotext_sql = sql("SELECT * FROM infotext ORDER BY  infotext_id ASC");
$i = 0;
while($row = mysql_fetch_assoc($infotext_sql)){
  $index = $i;
  $infotext[$index]["text"] = $row["text"];
  $infotext[$index]["title"] = $row["title"];
  $infotext[$index]["infotext_id"]= $row["infotext_id"];
  $i++;
}


//Delete infotext
if (isset($_POST['delete_infotext'])){
  $del_infotext_id = mysql_real_escape_string($_POST['delete_infotext']);
  $res = sql("DELETE FROM infotext WHERE infotext_id='".$del_infotext_id."'");
  respondeToSql($res);
}

//New Infotext
if(isset($_POST['new_infotext'])){
  //Neuen Infotext mit der letzten ID anlegen
  $res = sql("INSERT INTO `infotext` (`infotext_id` , `text` , `title`) VALUES ( NULL ,  '',  'new infotext')");
  respondeToSql($res);
}

function respondeToSql($sql_statement){
  global $notifications;
  if(!$sql_statement){
    //internal server error
    array_push($notifications["error"], "Ein Fehler ist aufgetreten");
    // http_response_code(500);
  }else if(mysql_affected_rows() == 0){
    //no row affected
    array_push($notifications["warning"], "Keine Änderungen vorgenommen");
    // http_response_code(304);
  }
  else{  
    //sql success
    array_push($notifications["success"], "Erfolgreich");
    // http_response_code(200);
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

        <script src="assets/js/vendor/jquery-1.10.1.min.js"></script>
        <script src="assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		
		<script>
		    
        function toggleEditRow(info_id,key){
          if($('#infotext_row_edit_'+key).is(':visible')){
            $('#infotext_row_edit_'+key).toggle();
          }
          else{
            $('.edit_row_toggle').css('display','none');
            $('#infotext_row_edit_'+key).toggle();
          }
			  };

        function deleteinfotext(info_id,key){
          $.ajax({
            type: "POST",
            data: {'delete_infotext': info_id},
            error: function(xhr, status, error) {
              //setFlash('error', 'Infotext konnte nicht gelöscht werden')
            },
            success: function(data, status, xhr) {
              //setFlash('success', 'Infotext wurde erfolgreich gelöscht')
              $("#infotext_row_"+key).remove()
            }
          });
        };

        function newinfotext(){
          $.ajax({
            type: "POST",
            data: {'new_infotext':1},
            error: function(xhr, status, error) {
              //setFlash('error', 'Infotext konnte nicht angelegt werden');
            },
            success: function(data, status, xhr) {
              //setFlash('success', 'Infotext wurde erfolgreich erstellt');
              window.location.reload();
            }
          });
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
            <li class="active"><a href="edit_infotext.php">Infotexte</a></li>
			<li><a href="edit_picture.php">Fotos</a></li>
			<li class="dropdown">
        <a href="edit_map.php" class="dropdown-toggle" data-toggle="dropdown">Übersichtskarten <b class="caret"></b></a>
         <ul class="dropdown-menu">
          <li><a href="edit_map.php?area=1">Halle 1/2</a></li>
          <li><a href="edit_map.php?area=2">KE</a></li>
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
		<table width="100%" cellspacing="0" cellpadding="5">
        <tr>
          <th width="80%" >Name</th>
          <th width="20%" >Edit</th>
        </tr>
		    <?php
          if(isset($infotext) && $infotext != null ){
            foreach($infotext as $key => $value){

              if($key%2==0){
                $class = 'infotext_row_gray';
              } else{
                $class = 'infotext_row_white';
              }

              echo("
                <tr id='infotext_row_".$key."' class='".$class."'>
                  <td id='infotext_title'><strong>".htmlspecialchars($value["title"])."</strong></td>
                  <td>
                  <span class='glyphicon glyphicon-edit pointer' onclick='toggleEditRow(".$value["infotext_id"].",".$key.")'></span>
                  ||
                  <span class='glyphicon glyphicon-trash pointer' onclick='deleteinfotext(".$value["infotext_id"].",".$key.")'></span>
                  </td>
                </tr>
                <tr id='infotext_row_edit_".$key."' class='edit_row_toggle'>
                  <td colspan='2'>
                  <table width='100%'>
                  <tr>
                    <th>Titel bearbeiten:</th>
                    <th colspan='2'>Text bearbeiten:</th>
                  </tr>
                  <tr>
                  <form name='update_infotext_row_".$key."' method='POST' width='100%'>
                    <td valign='top' width='20%'><input type='hidden' name='update_infotext_id' value='".$value["infotext_id"]."'></input><input name='infotext_title' value='".htmlspecialchars($value["title"])."'></input></td>
                    <td valign='top' width='60%'><textarea cols='50' rows='10' name='infotext_text' >".htmlspecialchars($value["text"])."</textarea></td>
                    <td valign='bottom' width='20%'><button type='submit' class='btn-success btn btn-xs'>aktualisieren</button></td>
                  </form>
                  </tr>
                  </table>
                  </td>
                </tr>");
            }
          }else{
            ?>
            <tr colspan="2">Keine Infotexte vorhanden!</tr>
            <?php
          }
            
          ?>
          <tr>
            <td colspan="2"><button class="btn" onclick='newinfotext()' >Infotext hinzufügen</button></td>
          </tr>
		</table>
	  <hr>

      <footer>
        <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
