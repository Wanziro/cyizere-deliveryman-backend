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
			$obj->description = $row['description'];
			$obj->images = getProductImages($row['prod_id']);
		}
	}else{
		$Arr = array();
		$obj->id = "Not found";
		$obj->name = "Not found";
		$obj->price = "Not found";
		$obj->description = "Not found";
		$obj->images = $Arr;
	}
	return $obj;
}

function getOrderDetails($id){
	include 'connect.php';
	// include 'fxs.php';
	$Arr = array();
	$qx = mysqli_query($conn, "select * from order_details,orders where order_details.order_id=orders.order_id and order_details.order_id='$id'");
	if(mysqli_num_rows($qx) > 0){
		while ($row = mysqli_fetch_assoc($qx)) {
			$obj = new StdClass();
			$obj->id = $row['order_details_id'];
			$obj->product = getProductDetails($row['prod_id']); 
			$obj->soldPrice = $row['prod_price'];
			$obj->soldQuantity = $row['prod_qty'];
			array_push($Arr, $obj);
		}
	}
	return $Arr;
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
		$qx = mysqli_query($conn, "select * from orders where payment_status='paid' and delivery_status='pending' and driver_id='-' order by order_id desc");
		if(mysqli_num_rows($qx) > 0){
			while ($row = mysqli_fetch_assoc($qx)) {
				$obj = new StdClass();
				$obj->orderId = $row['order_id'];
				$obj->orderDetails = getOrderDetails($row['order_id']);
				$obj->client = getClientDetails($row['client_id']);
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