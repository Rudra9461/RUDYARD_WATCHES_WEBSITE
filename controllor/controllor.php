<?php 
include('../config/config.php');


// sign in form //

if(isset($_REQUEST['signup'])) {

	$fname = $_REQUEST['fname'];
	$lname = $_REQUEST['lname'];
	$contact= $_REQUEST['contact'];
	$email = $_REQUEST['email'];
	$password = $_REQUEST['password'];
	$address = $_REQUEST['address'];
	$pin_code = $_REQUEST['pin_code'];
	

	if(empty($fname) && empty($email) && empty($contact) && empty($lname) && empty($password) && empty($address) && empty($pin_code)) {
		$_SESSION['error'] = "Please fill the blank field";
		header("location:".$siteurl."/signinform.php");
	}else {
		$sql  = "INSERT INTO table_signin_form(id_fname, id_email, id_contact ,id_lname ,id_password,id_address, id_pin_code) VALUES('$fname', '$email', '$contact' , '$lname' ,'$password','$address','$pin_code')";

		if(mysqli_query($conn, $sql)) {
			echo "Thank You For Sign In . Your Data Submitted Successfully";
			//header("location:../book-now.php");
		}else {
			 echo "Error: " . $sql . "<br>" . mysqli_error($conn);
		}
	}	

}
 