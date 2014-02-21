<?php
$DOCUMENT_ROOT = dirname(__FILE__);
$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
$dblk = connect();

$notifications = array("success" => array(),  "error" => array(), "warning" => array());

//Update poi
if(isset($_POST['update_poi_id'])){

  $p_id = mysql_real_escape_string($_POST['update_poi_id']);
  $p_name = mysql_real_escape_string($_POST['poi_name']);
  $p_description = mysql_real_escape_string($_POST['poi_description']);
  $p_poi_panorama = mysql_real_escape_string($_POST['poi_panorama']);
  $res = sql("UPDATE poi SET name = '$p_name', description = '$p_description', panorama = $p_poi_panorama WHERE poi_id = $p_id");
  respondeToSql($res);
}

//Delete poi
if (isset($_POST['delete_poi'])){
  $del_poi_id = mysql_real_escape_string($_POST['delete_poi']);
  $res = sql("DELETE FROM poi WHERE poi_id = $del_poi_id");
  respondeToSql($res);
}

// New poi
if(isset($_POST['new_poi'])){
  $poi_name = mysql_real_escape_string($_POST['poi_name']);
  $poi_description = mysql_real_escape_string($_POST['poi_description']);
  $p_poi_panorama = mysql_real_escape_string($_POST['poi_panorama']);
  $res = sql("INSERT INTO poi (name, description, panorama) VALUES ('$poi_name', '$poi_description', '$p_poi_panorama')");
  respondeToSql($res);
}

//List of all
$poi_sql = sql("SELECT * FROM poi ORDER BY  poi_id ASC");
$i = 0;
while($row = mysql_fetch_assoc($poi_sql)){
  $index = $i;
  $poi[$index]["poi_id"] = $row["poi_id"];
  $poi[$index]["name"] = $row["name"];
  $poi[$index]["description"] = $row["description"];
  $poi[$index]["panorama"]= $row["panorama"];
  $i++;
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
  function toggleEditRow(poi_id, key){
    if($('#poi_row_edit_'+key).is(':visible')){
      $("#poi_row_"+key).css('display','table-row');
      $("#poi_row_edit_"+key).css('display','none');
    }
    else{
      $("#poi_row_edit_"+key).css('display','table-row');
      $("#poi_row_"+key).css('display','none');
      var selectedOption = $("#pano_select_"+key).data("selected");
      $("#pano_select_"+key+" option[value="+selectedOption+"]").attr("selected", true);
    }
  };

  function deletePoi(poi_id, key){
    $.confirm({
      text: "Sind sie sicher, dass Sie dieses Markierung löschen wollen?",
      title: "Löschen bestätigen",
      confirmButton: "Ja, löschen",
      cancelButton: "Nein, abbrechen",
      confirm: function(button) {
        $.ajax({
          type: "POST",
          data: {'delete_poi': poi_id},
          error: function(xhr, status, error) {
            //setFlash('error', 'POI konnte nicht gelöscht werden')
          },
          success: function(data, status, xhr) {
            //setFlash('success', 'POI wurde erfolgreich gelöscht')
            $("#poi_row_"+key).remove()
          }
        });
      }
    });
  };

  $("window").load(function(){
    if($("#pano_select_new :selected").is(":empty")) $("#newPoiSubmit").prop("disabled", true);
  });
</script>

 <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container" >
    <h2>Interessante Orte verwalten</h2>
      </div>
    </div>
    <div class="container">
    <table width="100%" cellspacing="0" cellpadding="5" class="table table-striped table-hover">
      <thead>
        <tr>
          <th width="30%" >Name</th>
          <th width="40%">Beschreibung</th>
          <th width="10%">Zugehöriges Panorama</th>
          <th width="20%" >Aktionen</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $panoSelectOptions = "";
          $panoramas = sql("SELECT * FROM panorama");
          while($row = mysql_fetch_assoc($panoramas)){
            $panoList[$row['panorama_id']] = $row['name'];
            $panoSelectOptions .= "<option value = '".$row['panorama_id']."' >".$row['name']."</option>";
          }
          if(isset($poi) && $poi != null ){
            foreach($poi as $key => $value){
              foreach ($panoList as $panoId => $panoName) {
                if( $panoId == $value['panorama']){
                  $respPanoName = $panoName;
                  $respPanoId = $panoId;
                }
              }

              echo("
                <tr id='poi_row_".$key."'>
                  <td><strong>".htmlspecialchars($value["name"])."</strong></td>
                  <td>".htmlspecialchars($value["description"])."</td>
                  <td>".$respPanoName."</td>
                  <td>
                  <button class='btn btn-info btn-xs' onclick='toggleEditRow(".$value["poi_id"].",".$key.")'>Bearbeiten</button>
                  <button class='btn btn-danger btn-xs' onclick='deletePoi(".$value["poi_id"].",".$key.")'>Löschen</button>
                  </td>
                </tr>");
              echo ("
                <tr id='poi_row_edit_".$key."' class='edit_row_toggle'>
                  <td>
                    <form name='update_poi_row_".$key."' method='POST' width='100%'>
                      <input type='hidden' name='update_poi_id' value='".$value["poi_id"]."'/>
                      <input name='poi_name' value='".htmlspecialchars($value["name"])."'/>
                  </td>
                  <td>
                    <textarea cols='70' rows='1' name='poi_description' >".htmlspecialchars($value["description"])."</textarea>
                  </td>
                  <td>
                    <select id='pano_select_".$key."' name='poi_panorama' data-selected='".$respPanoId."'>
                      ".$panoSelectOptions."
                    </select>
                  </td>
                  <td>
                      <button type='submit' class='btn btn-success btn-xs' >Speichern</button>
                      <button class='btn btn-info btn-xs' onclick='toggleEditRow(".$value["poi_id"].",".$key.")'>Beenden</button>
                    </form>
                  </td>
                </tr>");
            }
          }
          echo("
              <tr>
                <form method='POST' width='100%'>
                <td><input type='hidden' name='new_poi'/>
                <input name='poi_name'/></td>
                <td><input name='poi_description'/></td>
                <td>
                  <select id='pano_select_new' name='poi_panorama'>
                    ".$panoSelectOptions."
                  </select>
                </td>
                <td><button id='newPoiSubmit' type='submit' class='btn btn-xs btn-success'>Interessanten Punkt hinzufügen</button></td>
                </form>
              </tr>
            ");
          ?>
        </tbody>
    </table>
    <hr>
      <footer>
        <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container
    </body>
</html>