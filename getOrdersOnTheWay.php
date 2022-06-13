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


function getSupplierDetails($id){
	include 'connect.php';
	// include 'fxs.php';
	$obj = new StdClass();
	$qx = mysqli_query($conn, "select * from supplier where supplier_id='$id'");
	if(mysqli_num_rows($qx) === 1){
		while ($row = mysqli_fetch_assoc($qx)) {
			$obj->id = $row['supplier_id'];
			$obj->name = $row['company_name'];
			$obj->phone = $row['supplier_contact'];
			$obj->email = $row['supplier_email'];
			$obj->address = $row['supplier_address'];
		}
	}else{
		$obj->id = "Not found";
		$obj->name = "Not found";
		$obj->phone = "Not found";
		$obj->email = "Not found";
		$obj->address = "Not found";
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
		$qx = mysqli_query($conn, "select * from orders where payment_status='paid' and delivery_status='on the way' and driver_id='$userId' order by order_id desc");
		if(mysqli_num_rows($qx) > 0){
			while ($row = mysqli_fetch_assoc($qx)) {
				$obj = new StdClass();
				$obj->orderId = $row['order_id'];
				$obj->orderDetails = getOrderDetails($row['order_id']);
				$obj->client = getClientDetails($row['client_id']);
				$obj->transactionId =  $row['payment_reference'];
				$obj->supplier = getSupplierDetails($row['supplier_id']);
				$obj->totalAmount = $row['total_amount'];
				$obj->lat = $row['delivery_lat'];
				$obj->long = $row['delivery_long'];
				$obj->address = $row['delivery_address'];
				$obj->moreInfo = $row['delivery_more_info'];
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