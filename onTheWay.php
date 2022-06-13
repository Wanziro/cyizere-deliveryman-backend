<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

include 'connect.php';
include 'fxs.php';

if (isset($data["email"])) {
	$email = mysqli_real_escape_string($conn,$data['email']);
	$userId = mysqli_real_escape_string($conn,$data['userId']);
	$orderId = mysqli_real_escape_string($conn,$data['orderId']);
	if (validateUser($email,$userId)) {	
		$qq = mysqli_query($conn,"update orders set delivery_status='on the way' where driver_id='$userId' and order_id='$orderId'");
		if($qq){
			$obj = new StdClass();
			$obj->msg = "Order updated successful.";
			$obj->type = "success";
			echo json_encode($obj);
		}else{
			$obj = new StdClass();
			$obj->msg= "Something went wrong while updating order, Please try again later";
	        $obj->type= "error";
	        echo json_encode($obj);
		}
		
	}else{
		$obj = new StdClass();
		$obj->msg= "Invalid credentials. server cant handle this request";
        $obj->type= "error";
        echo json_encode($obj);
	}
}

?>