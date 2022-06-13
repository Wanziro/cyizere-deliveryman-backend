<?php
date_default_timezone_set('Africa/Kigali');
$DB_NAME = 'cyizere_app';
$conn = mysqli_connect("localhost","root","",$DB_NAME);
if(!$conn){
	echo "Failed to connect to the server";
	exit();
}
?>