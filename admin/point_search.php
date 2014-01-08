<?php

	$DOCUMENT_ROOT = dirname(__FILE__);

	$tools_dir = $DOCUMENT_ROOT . "/tools/";
	include_once($tools_dir . "connect.php");
	include_once($tools_dir . "sql.php");
    include_once($tools_dir . "log.php");
	$dblk = connect();
    
    if($_GET['id'] >= 0) {
		$id = mysql_real_escape_string($_GET['id']);
	}

    $sql1 = sql("SELECT `x_position`,`y_position` FROM `photo` WHERE `ID` = '".$id."'");
    
    while ($row = mysql_fetch_assoc($sql1)) {
        $photo_x = $row['x_position'];
        $photo_y = $row['y_position'];
    }
    
    echo "Photo ID: ".$id."<br/>";
    echo "X: ".$photo_x."<br/>";
    echo "Y: ".$photo_y."<br/>";
    
    $sql2 = sql("SELECT `ID`,`x_position`,`y_position` FROM `photo` 
                WHERE (`y_position` >= '".($photo_y-15)."' 
                    AND `y_position` <= '".($photo_y+15)."'
                    AND `ID` != '".$id."')
                        OR
                    ( `x_position` >= '".($photo_x-15)."'
                    AND `x_position` <= '".($photo_x+15)."'   
                    AND `ID` != '".$id."' )");
    while ($row = mysql_fetch_assoc($sql2)) {
        echo "Nachbar: ID: ".$row['ID']." X: ".$row['x_position']." Y:".$row['y_position']."<br/>";
    }
?>
