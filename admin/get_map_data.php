<?php

	$DOCUMENT_ROOT = dirname(__FILE__);

	$tools_dir = $DOCUMENT_ROOT . "/tools/";
	include_once($tools_dir . "connect.php");
	include_once($tools_dir . "sql.php");
  	include_once($tools_dir . "log.php");
	$dblk = connect();
  	error_reporting(0);

	if(isset($_POST['area']) && isset($_POST['level'])) 
	{
		$area = mysql_real_escape_string($_POST['area']);
		$level = mysql_real_escape_string($_POST['level']);

	  	$map_data = sql("SELECT 
						 	X(center) AS center_lat,
						 	Y(center) AS center_lng,
						 	overlay_path,
						 	min_zoom,
						 	X(bound_sw) AS bound_sw_lat,
						 	Y(bound_sw) AS bound_sw_lng,
						 	X(bound_ne) AS bound_ne_lat,
						 	Y(bound_ne) AS bound_ne_lng,
						 	area,
						 	level
						 FROM map WHERE area = $area AND level = $level");

  	while ($row = mysql_fetch_assoc($map_data)) {
  		$center_lat = $row['center_lat'];
		$center_lng = $row['center_lng'];
		$overlay_path = $row['overlay_path'];
		$min_zoom = $row['min_zoom'];
		$bound_sw_lat = $row['bound_sw_lat'];
		$bound_sw_lng = $row['bound_sw_lng'];
		$bound_ne_lat = $row['bound_ne_lat'];
		$bound_ne_lng = $row['bound_ne_lng'];
		$area = $row['area'];
		$level = $row['level'];
  	}
      echo (json_encode(array(
          'map_data'=> array(
              'center_lat' => $center_lat,
              'center_lng' => $center_lng,
              'overlay_path' => $overlay_path,
              'min_zoom' => $min_zoom,
              'bound_sw_lat' => $bound_sw_lat,
              'bound_sw_lng' => $bound_sw_lng,
              'bound_ne_lat' => $bound_ne_lat,
              'bound_ne_lng' => $bound_ne_lng,
              'area' => $area,
              'level' => $level              
          )
      )));
  };
?>