<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

include 'connect.php';
include 'fxs.php';

$Arr = array();
$qx = mysqli_query($conn, "select * from category order by category_name asc");
while ($row = mysqli_fetch_assoc($qx)) {
	$obj = new StdClass();
	$obj->id = $row['category_id'];
	$obj->name = $row['category_name'];
	array_push($Arr, $obj);
}
echo json_encode($Arr);

?>