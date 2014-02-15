    <?php
    $DOCUMENT_ROOT = dirname(__FILE__);

    $tools_dir = $DOCUMENT_ROOT . "/tools/";
    include_once($tools_dir . "connect.php");
    include_once($tools_dir . "sql.php");
    include_once($tools_dir . "log.php");
    $dblk = connect();

    $error = 0;
    error_reporting(null);
    $panoid = mysql_real_escape_string($_GET['id']);
    ?>

    <!-- Modal -->
    <div id="infotext_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="infotext_modal" aria-hidden="true">
        <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h3 id="myModalLabel">Infotexte vergeben</h3>
                </div>
              <div class="modal-body">
                <form role="form">
                  
                  <?php
                    $checked_infoids = array();

                    $sql_infotext_cheked = sql("SELECT * FROM `infotext_panorama` WHERE `panorama` = '".$panoid."'");
                    
                    while($row = mysql_fetch_assoc($sql_infotext_cheked)) {
                      $checked_infoids[] += $row['infotext'];
                    }

                  $sql_infotext = sql("SELECT * FROM  `infotext`");
                  
                    while ($row = mysql_fetch_assoc($sql_infotext)){
                      ?>
                      <div class="checkbox">
                        <label>
                          <!-- TODO KURZ BITTE-->
                          <?php if (in_array($row['infotext_id'], $checked_infoids)){
                            ?>
                              <input type="checkbox" checked="checked" id="<?php echo $row['infotext_id'];?>">
                            <?php
                          }else{
                            ?>
                              <input type="checkbox" id="<?php echo $row['infotext_id'];?>">
                            <?php
                          }?>
                          <?php echo $row['title'];?>
                        </label>
                      </div>
                    <?php
                    } 
                    ?>
                  
                </form>
              </div>
              <div class="modal-footer">
                <!--TODO -->
                <button class="btn btn-success" onclick="safe_infotext(<?php echo $panoid;?>)">Speichern</button>
                <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Abbrechen</button>
              </div>
            </div>
      </div>
    </div>

    <script type="text/javascript">
    function safe_infotext(id){

      panoid = id;
      infotext = new Array();
      $.each($(".modal-body :checked"),function (){
        infotext.push($(this).attr("id"));
      });

      var json = {'panoid': panoid, 'infotextid': infotext}
      //send to Api
      $.ajax({
            url: 'apis/edit_pano_infotext.php',
            type: 'POST',
            data: json,
            success: function(){$('#infotext_modal').modal('hide');}
      });
    }
    </script>