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
  $panoramaId = mysql_escape_string($_POST['saveNeighboursFor']['panoramaId']);
  // Delete straight connections
  sql("DELETE FROM neighbour WHERE panorama = $panoramaId ");
  // Delete reverted Connection if exists
  sql("DELETE FROM neighbour WHERE neighbour = $panoramaId");
  // Iterate through given Neighbours
  foreach ($_POST['saveNeighboursFor']['neighbours'] as $neighbourId => $heading) {
    // Save Neighbour Connection
    $neighbourId = mysql_escape_string($neighbourId);
    $heading = mysql_escape_string($heading);
    // Insert reverted Connection
    $revertedHeading = ( $heading + 180 ) % 360;
    sql("INSERT INTO neighbour (panorama, neighbour, heading) VALUES ($neighbourId, $panoramaId, $revertedHeading)");
    // Save straight connection
    sql("INSERT INTO neighbour (panorama, neighbour, heading) VALUES ($panoramaId, $neighbourId, $heading)");
  }
}

// Action: updateHeading [POST]
if(isset($_POST['updateHeading'])){
  // escape Parameters
  $panoramaId = mysql_escape_string($_POST['panoramaId']);
  $neighbourId = mysql_escape_string($_POST['neighbourId']);
  $heading = mysql_escape_string($_POST['heading']);
  // update DB
  // Heading: panoramaId -> neihbour
  sql("UPDATE neighbour SET heading = $heading WHERE panorama = $panoramaId AND neighbour = $neighbourId");
  // revertedHeading: neihbour -> panoramaId
  $revertedHeading = ( $heading + 180 ) % 360;
  sql("UPDATE neighbour SET heading = $revertedHeading WHERE panorama = $neighbourId AND neighbour = $panoramaId");
}
?>