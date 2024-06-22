<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=UTF-8");
$host='localhost:3306';
$user='root';
$PASS='';
$NAME='ratex';
define('HOST',$host);
define('USER',$user);
define('PASS',$PASS);
define('NAME',$NAME);
$db = new mysqli(HOST ,USER ,PASS ,NAME);
if ($db->connect_errno) {
	die("Database connection error:" . $db->connect_errno);
}
$db -> set_charset("utf8");
?>