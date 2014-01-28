<?php
$DOCUMENT_ROOT = dirname(__FILE__);

$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
$dblk = connect();

$error = 0;
error_reporting(null);

?>

<?php

if(isset($_POST['latitude']) && isset($_POST['longitude']))
{
	$latitude = mysql_escape_string($_POST['latitude']);
	$longitude = mysql_escape_string($_POST['longitude']);
	sql("UPDATE photo SET x_position = $latitude, y_position = $longitude WHERE PhotoID = 1");
}

if(isset($_GET['photosOnMap']))
{
	$mapId = mysql_escape_string($_GET['photosOnMap']);
	$photos = sql("SELECT * FROM photo WHERE map_id = $mapId");
	
	$photoArray = array();
	$i = 0;
    while ($row = mysql_fetch_assoc($photos)) 
    {
		$photoArray[$i] = array('photoId' => $row['PhotoID'], 'lat' => $row['x_position'], 'lng' => $row['y_position']);
		$i++;
	}
	
	echo json_encode($photoArray);
	die();
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
        <style>
            body {
                padding-top: 50px;
                padding-bottom: 20px;
            }
        </style>
        <link rel="stylesheet" href="assets/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <link rel="stylesheet" href="assets/css/edit_map.css">

        <script src="assets/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    </head>
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
    <div class='flash'>
      <button type='button' class='close' onclick="$('.flash').hide()">&times;</button>
      <div class='flash_msg'></div>
    </div>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.html">VCL</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.html">Home</a></li>
            <li><a href="edit_admin.php">Administration</a></li>
            <li><a href="edit_infotext.php">Infotexte</a></li>
      <li><a href="edit_picture.php">Fotos</a></li>
      <li class="dropdown">
        <a href="edit_admin.php" class="dropdown-toggle" data-toggle="dropdown">Übersichtskarten <b class="caret"></b></a>
         <ul class="dropdown-menu">
          <li><a href="edit_map.php?map_id=1">Halle 1/2</a></li>
          <li><a href="edit_map.php?map_id=2">KE</a></li>
              </ul> 
      </li>
          </ul>
        </div><!--/.navbar-collapse -->
      </div>
    </div>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container">
        <h2>Platziere ein neues Bild auf der Übersichtskarte</h2>
      </div>
    </div>


    <div class="container">
      
      <!-- Example row of columns -->
      <script src="assets/js/vendor/jquery-1.10.1.min.js"></script>
      <script src="assets/js/main.js"></script>
      <script src="assets/js/vendor/bootstrap.min.js"></script>
      <script src="assets/js/bootstrap-select.min.js"></script>
      

      <ul id="map_tabs" class="nav nav-tabs">
        <li><a href="#Halle1_2" data-map-id="1" data-href="edit_map.php?map_id=1" data-toggle="tab">Halle 1/2</a></li>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-no-action="true" data-toggle="dropdown">KE Gebäude<b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="#KEEG" data-map-id="2" data-href="edit_map.php?map_id=2" data-toggle="tab">KE EG</a></li>
            <li><a href="#KE1OG" data-map-id="3" data-href="edit_map.php?map_id=3" data-toggle="tab">KE 1OG</a></li>
          </ul>
        </li>
      </ul>
      
      <div id="map-canvas"></div>
      
      <script>
        var map;
        var marker;
      
      	function initializeMap()
      	{
			var mapOptions =
			{
				center: new google.maps.LatLng(52.51947, 7.32260),
				zoom: 18
			};
      		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      		marker = new google.maps.Marker();
      			
      		positionPhotos();
      			
      		google.maps.event.addListener(map, 'click', function(event)
			{
				placeMarker(event.latLng);
				fillForm(event.latLng);
			}
			);
      	}
      	
      	function placeMarker(location)
		{
			marker.setMap(map);
			marker.setPosition(location);
		}
		
		function positionPhotos()
		{
			$.ajax({
				url: 'http://vcl_app/admin/edit_map.php',
				data: 'photosOnMap=1',
				type: 'GET',
				success: function(data)
				{
					var photoData = JSON.parse(data);
					$(photoData).each(function(index, value)
					{
						new google.maps.Marker({
							position: new google.maps.LatLng(value.lat, value.lng),
							map: map,
							photoId: value.photoId
						});
					});
				}
			});
		}
		
		function fillForm(location)
		{
			$('#latitude').val(location.lat());
			$('#longitude').val(location.lng());
		}
      	
      	google.maps.event.addDomListener(window, 'load', function()
		{
			initializeMap();
		}
		);
      </script>
      
      <form method="POST">
      	 <input type="text" name="latitude" id="latitude" placeholder="Latitude">
		 <input type="text" name="longitude" id="longitude" placeholder="Longitude">
		 <button type="submit" class="btn btn-success">Speichern</button>
      </form>
      
      <footer>
          <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
