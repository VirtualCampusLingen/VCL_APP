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

if(isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['photoId']))
{
	// TODO: mapId festlegen
	$photoId = mysql_escape_string($_POST['photoId']);
	$lat = mysql_escape_string($_POST['lat']);
	$lng = mysql_escape_string($_POST['lng']);
	sql("UPDATE photo SET x_position = $lat, y_position = $lng WHERE PhotoID = $photoId");
}

if(isset($_GET['photosOnMap']))
{
	$mapId = mysql_escape_string($_GET['photosOnMap']);
	$photosOnMap = sql("SELECT * FROM photo WHERE map_id = $mapId");
	
	$photoArray = array();
	$i = 0;
    while ($row = mysql_fetch_assoc($photosOnMap)) 
    {
		$photoArray[$i] = array('photoId' => $row['PhotoID'], 
								'lat' => $row['x_position'], 
								'lng' => $row['y_position'], 
								'desc' => $row['description']);
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
        <div id="editPanel" class="pull-right" style="display: none">
        	<button type="button" class="btn btn-success" onclick="saveNeighbours()">Speichern</button>
        	<button type="button" class="btn btn-danger" onclick="exitEditMode()">Abbrechen</button>
        </div>
      </ul>
      
      <div id="map-canvas"></div>
      
      <script>
        var map;
        var marker;
        var editMarker = null;
      
      	function initializeMap()
      	{
			var mapOptions =
			{
				center: new google.maps.LatLng(52.51947, 7.32260),
				zoom: 18,
				markerHash: {}
			};
      		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      		marker = new google.maps.Marker();
      			
      		positionPhotos();
      			
      		google.maps.event.addListener(map, 'click', function(event)
      		{
      			if(!inEditMode())
      			{
	      			placeMarker(event.latLng);
					fillForm(event.latLng);	
      			}
			});
      	}
      	
      	function inEditMode()
      	{
      		return editMarker != null;
      	}
      	
      	function placeMarker(location)
		{
			marker.setMap(map);
			marker.setPosition(location);
		}
		
		function toggleNeighbour(marker)
		{
			if(marker == editMarker) return;
			
			var markerIndex = $.inArray(marker.photoId, editMarker.neighbours);
			if(markerIndex == -1)
			{
				editMarker.neighbours.push(marker.photoId);
				marker.setIcon('images/marker_orange.png');
			}
			else
			{
				editMarker.neighbours.splice(markerIndex, 1);
				marker.setIcon('images/marker_green.png');
			}
		}
		
		function saveNeighbours()
		{
			if(!inEditMode()) return;
						
			var json = {'saveNeighboursFor': {'photoId': editMarker.photoId, 'neighbours': editMarker.neighbours}}
			console.log(json);
			$.ajax(
			{
				url: 'test_new.php',
				data: json,
				type: 'POST',
				success: function(data)
				{
					// TODO:
					//console.log(test);
				}
			});
			// TODO: editMode schließen
			exitEditMode();
		}
		
		function exitEditMode()
		{
			editMarker = null;
			for(var key in map.markerHash)
			{
				map.markerHash[key].setIcon('images/marker_green.png');
			}
			$('#editPanel').hide();
		}
		
		function showInfoWindow(marker)
		{
			marker.infoWindowOpen ? marker.infoWindow.close() : marker.infoWindow.open(map, marker);
			marker.infoWindowOpen = !marker.infoWindowOpen;
		}
		
		function positionPhotos()
		{
			$.ajax(
			{
				// TODO: Refactoring
				data: 'photosOnMap=1',
				type: 'GET',
				success: function(data)
				{
					var photoData = JSON.parse(data);
					$(photoData).each(function(index, value)
					{
						var content = "<p>" + value.desc + "</p>";
						content += "<button type='button' class='btn btn-info btn-xs' onclick='enterEditMode("
								+ value.photoId
								+ ")'>Bearbeiten</button>";
						
						var infoWindow = new google.maps.InfoWindow(
						{
							content: content
						});
						var marker = new google.maps.Marker(
						{
							position: new google.maps.LatLng(value.lat, value.lng),
							map: map,
							icon: 'images/marker_green.png',
							infoWindow: infoWindow,
							infoWindowOpen: false,
							photoId: value.photoId,
							neighbours: []
						});
						map.markerHash[value.photoId] = marker;
						google.maps.event.addListener(marker, 'click', function()
						{
							inEditMode() ? toggleNeighbour(marker) : showInfoWindow(marker);
						});
					});
				}
			});
		}
		
		function fillForm(location)
		{
			$('#lat').val(location.lat());
			$('#lng').val(location.lng());
		}
		
		function enterEditMode(photoId)
		{
			// TODO: Refactoring der AJAX-Parameter
			$('#editPanel').show();
			editMarker = map.markerHash[photoId];
			editMarker.setIcon('images/marker_blue.png');
			for(key in map.markerHash)
			{
				map.markerHash[key].infoWindow.close();
			}
			$.ajax(
			{
				url: 'test_new.php',
				type: 'GET',
				data: 'id=' + editMarker.photoId,
				success: function(data)
				{
					photoData = JSON.parse(data)['Panoid'];
					if(photoData.neighbours)
					{
						photoData.neighbours.forEach(function(entry)
						{
							editMarker.neighbours.push(entry.neighbour_id);
							map.markerHash[entry.neighbour_id].setIcon('images/marker_orange.png');
						});
					}
				}
			});
		}
      	
      	google.maps.event.addDomListener(window, 'load', function()
		{
			initializeMap();
		}
		);
		
      </script>
      
      <form method="POST">
      	  <?php
      	  	$allPhotos = sql("SELECT * FROM photo");
            echo("<select name='photoId'>");
            $i = 0;
            while($row = mysql_fetch_assoc($allPhotos)){
              $photo_hsh[$i]["PhotoID"] = $row["PhotoID"];
              $photo_hsh[$i]["photo_name"] = $row["photo_name"];
              
              echo("<option value='" .$photo_hsh[$i]["PhotoID"]. "'>" .$photo_hsh[$i]["photo_name"]. "</option>");
              $i++;
            }
            echo("</select>");
          ?>
      	 <input type="hidden" name="lat" id="lat">
		 <input type="hidden" name="lng" id="lng">
		 <button type="submit" class="btn btn-success">Speichern</button>
      </form>
      
      <footer>
          <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
