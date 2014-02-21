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

include '_boilerplate.html';

//display notifications
foreach ($notifications as $type => $notfiy_array) {
  foreach($notfiy_array as $msg){
    echo "<script>window.onload = function(){setFlash('".$type."','".$msg."')};</script>";
  }
}

?>

<script>
  function toggleEditRow(info_id,key){
    if($('#infotext_row_edit_'+key).is(':visible')){
      $("#infotext_row_"+key).css('display','table-row');
      $("#infotext_row_edit_"+key).css('display','none');
    }
    else{
      $("#infotext_row_edit_"+key).css('display','table-row');
      $("#infotext_row_"+key).css('display','none');
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

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container" >
    <h2>Infotexte verwalten</h2>
      </div>
    </div>
    <div class="container">
    <table width="100%" cellspacing="0" cellpadding="5" class="table table-striped table-hover">
      <thead>
        <tr>
          <th width="30%" >Name</th>
          <th width="50%"></th>
          <th width="20%" ></th>
        </tr>
      </thead>
      <tbody>
        <?php
          if(isset($infotext) && $infotext != null ){
            foreach($infotext as $key => $value){
              $new_infotext1 = "";
              $new_infotext2 = "";

              if($value["title"] == 'new infotext'){
                $new_infotext1 = "style='display:none;' ";
                $new_infotext2 = "style='display:table-row;' ";
                $value["title"] = "";
              }
              $text_kurz = substr($value["text"],0,50);
              echo("
                <tr id='infotext_row_".$key."' ".$new_infotext1.">
                  <td><strong>".htmlspecialchars($value["title"])."</strong></td>
                  <td>".$text_kurz."</td>
                  <td>
                  <button class='btn btn-info btn-xs' onclick='toggleEditRow(".$value["infotext_id"].",".$key.")'>Bearbeiten</button>
                  <button class='btn btn-danger btn-xs' onclick='deleteinfotext(".$value["infotext_id"].",".$key.")'>Löschen</button>
                  </td>
                </tr>");
              echo ("
                <tr id='infotext_row_edit_".$key."' class='edit_row_toggle' ".$new_infotext2.">
                  <td>
                    <form name='update_infotext_row_".$key."' method='POST' width='100%'>
                      <input type='hidden' name='update_infotext_id' value='".$value["infotext_id"]."'/>
                      <input name='infotext_title' value='".htmlspecialchars($value["title"])."'/>
                  </td>
                  <td>
                      <textarea cols='90' rows='5' name='infotext_text' >".htmlspecialchars($value["text"])."</textarea>
                  </td>
                  <td>
                      <button type='submit' class='btn btn-success btn-xs' >Speichern</button>
                      <button class='btn btn-info btn-xs' onclick='toggleEditRow(".$value["infotext_id"].",".$key.")'>Beenden</button>
                    </form>
                  </td>
                </tr>");
            }
          }else{
            ?>
            <tr colspan="2">Keine Infotexte vorhanden!</tr>
            <?php
          }

          ?>
                </tbody>
    </table>
    <div>
      <button class="btn btn btn-success" onclick='newinfotext()' >Infotext hinzufügen</button>
    </div>
    <hr>

      <footer>
        <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
