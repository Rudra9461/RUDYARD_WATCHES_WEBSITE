<?php

if(isset($_POST['sumbit'])){

	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$contact = $_POST['contact'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	$address = $_POST['address'];
	$pin_code = $_POST['pin_code'];

	if(empty($fname) && empty($lname) && empty($contact) && empty($email) && empty($password) && empty($address) && empty($pin_code) ){
		echo '<script>alert("please fill the blank field");</script>';
	}else{
		if (is_numeric($contact)) {
			
		}else{
			echo '<script>alert("Please enter only numbers");</script>';
		}
	}

}

echo('THANK YOU FOR VISITING. YOUR APPLICATION WILL BE FORWARDED TO OUR SENIOR STAFF VERY SOON....')
?>