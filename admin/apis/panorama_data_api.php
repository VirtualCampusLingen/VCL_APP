<?php
	$DOCUMENT_ROOT = dirname(__FILE__).'/..';

	$tools_dir = $DOCUMENT_ROOT . "/tools/";
	include_once($tools_dir . "connect.php");
	include_once($tools_dir . "sql.php");
  include_once($tools_dir . "log.php");
	$dblk = connect();
  error_reporting(0);

	if( !empty($_GET['id']) ) {
		$id = mysql_real_escape_string($_GET['id']);


  	$sql1 = sql("SELECT panorama_id, X(position) AS position_lat, Y(position) AS position_lng , name, panorama_path FROM panorama WHERE panorama_id = $id");

  	while ($row = mysql_fetch_assoc($sql1)) {
  		$id = $row['panorama_id'];
		$position_lat = $row['position_lat'];
		$position_lng = $row['position_lng'];
  		$photo_name = $row['name'];
  		$path = $row['panorama_path'];

  		$sql2 = sql("SELECT *  FROM neighbour AS n INNER JOIN panorama AS p ON n.neighbour = p.panorama_id WHERE n.panorama = $id");
  		$neighbours = array();
      	$i = 0;
      	while ($row2 = mysql_fetch_assoc($sql2)) {
        $neighbours[$i] = array('neighbour_id'=>$row2['neighbour'],'heading' => $row2['heading'],'description'=>"",'path'=> $row2['panorama_path']);
        $i++;
	    }

      $pano_infotext = sql("SELECT it.*  FROM panorama AS p INNER JOIN infotext_panorama AS ip ON p.panorama_id = ip.panorama INNER JOIN infotext AS it ON ip.infotext = it.infotext_id WHERE p.panorama_id = $id");
      $info_texts = array();
      $i = 0;
      while ($row3 = mysql_fetch_assoc($pano_infotext)) {
        $info_texts[$i] = array('infotext_id'=>$row3['infotext_id'], 'infotext_title'=>$row3['title'],'infotext_text' => $row3['text']);
        $i++;
      }
  	}
      echo (json_encode(array(
          'Panoid'=> array(
              'path' => $path,
              'position_lat' => $position_lat,
              'position_lng' => $position_lng,
              'description' => $photo_name,
              'id' => $id,
              'neighbours' => $neighbours,
              'info_texts' => $info_texts
          )
      )));
  };
?>