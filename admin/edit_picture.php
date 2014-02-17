<?php
//Upload PHP
//include necessary directorys
$DOCUMENT_ROOT = dirname(__FILE__);
$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
$dblk = connect();


$notifications = array("success" => array(),  "error" => array(), "warning" => array());

//Photo Upload
if(isset($_POST['Upload'])){
  $description = mysql_real_escape_string($_POST['description']);
  $name = mysql_real_escape_string($_POST['name']);
  if($panorama_path = uploadPhoto())
  {
    $res = sql("INSERT INTO panorama (name, description, panorama_path, uploaded_at)
                VALUES('$name', '$description', '$panorama_path', NOW())");
    respondeToSql($res);
  }
}

//Update Panorama
if(isset($_POST['update_panorama'])){
  $panorama_id = mysql_real_escape_string($_POST['update_panorama']);
  $name = mysql_real_escape_string($_POST['name']);
  $description = mysql_real_escape_string($_POST['photo_description']);

  if($panorama_path = uploadPhoto()) $res = sql("UPDATE panorama SET panorama_path = '$panorama_path' WHERE panorama_id = $panorama_id");
  else if(!empty($name) || !empty($description))
  {
    $res = sql("UPDATE panorama SET name = '$name', description = '$description' WHERE panorama_id = $panorama_id");
    respondeToSql($res);
  }
}

//Delete Panorama
if (isset($_POST['delete_panorama'])){
  $del_panorama_id = mysql_real_escape_string($_POST['delete_panorama']);
  $res = sql("DELETE FROM panorama WHERE panorama_id = $del_panorama_id");
  respondeToSql($res);
}

//All Panoramas
$photos = sql("SELECT * FROM panorama");
while($row = mysql_fetch_assoc($photos)){
  $index = $row["panorama_id"];
  $photo[$index]["panorama_id"] = $row["panorama_id"];
  $photo[$index]["name"] = $row["name"];
  $photo[$index]["description"] = $row["description"];
  $photo[$index]["uploaded_at"] = $row["uploaded_at"];
  $photo[$index]["panorama_path"] = $row["panorama_path"];
}

function uploadPhoto()
{
  global $notifications;
  if(empty($_FILES['fileToUpload'])) return false;
  if ($_FILES['fileToUpload']['error'] > 0)
  {
    switch ($_FILES['fileToUpload']['error']) {
      case '1':
        $error = "Foto ist zu groß";
        break;
      case '3':
        $error = "Foto konnte nicht vollständig hochgeladen werden";
        break;
      case '4':
        $error = "Kein Foto ausgewählt";
        break;
      case '7':
        $error = "Foto konnte nicht gespeichert werden";
        break;
      default:
        $error = "Unbekannter Fehler beim Foto upload. Fehlercode: ".$_FILES['fileToUpload']['error'];
        break;
    }
    array_push($notifications["error"], $error);
    return false;
  }else
  {
    // array of valid extensions
    $validExtensions = array('.jpg', '.jpeg', '.gif', '.png');
      // get extension of the uploaded file
    $fileExtension = strrchr($_FILES['fileToUpload']['name'], ".");
    // check if file Extension is on the list of allowed ones
    if (in_array($fileExtension, $validExtensions))
    {
      //we are renaming the file so we can upload files with the same name
      $newName = $_FILES['fileToUpload']['name'];
      $panorama_path = 'assets/img_360/' . $newName;
      $doubledPath = sql("SELECT * FROM panorama WHERE panorama_path = '$panorama_path'");

      if(mysql_num_rows($doubledPath) > 0) array_push($notifications["error"], "Es wurde bereits in Panorama mit diesem Dateinamen hochgeladen");
      else if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $panorama_path)) 
      {
        chmod($panorama_path, 0644);
        return $panorama_path;
      }
    }
    else
    {
      //echo 'Bitte wählen Sie ein Bild! (.jpg, .jpeg, .gif, .png)';
      array_push($notifications["error"], "Kein Foto wurde ausgewählt (.jpg, .jpeg, .gif, .png)");
      return false;
    }
  }
  return false;
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
          <link rel="stylesheet" href="assets/css/bootstrap-theme.min.css">
          <link rel="stylesheet" href="assets/css/main.css">
          <script src="assets/js/vendor/jquery-1.10.1.min.js"></script>
          <script src="assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

          <style>
              body {
                  padding-top: 50px;
                  padding-bottom: 20px;
              }
          </style>
      </head>
      <body>
          <!--[if lt IE 7]>
              <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
          <![endif]-->
      <!-- FLASH MSG -->
      <div class='flash'>
        <button type='button' class='close' onclick="$('.flash').hide()">&times;</button>
        <div class='flash_msg'></div>
      </div>

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
              <li><a href="edit_infotext.php">Infotexte</a></li>
              <li class="active"><a href="edit_picture.php">Fotos</a></li>
            <li class="dropdown">
            <a href="edit_map.php" class="dropdown-toggle" data-toggle="dropdown">Übersichtskarten <b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="edit_map.php?area=1">Halle 1/2</a></li>
              <li><a href="edit_map.php?area=2">KE</a></li>
            </ul>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <script>
      //Main JS
      function togglePictureThumb(span_thumb_id){
        $("#picture_thumb_"+span_thumb_id).toggle()
      };
      function toggleEditRow(photo_id){
        $("#photo_row_edit_"+photo_id).toggle()
        $("#photo_row_"+photo_id).toggle()
      };
      function deletePhoto(photo_id){
        $.ajax({
          type: "POST",
          data: {'delete_panorama': photo_id},
          error: function(xhr, status, error) {
            setFlash('error', 'Foto konnte nicht gelöscht werden')
          },
          success: function(data, status, xhr) {
            setFlash('success', 'Foto wurde erfolgreich gelöscht')
            $("#photo_row_"+photo_id).remove()
          }
        });
      };
      function enableUpdatePano(row){
        targetId = $(row).attr("data-target");
        $(".toggleOnPanoUpload").prop("disabled", true);
        $("#"+targetId).prop("disabled", false);
        $("#"+targetId).closest("td[class='hiddenRow']").removeClass("hiddenRow");
        $(row).closest("td").remove();
      }
    </script>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container">
		  <h2>Hochladen & Verwalten von Fotos</h2>
      </div>
    </div>

    <!-- Upload Photo -->
    <div class="container">
      <section>
        <h2>Neues Foto Hochladen</h2>
          <form class="form-inline" role="form" enctype="multipart/form-data" method="post">
            <div class="form-group">
              <label class="sr-only" for="fileToUpload">File</label>
              <input type="file" class="form-control" name="fileToUpload" id="fileToUpload">
            </div>
            <div class="form-group">
              <label class="sr-only" for="name">Name</label>
              <input class="form-control" id="name" name="name" placeholder="Fotoname">
            </div>
            <div class="form-group">
              <label class="sr-only" for="description">Description</label>
              <input class="form-control" id="description" name="description" placeholder="Beschreibung">
            </div>
            <input type="submit" class="btn btn-success" value="Hochladen" name="Upload">
          </form>
      </section>
    </div class="container">

    <!-- List of Photos -->
    <div class="container">
      <section>
      <h2>Liste der hochgeladenen Fotos</h2>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Bild</th>
            <th>Name</th>
            <th>Beschreibung</th>
            <th>Hochgeladen am</th>
            <th>Aktionen</th>
          </tr>
        </thead>
        <tbody>
          <?php
            foreach($photo as $key => $value){
              echo("
                <tr id='photo_row_".$key."'>
                  <td>
                    <button type='button' class='btn btn-info btn-xs' onclick='togglePictureThumb(".$key.")'>
                      <span class='glyphicon glyphicon-picture'></span> anzeigen
                    </button>
                    <span id=picture_thumb_".$key." style='display: none'>
                      <img src='"."/admin/".$value["panorama_path"]."' width='300px' alt='' class='img-thumbnail'>
                    </span>
                  </td>
                  <td id='name'>".htmlspecialchars($value["name"])."</td>
                  <td id='description'>".htmlspecialchars($value["description"])."</td>
                  <td id='uploaded_at'>".htmlspecialchars($value["uploaded_at"])."</td>
                  <td>
                  <button type='button' class='btn btn-xs btn-info' onclick='toggleEditRow(".$key.")'>Bearbeiten</button>
                  <button type='button' class='btn btn-xs btn-danger' onclick='deletePhoto(".$key.")'>Löschen</button>
                  </td>
                </tr>
                <tr id='photo_row_edit_".$key."' class='edit_row_toggle'>
                  <form name='update_photo_row_".$key."' role='form' enctype='multipart/form-data' method='POST'>
                    <input type='hidden' name='update_panorama' value='".$key."'></input>
                    <td><button type='button' onclick='enableUpdatePano(this)' data-target='uploadPanoUpdate_".$key."' class='btn btn-primary btn-xs'>Panorama ändern</button></td>
                    <td class='hiddenRow'><input type='file' name='fileToUpload' id='uploadPanoUpdate_".$key."' disabled/></td>
                    <td><input class='toggleOnPanoUpload' name='name' value='".htmlspecialchars($value["name"])."'></input></td>
                    <td><input class='toggleOnPanoUpload' name='photo_description' value='".htmlspecialchars($value["description"])."'></input></td>
                    <td><span>".htmlspecialchars($value["uploaded_at"])."</span></td>
                    <td><button type='submit' class='btn-success btn btn-xs'>aktualisieren</button></td>
                  </form>
                </tr>"
                );
            }
          ?>
        </tbody>
      </table>
      </section>
      
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
