    <?php
	$DOCUMENT_ROOT = dirname(__FILE__).'/..';

	$tools_dir = $DOCUMENT_ROOT . "/tools/";
	include_once($tools_dir . "connect.php");
	include_once($tools_dir . "sql.php");
	include_once($tools_dir . "log.php");
	$dblk = connect();

    $error = 0;
    error_reporting(null);

	$panoid = mysql_real_escape_string($_POST['panoid']);
	sql("DELETE FROM infotext_panorama WHERE panorama = ".$panoid." ");
	foreach ($_POST['infotextid'] as $infotext => $infotextid) {
		$infotextid = mysql_real_escape_string($infotextid);
		sql("INSERT INTO infotext_panorama (infotext,panorama) VALUES ($infotextid,$panoid)");
		//sql("INSERT INTO infotext_panorama (infotext,panorama) VALUES (".$infotextid.",".$panoid.")";
	}
?>