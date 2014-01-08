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

        <div id="edit_map_ctrl_btnGrp"> 
          <button class="btn btn-danger" onclick="exitEditMode()">Bearbeitungsmodus verlassen</button>
          <button class="btn btn-success" onclick="neighboursSendAjax()">Speichern</button>
        </div>
      </ul>



      <?php 
        //Read GET Data
        $mapId = $_GET['map_id'];
        if(empty($mapId)) $mapId = 1;

        //Read transmitted POST Data
        $entering_map_id = $_POST['entering_point'];
        $id = $_POST['ID'];
        $xPos = $_POST['x_position'];
        $yPos = $_POST['y_position'];
        $map_id = $_POST['map_id'];

        //If 'is_entering_point' is set, change map Table
        if( !empty($entering_map_id) ) {
          sql("UPDATE map_enterings SET entered_photo_id='".$id."', x_pos_on_map='".$xPos."', y_pos_on_map='".$yPos."' WHERE map_id='".$mapId."' AND entered_map_id='".$entering_map_id."' ");
        }
        //else change photo table
        elseif ( !empty($xPos) && !empty($yPos) && !empty($id) ) {
          sql("UPDATE photo SET 
            map_id = '".$map_id."',
            x_position='" .$xPos. "', 
            y_position='" .$yPos. "' 
            WHERE PhotoID='" .$id. "' ");
        }

      ?>

      <?php
        $sql_photos = sql("SELECT * FROM photo");
        $sql_map_photos = sql("SELECT * FROM photo WHERE map_id='" .$mapId. "'");
        $sql_sub_map_photos = sql("SELECT * FROM map ");
        //TODO select all From map Table
        $sql_map = sql("SELECT map.*, map_enterings.* From map INNER JOIN map_enterings ON map.MapID = map_enterings.map_id WHERE map.MapID='".$mapId."' ");

        while($row = mysql_fetch_assoc($sql_map)){
          if($row["MapID"] == $mapId){
            $map["MapID"] = $mapId;
            $map["map_name"] = $row["map_name"];
            $map["image_map_path"] = $row["image_map_path"];
            $map["image_map_entrys"] = $row["image_map_entrys"];
            $map["parent_map"] = $row["parent_map"];
            $map["map_starting_photo"] = $row["map_starting_photo"];

            if(!empty($row["path"])) $map["path"] = $row["path"];
            else $map["path"] = "";
          }
          
          $entered_photo_map_name = sql("SELECT map_name FROM map WHERE MapID='".$row['entered_map_id']."' ");
          $entered_photo_map_name = mysql_result($entered_photo_map_name, 0);
          echo("
            <a class='aPop' data-id='".$row["entered_photo_id"]."'>
            <img id='dot".$row["entered_photo_id"]."' 
            class='map_dots' 
            src='assets/img/dot_orange.png' 
            data-origin-color='orange'
            data-map-id='".$row["entered_map_id"]."'
            data-entering-to='".$entered_photo_map_name."'
            data-id='".$row["entered_photo_id"]."'
            data-x-pos='" . $row["x_pos_on_map"] . "' 
            data-y-pos='".$row["y_pos_on_map"]."'
            data-content= 'Test'
            />
            </a>  ");
        };

        while($row = mysql_fetch_assoc($sql_map_photos)){
          $index = $row["PhotoID"];
          $hsh[$index]["PhotoID"] = $row["PhotoID"];
          $hsh[$index]["photo_name"] = $row["photo_name"];
          $hsh[$index]["description"] = $row["description"];
          $hsh[$index]["x_position"] = $row["x_position"];
          $hsh[$index]["y_position"] = $row["y_position"];
          $hsh[$index]["map_id"] = $row["map_id"];

          if($hsh[$index]["x_position"] && $hsh[$index]["y_position"]){
            echo("
              <a class='aPop' data-id='".$hsh[$index]["PhotoID"]."'>
              <img id='dot".$hsh[$index]["PhotoID"]."' 
              class='map_dots' 
              src='assets/img/dot_blue.png'
              data-origin-color='blue'
              data-id='".$hsh[$index]["PhotoID"]."'
              data-x-pos='" . $hsh[$index]["x_position"] . "' 
              data-y-pos='".$hsh[$index]["y_position"]."'
              data-content= '" . $hsh[$index]["description"] . "'
              />
              </a>  ");
          }
        }
      ?>

      <script>        
        $(window).load(function(){
          $("#photo_pick").selectpicker();
          $("#map_tabs a[data-no-action!='true']").each(function(){
            if ("edit_map.php"+location.search == $(this).attr("data-href") ) {
              $(this).parent("li").addClass("active")
            }
            $(this).click(function(){
              location.href = $(this).attr("data-href"); 
            });
          });

          if(!$("#edit_map_map").attr("src")){
            $("#edit_map_form").hide();
          }

          imgOffset = $("#edit_map_map").offset();
          $(".map_area").click(function(e){
            $("#edit_map_form_X").val(e.pageX-imgOffset.left);
            $("#edit_map_form_Y").val(e.pageY-imgOffset.top);
          });

          $(".map_dots").each(function(){
            $(this).click(function(){
              if(typeof editModeState != 'undefined' && editModeState){
                //prevent popover
                $(this).popover('disable');
                //edit JSon
                var neighbour_id = $(this).attr("data-id")
                toggleNeighbourJson(neighbour_id)
                //toggle Img color
                toggleImgColor(this);
              }
            });
            //TODO read correct photo name with id of $(this).attr('data-id')
            if(!$(this).attr("data-entering-to"))
              $(this).popover({
                html: true,
                title: "<?= $hsh[1]['photo_name'] ?> <a onclick='editMode(this)'>bearbeiten</a>"
              });

            var map_dot = $(this);
            var offsetTop = imgOffset.top + parseInt(map_dot.attr("data-y-pos")) - map_dot.height()/2;
            var offsetLeft = imgOffset.left + parseInt(map_dot.attr("data-x-pos")) - map_dot.width()/2;
            $(this).attr("style", "z-index: 2; position: absolute; top: "+offsetTop+"px; left: "+offsetLeft+"px;");
          });
        });

        function editMode(popover_link){
          editModeState = true
          var clickedPhotoID = $(popover_link).closest(".aPop").attr("data-id")
          //reset all previos img colors
          $("img[src='assets/img/dot_green.png']").each(function(){toggleImgColor(this)})
          //show control button group
          $("#map_tabs li[class!='active']").hide()
          $("#edit_map_ctrl_btnGrp").show();
          //highlight curren img
          $("#dot"+clickedPhotoID).addClass("edit_current_img")
          //get all  neighbours form DB as Json
          $.getJSON("/admin/test_new.php?id="+clickedPhotoID, function(data){
            var existingNeighbours = data["Panoid"].neighbours;
            console.log(existingNeighbours)
            //generate Json
            neighbourJson = new Object();
            neighbourJson.photo_id = clickedPhotoID;
            neighbourJson.neighbours = [];
            for(var index in existingNeighbours){
              var neighbour_id = existingNeighbours[index].neighbour_id
              neighbourJson.neighbours[index] = neighbour_id

              //$("#sub_map_container button[data-entry-photo='"+neighbour_id+"']").attr("class", "btn btn-success")
              toggleImgColor( $("#dot"+neighbour_id) )
            };
            //show SubMap Panel
            //$("#sub_map_container").show()
            //hide popover
            $(".popover:visible").hide()
          });
          
        };

        function toggleImgColor(img){ 
          $(img).attr("src").indexOf($(img).attr("data-origin-color")) >= 0 ?  $(img).attr("src", "assets/img/dot_green.png") : $(img).attr("src", "assets/img/dot_"+$(img).attr("data-origin-color")+".png") 
        }

        function exitEditMode(){
          //hide control button group
          $("#edit_map_ctrl_btnGrp").hide()
          $("#map_tabs li[class!='active']").show()
          editModeState = false;
          //reset all previous img colors
          $("img[src='assets/img/dot_green.png']").each(function(){toggleImgColor(this)})
          //remove Highlight class
          $(".edit_current_img").removeClass("edit_current_img")
          //$("#sub_map_container button").attr("class", "btn btn-info")
          //$("#sub_map_container").hide()
          //delete Json Object
          delete neighbourJson
          //active popover functionality
          $(".map_dots").popover('enable')
        }

        function subMapToggle(subMapToggleBtn){
          var id = $(subMapToggleBtn).attr('data-entry-photo')
          toggleNeighbourJson(id)
          
          new_class = neighbourJson.neighbours.indexOf(id) > -1 ? "btn btn-success" : "btn btn-info"
          $(subMapToggleBtn).removeClass()
          $(subMapToggleBtn).addClass(new_class)
        }

        function toggleNeighbourJson(id){
          var neighbours_array = neighbourJson.neighbours;
          if ( neighbours_array.indexOf(id) > -1){
            neighbours_array.splice(neighbours_array.indexOf(id), 1)
          }else neighbours_array.push(id)
        }

        function neighboursSendAjax(){
          //request to Server
          var jqueryXHR = $.ajax({
            type: "GET",
            url: "/admin/test_new.php",
            data: neighbourJson,
            error: function(xhr, status, error) {
              setFlash('error', 'Ihre Anfrage konnte nicht abgesendet werden')
            },
            success: function(data, status, xhr) {
              setFlash('success', 'Daten wurden gespeichert')
            },
            complete: function(data, status) {
              exitEditMode()
            }
          });
        }

        //deprecated => moved to main.js
        /*function setFlash(type, msg){
          switch (type){
            case 'success':
              $(".flash").addClass("flash_success")
              $(".flash_msg").html(msg)
              $(".flash").show()
              break;
            case 'error':
              $(".flash").addClass("flash_error")
              $(".flash_msg").html(msg)
              $(".flash").show()
              break;
          }
        }*/
      </script> 

      <div id="edit_map_content">
        <map name="map" id="map">
        <area id="1" class="map_area" 
          shape="poly"
          coords= "<?= $map["image_map_path"] ?>"
          href="#" 
          alt="" />

        <area id="2" 
        shape="poly" 
        coords= "<?= $map["image_map_entrys"] ?>"
        href="edit_map.php?map_id=2" 
        alt="" />
        </map>
        <img src="<?= $map['path'] ?>"  border="0" alt="Übersichtskarte" title="" usemap="#map" id="edit_map_map"/>

        <form id="edit_map_form" method="POST">
          <?php
            echo("<select name='ID' id='photo_pick' class='selectpicker'>");
            $n = 0;
            while($row = mysql_fetch_assoc($sql_photos)){
              $photo_hsh[$n]["PhotoID"] = $row["PhotoID"]; 
              $photo_hsh[$n]["photo_name"] = $row["photo_name"];
              
              echo("<option value='" .$photo_hsh[$n]["PhotoID"]. "'>" .$photo_hsh[$n]["photo_name"]. "</option>");
              $n++;  
            }
            echo("</select>");
          ?>
          <input type="hidden" name="map_id" id="edit_map_form_map_id" value="<?=$mapId?>">
          <span id='realted_maps_radio_bar'>
          </span>
          <script>
            $(".map_dots[data-entering-to]").each(function(){
              var val = $(this).attr('data-map-id')
              var content = $(this).attr('data-entering-to')
              $('#realted_maps_radio_bar').append("<input type='radio' name='entering_point' value='"+val+"'>"+content+" ");
            });
          </script>

          <input type="text" name="x_position" id="edit_map_form_X" placeholder="X Position">
          <input type="text" name="y_position" id="edit_map_form_Y" placeholder="Y Position">
          <button type="submit" class="btn btn-success">Speichern</button>
        </form>
        <hr>

        <footer>
          <p>&copy; VCL 2013</p>
        </footer>
      </div> <!-- /edit_map_content  -->
    </div> <!-- /container -->        
    <!-- 
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.1.min.js"><\/script>')</script>

        <script src="js/vendor/bootstrap.min.js"></script>

        <script src="js/main.js"></script>

        <script>
            var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
            (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
            g.src='//www.google-analytics.com/ga.js';
            s.parentNode.insertBefore(g,s)}(document,'script'));
        </script> 
        -->
    </body>
</html>
