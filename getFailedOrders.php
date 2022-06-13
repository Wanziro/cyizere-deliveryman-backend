<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

include 'connect.php';
include 'fxs.php';

function getProductDetails($id){
	include 'connect.php';
	// include 'fxs.php';
	$obj = new StdClass();
	$qx = mysqli_query($conn, "select * from product where prod_id='$id'");
	if(mysqli_num_rows($qx) > 0){
		while ($row = mysqli_fetch_assoc($qx)) {
			$obj->id = $row['prod_id'];
			$obj->name = $row['product_name'];
			$obj->price = $row['product_price'];
			$obj->images = getProductImages($row['prod_id']);
		}
	}else{
		$Arr = array();
		$obj->id = "Not found";
		$obj->name = "Not found";
		$obj->price = "Not found";
		$obj->images = $Arr;
	}
	return $obj;
}

function getClientDetails($id){
	include 'connect.php';
	$obj = new StdClass();
	$qx = mysqli_query($conn, "select * from clients where client_id='$id'");
	if(mysqli_num_rows($qx) > 0){
		while ($row = mysqli_fetch_assoc($qx)) {
			$obj->id = $row['client_id'];
			$obj->name = $row['client_name'];
			$obj->address = $row['client_address'];
			$obj->phone = $row['client_phone'];
		}
	}else{
		$obj->id = "Not found";
		$obj->name = "Not found";
		$obj->address = "Not found";
		$obj->phone = "Not found";
	}
	return $obj;
}

if (isset($data["email"])) {
	$email = mysqli_real_escape_string($conn,$data['email']);
	$userId = mysqli_real_escape_string($conn,$data['userId']);
	if (validateUser($email,$userId)) {
		$Arr = array();
		$qx = mysqli_query($conn, "select * from product,orders,order_details where order_details.order_id=orders.order_id and order_details.prod_id=product.prod_id and product.supplier_id='$userId' and orders.payment_status='failed' order by orders.order_id desc");
		if(mysqli_num_rows($qx) > 0){
			while ($row = mysqli_fetch_assoc($qx)) {
				$obj = new StdClass();
				$obj->orderId = $row['order_id'];
				$obj->product = getProductDetails($row['prod_id']);
				// $obj->client = getClientDetails($row['client_id']);
				$obj->status = $row['payment_status'];
				$obj->soldPrice = $row['prod_price'];
				$obj->quantity = $row['prod_qty'];
				$obj->date = $row['order_date'];
				array_push($Arr, $obj);
			}
		}
		$obj = new StdClass();
		$obj->msg= "fetched all orders successfull.";
        $obj->type= "success";
        $obj->orders=$Arr;
        echo json_encode($obj);
	}else{
		$obj = new StdClass();
		$obj->msg= "Invalid credentials. server cant handle this request";
        $obj->type= "error";
        echo json_encode($obj);
	}
}
?>