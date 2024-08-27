<?php
include "db.php";
session_start();

if (isset($_POST["email"]) && isset($_POST["password"])) {
	$email = mysqli_real_escape_string($con, $_POST["email"]);
	$password = mysqli_real_escape_string($con, $_POST["password"]); // Prevent SQL injection
	$sql_one = "SELECT * FROM user_info WHERE email = '$email' AND password = '$password'";
	$run_query = mysqli_query($con, $sql_one);
	$count = mysqli_num_rows($run_query);

	if ($count == 1) {
		$row = mysqli_fetch_array($run_query);
		$_SESSION["uid"] = $row["user_id"];
		$_SESSION["name"] = $row["first_name"];
		$ip_add = getenv("REMOTE_ADDR");

		// Update the cart with the current user's session ID
		$sql_two = "UPDATE cart SET user_id = '$_SESSION[uid]' WHERE ip_add='$ip_add' AND user_id = -1";
		mysqli_query($con, $sql_two);

		// Generate a new OTP
		$otp = rand(100000, 999999);

		// Check if an old OTP exists for the user and delete it if it does
		$check_sql = "SELECT * FROM user_otp WHERE user_id = '$_SESSION[uid]'";
		$check_result = mysqli_query($con, $check_sql);

		if (mysqli_num_rows($check_result) > 0) {
			$delete_sql = "DELETE FROM user_otp WHERE user_id = '$_SESSION[uid]'";
			mysqli_query($con, $delete_sql);
		}

		// Insert the new OTP into the database
		$otp_sql = "INSERT INTO user_otp (user_id, otp) VALUES ('$_SESSION[uid]', '$otp')";
		mysqli_query($con, $otp_sql);

		// Handle product list from the cookie if it exists
		if (isset($_COOKIE["product_list"])) {
			$p_list = stripcslashes($_COOKIE["product_list"]);
			$product_list = json_decode($p_list, true);

			foreach ($product_list as $product_id) {
				// Check if the product already exists in the user's cart
				$verify_cart = "SELECT id FROM cart WHERE user_id = $_SESSION[uid] AND p_id = $product_id";
				$result = mysqli_query($con, $verify_cart);

				if (mysqli_num_rows($result) < 1) {
					// Update cart with the valid user ID for new products
					$update_cart = "UPDATE cart SET user_id = '$_SESSION[uid]' WHERE ip_add = '$ip_add' AND user_id = -1 AND p_id = $product_id";
					mysqli_query($con, $update_cart);
				} else {
					// Delete the product record with the anonymous user ID
					$delete_existing_product = "DELETE FROM cart WHERE user_id = -1 AND ip_add = '$ip_add' AND p_id = $product_id";
					mysqli_query($con, $delete_existing_product);
				}
			}

			// Destroy the product list cookie after processing
			setcookie("product_list", "", strtotime("-1 day"), "/");
			echo "cart_login";
			exit();
		}

		// If login is successful, redirect to OTP verification page
		echo "login_success";
		echo "<script> location.href='verify_otp.php'; </script>";
		exit();
	} else {
		// Check if the login attempt is for an admin
		$password_md5 = md5($password); // Apply MD5 hashing
		$sql_one = "SELECT * FROM admin_info WHERE admin_email = '$email' AND admin_password = '$password_md5'";
		$run_query = mysqli_query($con, $sql_one);
		$count = mysqli_num_rows($run_query);

		if ($count == 1) {
			$row = mysqli_fetch_array($run_query);
			$_SESSION["uid"] = $row["admin_id"];
			$_SESSION["name"] = $row["admin_name"];

			echo "login_success";
			echo "<script> location.href='admin/addproduct.php'; </script>";
			exit();
		} else {
			echo "<span style='color:red;'>Please register before login..!</span>";
			exit();
		}
	}
}
