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


  	$sql1 = sql("SELECT `PhotoID`,`photo_name`,`path` FROM `photo` WHERE `PhotoID` = '".$id."'");

  	while ($row = mysql_fetch_assoc($sql1)) {
  		$id = $row['PhotoID'];
  		$photo_name = $row['photo_name'];
  		$path = "admin/".$row['path'];

  		$sql2 = sql("SELECT *  FROM `photo_neighbour` INNER JOIN `photo` ON `neighbour_id`=`PhotoID` WHERE `photo_id` = '".$id."' ");
  		$neighbours = array();
      $i = 0;
      while ($row2 = mysql_fetch_assoc($sql2)) {
        $neighbours[$i] = array('neighbour_id'=>$row2['neighbour_id'],'heading' => $row2['heading'],'description'=>"",'path'=> $row2['path']);
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

  if( !empty($_GET['photo_id']) ){
    $photo_id = mysql_real_escape_string($_GET['photo_id']);
    $neighbours_array = $_GET['neighbours'];
    //Delte all relations for specific photo
    sql("DELETE FROM photo_neighbour WHERE photo_id='".$photo_id."'");

    $Pa = sql("SELECT * FROM photo where PhotoID='".$photo_id."'");
    while ($row = mysql_fetch_assoc($Pa)) {
       $PaMapId = $row['map_id'];
       $PaX = $row['x_position'];
       $PaY = $row['y_position'];
    }

    foreach($neighbours_array as $value) {
      $PbMapId = sql("SELECT * FROM photo where PhotoID='".$value."'");
      while ($row = mysql_fetch_array($PbMapId)){
        $PbMapId = $row['map_id'];
      }

      if($PaMapId == $PbMapId) $Pb = sql("SELECT x_position, y_position FROM photo WHERE PhotoID='".$value."' ");
      else $Pb = sql("SELECT x_pos_on_map AS x_position, y_pos_on_map AS y_position FROM map_enterings WHERE map_id='".$PaMapId."' AND entered_photo_id='".$value."' ");

      while ($row = mysql_fetch_assoc($Pb)) {
        $PbX = $row['x_position'];
        $PbY = $row['y_position'];
      }
      
      if($PbY <= $PaY){
        if($PbX >= $PaX){ $ref = 360; $calcuate = "-"; }
        else { $ref = 180 ; $calcuate = "+"; }
      }else {
        if($PbX >= $PaX){ $ref = 0; $calcuate = "+";}
        else{ $ref = 180 ; $calcuate = "-";}
      }
      $a = abs($PbX - $PaX);
      $b = abs($PbY - $PaY);
      $c = sqrt(pow($a, 2)+pow($b, 2));
      //alpha = arccos(a/c)
      $alpha = rad2deg(acos($a/$c));
      //heading = ref calcu_method aplha
      $heading = $calcuate == "+" ? $ref + $alpha : $ref - $alpha ;
      $heading = intval($heading);
      //Insert
      sql("INSERT INTO photo_neighbour (photo_id, neighbour_id, heading) VALUES ('".$photo_id."', '".$value."', '".$heading."')");
    }
  };

?>