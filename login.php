<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"), true);

include 'connect.php';

if (isset($data["email"])) {
	$email = mysqli_real_escape_string($conn,$data['email']);
	$password = mysqli_real_escape_string($conn,$data['password']);
	$q = mysqli_query($conn, "select * from delivery_man where delivery_man_email='$email' or delivery_man_contact='$email' and password='".md5($password)."' and is_disabled='0'");
	if(mysqli_num_rows($q) == 1){
		while ($row = mysqli_fetch_assoc($q)) {
			//user object
			$userObj = new StdClass();
			$userObj->id = $row['delivery_man_id'];
			$userObj->name = is_null($row['delivery_man_name'])?'':$row['delivery_man_name'];
			$userObj->nationalId = is_null($row['delivery_man_national_id'])?'':$row['delivery_man_national_id'];
			$userObj->phone = is_null($row['delivery_man_contact'])?'':$row['delivery_man_contact'];
			$userObj->email = is_null($row['delivery_man_email'])?'':$row['delivery_man_email'];
			$userObj->image = is_null($row['delivery_man_image'])?'':$row['delivery_man_image'];
			$userObj->availability = is_null($row['is_available'])?'':$row['is_available'];
			//user object

			$obj = new StdClass();
			$obj->msg = "Logged in successful";
			$obj->user = $userObj;
			$obj->type = "success";
			echo json_encode($obj);
		}	
	}else{
		$obj = new StdClass();
		$obj->msg= "Invalid credentials.";
        $obj->type= "error";
        echo json_encode($obj);
	}
}

?>