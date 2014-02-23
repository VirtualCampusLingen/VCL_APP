<?php
  $DOCUMENT_ROOT = dirname(__FILE__).'/..';

  $tools_dir = $DOCUMENT_ROOT . "/tools/";
  include_once($tools_dir . "connect.php");
  include_once($tools_dir . "sql.php");
  include_once($tools_dir . "log.php");
  $dblk = connect();

  // Set start Pano
  if(isset($_POST['set_start_pano']))
  {
    $start_pano = mysql_real_escape_string($_POST['set_start_pano']);
    sql("DELETE FROM start_pano");
    sql("INSERT INTO start_pano VALUES ($start_pano)");
  }

  // Get start Pano
  if(isset($_GET['get_start_pano']))
  {
    $res = sql("SELECT * FROM start_pano");
    while($row = mysql_fetch_assoc($res)){
      echo json_encode(array(
        'start_pano' => $row['panorama']
      ));
    }
  }
?>