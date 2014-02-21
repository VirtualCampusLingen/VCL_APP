<?php
$DOCUMENT_ROOT = dirname(__FILE__);

$tools_dir = $DOCUMENT_ROOT . "/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
include_once($tools_dir . "header.php");
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
	$panoramaId = mysql_escape_string($_POST['panoramaId']);
	$lat = mysql_escape_string($_POST['lat']);
	$lng = mysql_escape_string($_POST['lng']);
  if(isset($_POST['area']) && isset($_POST['level']))
  {
    $area = mysql_escape_string($_POST['area']);
    $level = mysql_escape_string($_POST['level']);
    sql("UPDATE panorama SET position = GeomFromText('POINT($lat $lng)'), area = $area, level = $level WHERE panorama_id = $panoramaId");
  }else sql("UPDATE panorama SET position = GeomFromText('POINT($lat $lng)') WHERE panorama_id = $panoramaId");
}

if(isset($_GET['panoramasOnArea']))
{
	$area = mysql_escape_string($_GET['panoramasOnArea']);
	$allPanoramas = sql("SELECT panorama_id, X(position) AS lat, Y(position) AS lng, name, description, level FROM panorama AS p1 WHERE area = $area");

	$photoArray = array();
	$i = 0;
    while ($row = mysql_fetch_assoc($allPanoramas)) 
    {
		$photoArray[$i] = array('panoramaId' => $row['panorama_id'], 
								'lat' => $row['lat'], 
								'lng' => $row['lng'], 
								'name' => $row['name'],
                'desc' => $row['description'],
                'level' => $row['level']);
		$i++;
	}

	echo json_encode($photoArray);
	die();

}
include '_boilerplate.html';
?>

    <!-- Main jumbotron for a primary marketing message or call to action -->
    <div class="jumbotron" style="padding: 10px 0px 10px 0px;">
      <div class="container">
        <h2>Platziere ein neues Bild auf der Übersichtskarte</h2>
      </div>
    </div>


    <div class="container">
      <ul id="map_tabs" class="nav nav-tabs">
        <li><a href="#" onclick="changeArea(1);" data-toggle="tab">Kaiserstraße</a></li>
        <li><a href="#" onclick="changeArea(2);" data-toggle="tab">Baccumer Straße</a></li>
        <div id="editPanel" class="pull-right" style="display: none">
        	<button type="button" class="btn btn-success" onclick="saveNeighbours()">Speichern</button>
        	<button type="button" class="btn btn-danger" onclick="exitEditMode()">Abbrechen</button>
        </div>
      </ul>

      <div id="level-selector" class="btn-group btn-group-vertical">
      	<img id="elevator" src="assets/img/elevator.png" />
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
        var minZoom;
        var allowedBounds;
        var lastValidCenter;

      	function initializeMap(givenArea, givenLevel)
      	{
      		$("body").appendPartial('selectpano_modal.php');
      		mapData = JSON.parse(getMapData(givenArea, givenLevel).responseText).map_data;
      		// set global JS Variable
          	area = givenArea;
          	level = givenLevel;
			minZoom = parseInt(mapData.min_zoom);
        	allowedBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(mapData.bound_sw_lat, mapData.bound_sw_lng),
				new google.maps.LatLng(mapData.bound_ne_lat, mapData.bound_ne_lng)
			);
          //toggle classes
      		$(".active-level").removeClass('btn-primary').removeClass('active-level').addClass('btn-default');
      		$("#level" + level).removeClass('btn-default').addClass('btn-primary').addClass('active-level');
			var mapOptions =
			{
				center: new google.maps.LatLng(mapData.center_lat, mapData.center_lng),
				zoom: minZoom,
				markerHash: {}
			};
      		map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      		overlayBounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(mapData.overlay_sw_lat, mapData.overlay_sw_lng),
				new google.maps.LatLng(mapData.overlay_ne_lat, mapData.overlay_ne_lng)
			);
      overlay = new google.maps.GroundOverlay(
				mapData.overlay_path,
				overlayBounds
			);
			overlay.setMap(map);
			lastValidCenter = map.getCenter();
      		marker = new google.maps.Marker();

      		positionPhotos();

      		google.maps.event.addListener(overlay, 'click', function(event)
      		{
      			if(!inEditMode())
      			{
	      			placeMarker(event.latLng);
					fillForm(event.latLng, givenArea, givenLevel);
      			}
			});
			google.maps.event.addListener(marker, 'click', selectPanorama);
			google.maps.event.addListener(map, 'center_changed', checkBounds);
			google.maps.event.addListener(map, 'zoom_changed', checkZoom);
      	}

      	function checkBounds()
		{
			if(allowedBounds.contains(map.getCenter()))
			{
				lastValidCenter = map.getCenter();
				return;
			}
			map.panTo(lastValidCenter);
		}

		function checkZoom()
		{
			if(map.getZoom() < minZoom)
			{
				map.setZoom(minZoom);
			}
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
				marker.setIcon('assets/img/marker_orange.png');
			}
			else
			{
				editMarker.neighbours.splice(markerIndex, 1);
				marker.setIcon('assets/img/marker_green.png');
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
			});
			exitEditMode();
		}

		function exitEditMode()
		{
			if(editMarker.level != level) editMarker.setMap(null);
      editMarker = null;
			for(var key in map.markerHash)
			{
				map.markerHash[key].setIcon('assets/img/marker_green.png');
			}
			$('#editPanel').hide();
		}

		function showInfoWindow(marker)
		{
			var isOpen = marker.infoWindowOpen;
			for(var key in map.markerHash)
			{
				map.markerHash[key].infoWindow.close();
				map.markerHash[key].infoWindowOpen = false;
			}
			if(!isOpen)
			{
				$("#infotext_modal").remove();
				marker.infoWindow.open(map, marker);
				marker.infoWindowOpen = true;
				$("body").appendPartial('infotext_modal.php?id=' + marker.panoramaId);
			}
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
				data: {'panoramasOnArea': area},
				type: 'GET',
				success: function(data)
				{
					var photoData = JSON.parse(data);
					$(photoData).each(function(index, value)
					{
						var content = "<h4>" + value.name + "</h4>";
						content += "<p>" + value.desc + "</p>"
								+ "<button type='button' class='btn btn-info btn-xs info_button' onclick='enterEditMode("
								+ value.panoramaId
								+ ")'>Nachbarn bearbeiten</button><br/>";
						content += "<button type='button' class='btn btn-info btn-xs info_button' onclick='infotext_modal("
								+ value.panoramaId
								+ ")'>Infotext bearbeiten</button>";

						var infoWindow = new google.maps.InfoWindow(
						{
							content: content
						});
						var marker = new google.maps.Marker(
						{
							position: new google.maps.LatLng(value.lat, value.lng),
							map: map,
							icon: 'assets/img/marker_green.png',
							infoWindow: infoWindow,
							infoWindowOpen: false,
              				draggable: true,
							panoramaId: value.panoramaId,
              level: value.level,
							neighbours: []
						});
            map.markerHash[marker.panoramaId] = marker;
            //If marker is on another Level dont show it
            if(level != value.level) marker.setVisible(false);
            //If Level changes with an editMarker
            if (editMarker && editMarker.panoramaId == marker.panoramaId && editMarker.level == level)
            {
              // Delete 'normal' Marker
              marker.setMap(null);
              // Set Edit Marker Icon to blue
              editMarker.setIcon('assets/img/marker_blue.png');
            }else if(editMarker && editMarker.panoramaId == marker.panoramaId)
            {
              editMarker.setIcon('assets/img/marker_blue_transparent.png');
            }
						google.maps.event.addListener(marker, 'dragend', function(){markerDragEnd(marker)})
            			google.maps.event.addListener(marker, 'click', function()
						{
							inEditMode() ? toggleNeighbour(marker) : showInfoWindow(marker);
						});
					});
          if(editMarker){
            map.markerHash[editMarker.panoramaId] = editMarker;
            editMarker.setMap(map);
            editMarker.neighbours.forEach(function(neighbour_id){
              var neighbourMarker = map.markerHash[neighbour_id];
              if(neighbourMarker.level == level) neighbourMarker.setIcon('assets/img/marker_orange.png');
            });
          }
				}
			});
		}

		function fillForm(location, area, level)
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
			editMarker.setIcon('assets/img/marker_blue.png');
			for(key in map.markerHash)
			{
				map.markerHash[key].infoWindow.close();
				map.markerHash[key].infoWindowOpen = false;
			}
      getAllNeighboursFor(editMarker.panoramaId, function(data)
      {
         photoData = JSON.parse(data)['Panoid'];
         if(photoData.neighbours)
         {
           photoData.neighbours.forEach(function(entry)
           {
             pushToArrayUnlessExist(editMarker.neighbours, entry.neighbour_id);
             map.markerHash[entry.neighbour_id].setIcon('assets/img/marker_orange.png');
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
    
	function infotext_modal(id){
      $('#infotext_modal').modal('show');
    }
    
	function changeArea(area)
	{
		window.location.href = "edit_map.php?area=" + area;
		window.load;
	}
		function selectPanorama()
		{
			$("#selectpano_modal").modal();
		}

      	google.maps.event.addDomListener(window, 'load', function()
		{
			initializeMap(area, level);
		}
		);

      </script>
           
      <footer class="text-right">
          <p>&copy; VCL 2013</p>
      </footer>
    </div> <!-- /container -->        
    </body>
</html>
