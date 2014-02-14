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
	  						m.area,
	  						m.level,
						 	X(a.center) AS center_lat,
						 	Y(a.center) AS center_lng,
						 	a.min_zoom,
						 	X(a.bound_sw) AS bound_sw_lat,
						 	Y(a.bound_sw) AS bound_sw_lng,
						 	X(a.bound_ne) AS bound_ne_lat,
						 	Y(a.bound_ne) AS bound_ne_lng,
						 	o.overlay_path,
						 	X(o.bound_sw) AS overlay_sw_lat,
						 	Y(o.bound_sw) AS overlay_sw_lng,
						 	X(o.bound_ne) AS overlay_ne_lat,
						 	Y(o.bound_ne) AS overlay_ne_lng
						 FROM map AS m
						 JOIN area AS a ON m.area = a.area_id
						 JOIN overlay AS o ON m.overlay = o.overlay_id
						 WHERE area = $area AND level = $level");

  	while ($row = mysql_fetch_assoc($map_data)) {
  		$area = $row['area'];
		$level = $row['level'];
  		$center_lat = $row['center_lat'];
		$center_lng = $row['center_lng'];
		$min_zoom = $row['min_zoom'];
		$bound_sw_lat = $row['bound_sw_lat'];
		$bound_sw_lng = $row['bound_sw_lng'];
		$bound_ne_lat = $row['bound_ne_lat'];
		$bound_ne_lng = $row['bound_ne_lng'];
		$overlay_path = $row['overlay_path'];
		$overlay_sw_lat = $row['overlay_sw_lat'];
		$overlay_sw_lng = $row['overlay_sw_lng'];
		$overlay_ne_lat = $row['overlay_ne_lat'];
		$overlay_ne_lng = $row['overlay_ne_lng'];
  	}
      echo (json_encode(array(
          'map_data'=> array(
              'area' => $area,
              'level' => $level,
              'center_lat' => $center_lat,
              'center_lng' => $center_lng,
              'min_zoom' => $min_zoom,
              'bound_sw_lat' => $bound_sw_lat,
              'bound_sw_lng' => $bound_sw_lng,
              'bound_ne_lat' => $bound_ne_lat,
              'bound_ne_lng' => $bound_ne_lng,
              'overlay_path' => $overlay_path,  
              'overlay_sw_lat' => $overlay_sw_lat,
              'overlay_sw_lng' => $overlay_sw_lng,
              'overlay_ne_lat' => $overlay_ne_lat,
              'overlay_ne_lng' => $overlay_ne_lng       
          )
      )));
  };
?>