<?php
$DOCUMENT_ROOT = dirname(__FILE__);

$tools_dir = $DOCUMENT_ROOT . "/admin/tools/";
include_once($tools_dir . "connect.php");
include_once($tools_dir . "sql.php");
include_once($tools_dir . "log.php");
$dblk = connect();

$error = 0;
error_reporting(null);

?>

<div id="poi_modal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Wo möchtest du hin?</h4>
      </div>
      <div class="modal-body">
      	<div class="list-group" style="height: 300px; overflow: auto">
	        <?php
	        	$sql_poi = sql("SELECT name, description, panorama, preview_image_path FROM poi ORDER BY name ASC");
				$i = 0;
				while($row = mysql_fetch_assoc($sql_poi)){
					$poi_name = $row['name'];
					$poi_description = $row['description'];
					$poi_panorama = $row['panorama'];
					$poi_preview_image = $row['preview_image_path'];

					echo '<a class="list-group-item poi" href="javascript:void(0)" onclick="initialize(' . $poi_panorama . ')" data-dismiss="modal">';
					echo '<img class="media-object poi-preview" src="/admin/assets/img/poi_example.png" alt="poi-preview">';
					echo '<h4 class="list-group-item-heading">' . $poi_name . '</h4>';
					echo '<p class="list-group-item-text">' . $poi_description . '</p>';
					echo '<div class="clear"></div>';
					echo '</a>';
					
					$i++;
				}
	        ?>
	    </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div>
  </div>
</div>
