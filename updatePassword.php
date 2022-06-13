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
	$currentPassword = mysqli_real_escape_string($conn,$data['currentPassword']);
	$newPassword = mysqli_real_escape_string($conn,$data['newPassword']);
	
	if (validateUser($email,$userId)) {
		if(trim($newPassword) != ''){	
			$qq = mysqli_query($conn,"select * from delivery_man where delivery_man_id='$userId' and delivery_man_email='$email'");
			if(mysqli_num_rows($qq) == 1){
				while($r = mysqli_fetch_assoc($qq)){
					if(md5($currentPassword) == $r['password']){
						$q = mysqli_query($conn, "update delivery_man set password='".md5($newPassword)."' where delivery_man_id='$userId' and delivery_man_email='$email'");
						if($q){
							$obj = new StdClass();
							$obj->msg = "Your password has been updated successful.";
							$obj->type = "success";
							echo json_encode($obj);
						}else{
							$obj = new StdClass();
							$obj->msg = "Something went wrong. Try again later after some time.";
							$obj->type = "error";
							echo json_encode($obj);
						}
					}else{
						$obj = new StdClass();
						$obj->msg = "Wrong old password.";
						$obj->type = "error";
						echo json_encode($obj);
					}
				}
			}else{
				$obj = new StdClass();
				$obj->msg= "Invalid info, Please try again later";
		        $obj->type= "error";
		        echo json_encode($obj);
			}
		}else{
			$obj = new StdClass();
			$obj->msg= "Sever rejected to update the product because it is empty";
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