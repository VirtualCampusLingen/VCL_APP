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
if(isset($_GET['area']))
	$area = mysql_escape_string($_GET['area']);
elseif(isset($_POST['area']))
	$area = mysql_escape_string($_POST['area']);
else
	$area = 1;

if(isset($_POST['level']))
{
	$level = mysql_escape_string($_POST['level']);
}
else {
	$sql_level = sql("SELECT level FROM map WHERE area = $area ORDER BY level ASC LIMIT 1");
	
	while ($row = mysql_fetch_assoc($sql_level)) 
	{
		$level = $row['level'];
	}
}

$sql_levels = sql("SELECT DISTINCT level FROM map WHERE area = $area ORDER BY level DESC");
while ($row = mysql_fetch_assoc($sql_levels)) 
{
	$levels[] += $row['level'];
}

if(isset($_POST['lat']) && isset($_POST['lng']) && isset($_POST['panoramaId']))
{
	// TODO: mapId festlegen
	$panoramaId = mysql_escape_string($_POST['panoramaId']);
	$lat = mysql_escape_string($_POST['lat']);
	$lng = mysql_escape_string($_POST['lng']);
	sql("UPDATE panorama SET position = GeomFromText('POINT($lat $lng)') WHERE panorama_id = $panoramaId");
}

if(isset($_GET['photosOnMap']))
{
	$mapId = mysql_escape_string($_GET['photosOnMap']);
	$photosOnMap = sql("SELECT panorama_id, X(position) AS lat, Y(position) AS lng, description FROM panorama WHERE level = $mapId");

	$photoArray = array();
	$i = 0;
    while ($row = mysql_fetch_assoc($photosOnMap)) 
    {
		$photoArray[$i] = array('panoramaId' => $row['panorama_id'], 
								'lat' => $row['lat'], 
								'lng' => $row['lng'], 
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
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?libraries=geometry&sensor=false">
</script>
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
          <a class="navbar-brand" href="index.html"><img src="assets/img/Logo.png" width="37" height="20" alt="home"/></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li><a href="index.html">Home</a></li>
            <li><a href="edit_infotext.php">Infotexte</a></li>
      <li><a href="edit_picture.php">Fotos</a></li>
      <li class="dropdown">
        <a href="edit_map.php" class="dropdown-toggle" data-toggle="dropdown">Übersichtskarten <b class="caret"></b></a>
         <ul class="dropdown-menu">
          <li><a href="edit_map.php?area=1">Halle 1/2</a></li>
          <li><a href="edit_map.php?area=2">KE</a></li>
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
        <li><a href="#" onclick="changeArea(1);" data-toggle="tab">Kaiserstraße</a></li>
        <li><a href="#" onclick="changeArea(2);" data-toggle="tab">Baccumer Straße</a></li>
        <div id="editPanel" class="pull-right" style="display: none">
        	<button type="button" class="btn btn-success" onclick="saveNeighbours()">Speichern</button>
        	<button type="button" class="btn btn-danger" onclick="exitEditMode()">Abbrechen</button>
        </div>
      </ul>
      
      <div id="level-selector" class="btn-group btn-group-vertical">
      	<img id="elevator" src="images/elevator.png" />
      	<?php
      		foreach ($levels as $currLevel => $key) {
				echo '<button type="button" id="level' . 
				$key . '" class="btn btn-default" onclick="initializeMap(' . 
				$area . ', ' . 
				$key . ')">' . 
				$key . '</button>';
			}
      	?>
      </div>
      
      <div id="map-canvas"></div>
      
      <div class="clear"></div>
      
      <script>

        var map;
        var marker;
        var editMarker = null;
        var area = <?=$area?>;
        var level = <?=$level?>;

      	function initializeMap(area, level)
      	{
      		mapData = JSON.parse(getMapData(area, level).responseText).map_data;
      		
      		$(".active-level").removeClass('btn-primary').removeClass('active-level');
      		$("#level" + level).removeClass('btn-default').addClass('btn-primary').addClass('active-level');

			var mapOptions =
			{
				center: new google.maps.LatLng(mapData.center_lat, mapData.center_lng),
				zoom: parseInt(mapData.min_zoom),
				markerHash: {}
			};
      		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      		overlayBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(52.51770, 7.32057),
				new google.maps.LatLng(52.52053, 7.32379)
			);
			overlay = new google.maps.GroundOverlay(
				mapData.overlay_path,
				overlayBounds
			);
			overlay.setMap(map);
      		marker = new google.maps.Marker();

      		positionPhotos();

      		google.maps.event.addListener(overlay, 'click', function(event)
      		{
      			if(!inEditMode())
      			{
	      			placeMarker(event.latLng);
					fillForm(event.latLng);	
      			}
			});
      	}
      	
      	function getMapData(area, level)
      	{
      		return $.ajax(
			{
				url: 'get_map_data.php',
				type: 'POST',
				async: false,
				data: {'area': area, 'level': level}
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

			var markerIndex = $.inArray(marker.panoramaId, editMarker.neighbours);
			if(markerIndex == -1)
			{
				editMarker.neighbours.push(marker.panoramaId);
				marker.setIcon('images/marker_orange.png');
			}
			else
			{
				editMarker.neighbours.splice(markerIndex, 1);
				marker.setIcon('images/marker_green.png');
			}
		}

    function computeHeading(latLng1, latLng2)
    {
      // geometry Api computed Heading clockwise from -180 to 180 degrees
      // streetView Api works with clockwise 0 to 360 degrees. Therefore +180
      return google.maps.geometry.spherical.computeHeading(latLng1, latLng2) + 180;
    }

		function saveNeighbours()
		{
			if(!inEditMode()) return;
      var neighbours = {};
      editMarker.neighbours.forEach(function(neighbourId){
        var ownLatLng = map.markerHash[editMarker.panoramaId].getPosition();
        var neighbourLatLng = map.markerHash[neighbourId].getPosition();
        neighbours[neighbourId] = computeHeading(neighbourLatLng, ownLatLng);
      });
			var json = {'saveNeighbour': true, 'saveNeighboursFor': {'panoramaId': editMarker.panoramaId, 'neighbours': neighbours}};
			$.ajax(
			{
				url: 'apis/edit_map_api.php',
				data: json,
				type: 'POST',
				success: function(data)
				{
					// TODO:
					//console.log(test);
				}
			});
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

    function markerDragEnd(marker)
    {
      //save new position
      var jsonData = {'lat': marker.getPosition().lat(), 'lng': marker.getPosition().lng(), 'panoramaId': marker.panoramaId };
      $.ajax({
        type: 'POST',
        data: jsonData
      });
      //Recalculate Heading for Marker -> Neighbours && Neighbour -> Marker
      //get all neighbours for Marker
      getAllNeighboursFor(marker.panoramaId, function(data)
      {
        photoData = JSON.parse(data)['Panoid'];
        if(photoData.neighbours)
        {
          //iterate through neighbours
          photoData.neighbours.forEach(function(entry)
          {
           //compute Heading
           var ownLatLng = marker.getPosition();
           var neighbourLatLng = map.markerHash[entry.neighbour_id].getPosition();
           var computedHeading = computeHeading(neighbourLatLng, ownLatLng);
           var json = {'updateHeading': true, 'panoramaId': marker.panoramaId, 'neighbourId': entry.neighbour_id, 'heading': computedHeading}
           //send to Api
           $.ajax({
            url: 'apis/edit_map_api.php',
            type: 'POST',
            data: json
           });
          });
        }
      })
      //
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
								+ value.panoramaId
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
              				draggable: true,
							panoramaId: value.panoramaId,
							neighbours: []
						});
						map.markerHash[value.panoramaId] = marker;
						google.maps.event.addListener(marker, 'dragend', function(){markerDragEnd(marker)})
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
			$('#area').val(area);
			$('#level').val(level);
		}

    function pushToArrayUnlessExist(array, value){
      if($.inArray(value, array) == -1) array.push(value);
    }

		function enterEditMode(panoramaId)
		{
			// TODO: Refactoring der AJAX-Parameter
			$('#editPanel').show();
			editMarker = map.markerHash[panoramaId];
			editMarker.setIcon('images/marker_blue.png');
			for(key in map.markerHash)
			{
				map.markerHash[key].infoWindow.close();
			}
      getAllNeighboursFor(editMarker.panoramaId, function(data)
      {
         photoData = JSON.parse(data)['Panoid'];
         if(photoData.neighbours)
         {
           photoData.neighbours.forEach(function(entry)
           {
             pushToArrayUnlessExist(editMarker.neighbours, entry.neighbour_id);
             map.markerHash[entry.neighbour_id].setIcon('images/marker_orange.png');
           });
         }
       });
		}

    function getAllNeighboursFor(panoramaId, successCallback){
      $.ajax(
      {
        url: 'apis/panorama_data_api.php',
        type: 'GET',
        data: 'id=' + panoramaId,
        success: function(data){ successCallback(data) }
      });
    }
    
	function changeArea(area)
	{
		window.location.href = "edit_map.php?area=" + area;
		window.load;
	}

      	google.maps.event.addDomListener(window, 'load', function()
		{
			initializeMap(area, level);
		}
		);

      </script>

      <form method="POST">
      	  <?php
      	  	$allPhotos = sql("SELECT * FROM panorama");
            echo("<select name='panoramaId'>");
            $i = 0;
            while($row = mysql_fetch_assoc($allPhotos)){
              $photo_hsh[$i]["panoramaId"] = $row["panorama_id"];
              $photo_hsh[$i]["photo_name"] = $row["name"];

              echo("<option value='" .$photo_hsh[$i]["panoramaId"]. "'>" .$photo_hsh[$i]["photo_name"]. "</option>");
              $i++;
            }
            echo("</select>");
          ?>
          
      	 <input type="hidden" name="lat" id="lat">
		 <input type="hidden" name="lng" id="lng">
		 <input type="hidden" name="area" id="area">
		 <input type="hidden" name="level" id="level">
		 <button type="submit" class="btn btn-success">Speichern</button>
      </form>
      
      <footer>
          <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
