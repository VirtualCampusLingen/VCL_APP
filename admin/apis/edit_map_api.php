<?php

$DOCUMENT_ROOT = dirname(__FILE__).'/..';

$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
$dblk = connect();

// Action: saveNeighbour [POST]
if(isset($_POST['saveNeighbour'])){
  // DROP all Neighbour Connections
  $photoId = mysql_escape_string($_POST['saveNeighboursFor']['photoId']);
  sql("DELETE FROM photo_neighbour WHERE photo_id = $photoId ");
  // Iterate through given Neighbours
  foreach ($_POST['saveNeighboursFor']['neighbours'] as $neighbourId => $heading) {
    // Save Neighbour Connection
    $neighbourId = mysql_escape_string($neighbourId);
    $heading = mysql_escape_string($heading);
    sql("INSERT INTO photo_neighbour (photo_id, neighbour_id, heading) VALUES ($photoId, $neighbourId, $heading)");
  }
}
?>