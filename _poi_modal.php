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
      	<!--<ul class="media-list" style="height: 300px; overflow: auto">-->
      	<div class="list-group" style="height: 300px; overflow: auto">
	        <?php
	        	$sql_poi = sql("SELECT name, description, panorama FROM poi ORDER BY name ASC");
				$i = 0;
				while($row = mysql_fetch_assoc($sql_poi)){
					$poi_name = $row['name'];
					$poi_description = $row['description'];
					$poi_panorama = $row['panorama'];
					
					/*
					echo '<li class="media">';
					echo '<a class="pull-left" href="#"><img class="media-object" src="/admin/assets/img/poi_example.png" alt="' . $poi_name . '"></a>';
					echo '<div class="media-body">';
					echo '<h4 class="media-heading">' . $poi_name . '</h4>';
					echo '<p>' . $poi_description . '</p>';
					echo '</li>';
					 */	
					
					echo '<a href="#" class="list-group-item">';
					echo '<h4 class="list-group-item-heading">' . $poi_name . '</h4>';
					echo '<p class="list-group-item-text">' . $poi_description . '</p>';
					echo '</a>';
					
					$i++;
				}
	        ?>
	    </div>
        <!--</ul>-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
      </div>
    </div>
  </div>
</div>
