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
  // Delete straight connections
  sql("DELETE FROM neighbour WHERE panorama = $photoId ");
  // Delete reverted Connection if exists
  sql("DELETE FROM neighbour WHERE neighbour = $photoId");
  // Iterate through given Neighbours
  foreach ($_POST['saveNeighboursFor']['neighbours'] as $neighbourId => $heading) {
    // Save Neighbour Connection
    $neighbourId = mysql_escape_string($neighbourId);
    $heading = mysql_escape_string($heading);
    // Insert reverted Connection
    $revertedHeading = ( $heading + 180 ) % 360;
    sql("INSERT INTO neighbour (panorama, neighbour, heading) VALUES ($neighbourId, $photoId, $revertedHeading)");
    // Save straight connection
    sql("INSERT INTO neighbour (panorama, neighbour, heading) VALUES ($photoId, $neighbourId, $heading)");
  }
}

// Action: updateHeading [POST]
if(isset($_POST['updateHeading'])){
  // escape Parameters
  $photoId = mysql_escape_string($_POST['photoId']);
  $neighbourId = mysql_escape_string($_POST['neighbourId']);
  $heading = mysql_escape_string($_POST['heading']);
  // update DB
  // Heading: photoId -> neihbour
  sql("UPDATE neighbour SET heading = $heading WHERE panorama = $photoId AND neighbour = $neighbourId");
  // revertedHeading: neihbour -> photoId
  $revertedHeading = ( $heading + 180 ) % 360;
  sql("UPDATE neighbour SET heading = $revertedHeading WHERE panorama = $neighbourId AND neighbour = $photoId");
}
?>