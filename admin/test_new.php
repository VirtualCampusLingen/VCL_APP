<?php

//TEST
/*
	$arr = array(
		"Panoid" => array(
			  "path" => "pfad",
			  "description" => "name",
			  "id" => "1",
			  "neighbour" => array(
			  	"1" => array(
			  		"neighbour_id" => "2",
			  		"heading" => "90",
			  		"description" => "test"
			  		)
			  )
			)
		);

	echo json_encode($arr);

	echo "<br/>";
*///ENDE

	$DOCUMENT_ROOT = dirname(__FILE__);

	$tools_dir = $DOCUMENT_ROOT . "/tools/";
	include_once($tools_dir . "connect.php");
	include_once($tools_dir . "sql.php");
  include_once($tools_dir . "log.php");
	$dblk = connect();
  error_reporting(0);

	if( !empty($_GET['id']) ) {
		$id = mysql_real_escape_string($_GET['id']);


  	$sql1 = sql("SELECT panorama_id, name, panorama_path FROM panorama WHERE panorama_id = $id");

  	while ($row = mysql_fetch_assoc($sql1)) {
  		$id = $row['panorama_id'];
  		$photo_name = $row['name'];
  		$path = "admin/".$row['panorama_path'];

  		$sql2 = sql("SELECT *  FROM neighbour AS n INNER JOIN panorama AS p ON n.neighbour = p.panorama_id WHERE n.panorama = $id");
  		$neighbours = array();
      $i = 0;
      while ($row2 = mysql_fetch_assoc($sql2)) {
        $neighbours[$i] = array('neighbour_id'=>$row2['neighbour'],'heading' => $row2['heading'],'description'=>"",'path'=> $row2['panorama_path']);
        $i++;
	    }
  	}
      echo (json_encode(array(
          'Panoid'=> array(
              'path' => $path,
              'description' => $photo_name,
              'id' => $id,
              'neighbours' => $neighbours,
              'info_texts' => array(
                'Foo' => 'Bar'
              )
          )
      )));
  };
?>