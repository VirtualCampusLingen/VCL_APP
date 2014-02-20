<?php
# Logging requests
$log_file = $_SERVER['DOCUMENT_ROOT'].'/admin/log/log.txt';
$logdatei = fopen($log_file,"a");
error_reporting(0);
fputs($logdatei,
    date("d.m.Y, H:i:s",time()) .
    ", " . $_SERVER['REMOTE_ADDR'] .
    ", " . $_SERVER['REQUEST_METHOD'] .
    ", " . $_SERVER['PHP_SELF'] .
    ", " . $_SERVER['HTTP_USER_AGENT'] .
    ", " . $_SERVER['HTTP_REFERER'] ."\n"
    );
fclose($logdatei);

?>