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

<script>
	function select(element)
	{
		$(".list-group a").removeClass("active");
		$(element).addClass("active");
		$("#selectedPanoramaId").val($(element).attr("data-pano_id"));
	}
</script>

<div id="selectpano_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Wählen Sie ein Foto aus</h4>
      </div>
      <div class="modal-body">
      	<div class="list-group" style="height: 300px; overflow: auto">
	      	<?php
	      		$sql_panoramas = sql("SELECT panorama_id, name, description FROM panorama ORDER BY name");
				$i = 0;
				while($row = mysql_fetch_assoc($sql_panoramas)){
					$panoramas[$i]["panorama_id"] = $row["panorama_id"];
					$panoramas[$i]["name"] = $row["name"];
					$panoramas[$i]["description"] = $row["description"];
					echo '<a href="javascript:void(0)" onclick="select(this)" class="list-group-item" data-pano_id="' . $panoramas[$i]["panorama_id"] . '">';
					echo '<h4 class="list-group-item-heading">' . $panoramas[$i]["name"] . '</h4>';
					echo '<p class="list-group-item-text">' . $panoramas[$i]["description"] . '</p>';
					echo '</a>';
					
					$i++;
				}
	      	?>
      	</div>
      </div>
      <div class="modal-footer">
      	<form method="POST" action="edit_map.php">
      		<input type="hidden" name="panoramaId" id="selectedPanoramaId">
			<input type="hidden" name="lat" id="lat">
			<input type="hidden" name="lng" id="lng">
			<input type="hidden" name="area" id="area">
			<input type="hidden" name="level" id="level">
	        <button type="submit" class="btn btn-success">Speichern</button>
	        <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
	    </form>
      </div>
    </div>
  </div>
</div>
