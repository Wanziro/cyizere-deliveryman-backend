<?php
function markAllCodesAsExpired($userId){
	include 'connect.php';
	mysqli_query($conn, "update login_codes set expired='Yes' where user_id='$userId'");
}

function validateUser($email,$userId){
	include 'connect.php';
	$q = mysqli_query($conn, "select * from delivery_man where delivery_man_id='$userId' and delivery_man_email='$email'");
	if (mysqli_num_rows($q) == 1) {
		return true;
	}else{
		return false;
	}
}

function getUserImage($username) {
	include 'connect.php';
	$q = mysqli_query($conn, "select * from users where username='$username'");
	if (mysqli_num_rows($q) == 1) {
		while ($row = mysqli_fetch_assoc($q)) {			 
			return $row['image'];
		}
	}else{
		return null;
	}
}

function getProductImages($id){
	include 'connect.php';
	$Arr = array();
	$qx = mysqli_query($conn, "select * from product_image where product_id='$id'");
	if(mysqli_num_rows($qx) > 0){
		while ($row = mysqli_fetch_assoc($qx)) {
			$obj = new StdClass();
			$obj->id = $row['image_id'];
			$obj->name = $row['image'];
			array_push($Arr, $obj);
		}
	}
	return $Arr;
}

function userObject($username){
	include 'connect.php';
	$q = mysqli_query($conn, "select * from users where username='$username'");
	if (mysqli_num_rows($q) == 1) {
		while ($row = mysqli_fetch_assoc($q)) {
			$obj = new StdClass();
			$obj->id = $row['id'];
			$obj->fname = $row['fname'];
			$obj->lname = $row['lname'];
			$obj->phone = $row['phone'];
			$obj->email = $row['email'];
			$obj->username = $row['username'];
			$obj->image = $row['image'];
			return $obj;
		}
	}else{
		$obj = new StdClass();
		$obj->id = "null";
		$obj->fname ="null";
		$obj->lname = "null";
		$obj->phone = "null";
		$obj->email = "null";
		$obj->email = "null";
		$obj->image = "null";
		$obj->fakeUsername = $username;
		return $obj;
	}
}
?>