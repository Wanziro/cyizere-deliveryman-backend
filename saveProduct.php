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
	$name = mysqli_real_escape_string($conn,$data['productName']);
	$description = mysqli_real_escape_string($conn,$data['description']);
	$categoryId = mysqli_real_escape_string($conn,$data['categoryId']);
	$subCategoryId = mysqli_real_escape_string($conn,$data['subCategoryId']);
	$quantity = mysqli_real_escape_string($conn,$data['quantity']);
	$price = mysqli_real_escape_string($conn,$data['price']);
	
	if (validateUser($email,$userId)) {
		$date = date("d-m-Y H:i");
		if(trim($name) != ''){
			if(trim($subCategoryId) != ''){
				$q = mysqli_query($conn, "insert into product(product_name,product_qty,product_price,supplier_id,category_id,sub_category_id,is_deleted,is_hidden,recorded_date,recorded_by,description) values('$name','$quantity','$price','$categoryId','$subCategoryId','0','0','0','$date','$email','$description')");
			}else{
				$q = mysqli_query($conn, "insert into product(product_name,product_qty,product_price,supplier_id,category_id,is_deleted,is_hidden,recorded_date,recorded_by,description) values('$name','$quantity','$price','$categoryId','0','0','0','$date','$email','$description')");
			}
			if($q){
				//select product id
				$get = mysqli_query($conn,"SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA='".$DB_NAME."' AND TABLE_NAME='product'");
				while($row_id = mysqli_fetch_assoc($get)){
					$id = $row_id['AUTO_INCREMENT'];
				}

				$obj = new StdClass();
				$obj->msg = "Your product has been saved successful.";
				$obj->type = "success";
				$obj->productId = $id - 1;
				echo json_encode($obj);
			}else{
				$obj = new StdClass();
				$obj->msg = "Something went wrong. Try again later after some time.";
				$obj->type = "error";
				echo json_encode($obj);
			}
		}else{
			$obj = new StdClass();
			$obj->msg= "Sever rejected to save the product because it is empty";
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