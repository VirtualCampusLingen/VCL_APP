<?php
//Upload PHP
//include necessary directorys
$DOCUMENT_ROOT = dirname(__FILE__);
$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
include_once($tools_dir . "header.php");
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

  if($_FILES){
    $panorama_path = uploadPhoto();
    $res = sql("UPDATE panorama SET panorama_path = '$panorama_path' WHERE panorama_id = $panorama_id");
  }
  else if(!empty($name) || !empty($description)) $res = sql("UPDATE panorama SET name = '$name', description = '$description' WHERE panorama_id = $panorama_id");
  respondeToSql($res);
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
  $validExtensions = array('.jpg', '.jpeg', '.gif', '.png');

  $i = 0;
  foreach ($_FILES as $array_value => $value_array) {
    foreach ($value_array as $array_name => $array) {
      foreach ($array as $key => $value) {
        $photo_to_upload[$key][$array_name] .= $value; 
      }
    }
  }
  $date = date('Y-m-t-h-i-s');
  $panorama_path = 'admin/assets/img_360/pano_'.$date;
  $oldmask = umask(0);
  mkdir('assets/img_360/pano_'.$date, 0777);
  umask($oldmask);

  foreach ($photo_to_upload as $key => $value) {
    $fileExtension = strrchr($value['name'], ".");

    if (in_array($fileExtension, $validExtensions)){
      //Ordnernamen aus der ID zusammen bauen 
      $panorama_name = $value['name'];
      if(move_uploaded_file($value['tmp_name'], 'assets/img_360/pano_'.$date.'/'.$value['name'])) 
      {
        $oldmask = umask(0);
        chmod("assets/img_360/pano_".$date."/".$panorama_name, 0777);
        umask($oldmask);
      }
    }
    else{
      //Ist kein Bild mit der vorgegebenen validExtensions 
      die("Leider kein .jpg, .jpeg, .gif oder .png");
      return false;
    }
  }
  return $panorama_path;
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

include '_boilerplate.html';

//display notifications
foreach ($notifications as $type => $notfiy_array) {
  foreach($notfiy_array as $msg){
    echo "<script>window.onload = function(){setFlash('".$type."','".$msg."')};</script>";
  }
}
?>
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
        $.confirm({
          text: "Sind sie sicher, dass Sie dieses Foto löschen wollen?",
          title: "Löschen bestätigen",
          confirmButton: "Ja, löschen",
          cancelButton: "Nein, abbrechen",
          confirm: function(button) {
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
              <fieldset>
                <label class="sr-only" for="fileToUpload">File</label>
                <input type="file" class="form-control" name="fileToUpload[]" id="fileToUpload" multiple="multiple">
              </fieldset>
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
            <th></th>
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
                  <td></td>
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
                    <td class='hiddenRow'><input type='file' name='fileToUpload[]' id='uploadPanoUpdate_".$key."' multiple='multiple' disabled/></td>
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
	  <script>
      var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
      (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
      g.src='//www.google-analytics.com/ga.js';
      s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>
    </body>
</html>
