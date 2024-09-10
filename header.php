<?php
session_start();
include "db.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<!-- ... existing meta tags and links ... -->
	<title>Online Shopping</title>
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
	<link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
	<link type="text/css" rel="stylesheet" href="css/slick.css" />
	<link type="text/css" rel="stylesheet" href="css/slick-theme.css" />
	<link type="text/css" rel="stylesheet" href="css/nouislider.min.css" />
	<link rel="stylesheet" href="css/font-awesome.min.css">
	<link type="text/css" rel="stylesheet" href="css/style.css" />
	<link type="text/css" rel="stylesheet" href="css/accountbtn.css" />

	<style>
		#navigation {
			background: linear-gradient(to right, #F9D423, #FF4E50);
		}

		#header {
			background: linear-gradient(to right, #F9D423, #F9D423);
		}

		.order_qtyst {
			background-color: #D10024;
			width: 20px;
			height: 20px;
			text-align: center;
			color: white;
			border-radius: 50px;
			position: absolute;
			top: 10px;
		}

		#top-header {
			background: linear-gradient(to right, #FF8225, #FF8225);
		}

		#footer {
			background: linear-gradient(to right, #348AC7, #7474BF);
			color: #1E1F29;
		}

		#bottom-footer {
			background: linear-gradient(to right, #348AC7, #7474BF);
		}

		.footer-links li a {
			color: #1E1F29;
		}

		.mainn-raised {
			margin: -7px 0px 0px;
			border-radius: 6px;
			box-shadow: 0 16px 24px 2px rgba(0, 0, 0, 0.14), 0 6px 30px 5px rgba(0, 0, 0, 0.12), 0 8px 10px -5px rgba(0, 0, 0, 0.2);
		}

		.glyphicon {
			display: inline-block;
			font: normal normal normal 14px/1 FontAwesome;
			font-size: inherit;
			text-rendering: auto;
			-webkit-font-smoothing: antialiased;
			-moz-osx-font-smoothing: grayscale;
		}

		.glyphicon-chevron-left:before {
			content: "\f053";
		}

		.glyphicon-chevron-right:before {
			content: "\f054";
		}

		/* Modal styles */
		.modal-content {
			background-color: #fefefe;
			margin: 15% auto;
			padding: 20px;
			border: 1px solid #888;
			width: 80%;
		}

		.modal-header {
			display: flex;
			/* justify-content: space-between; */
			align-items: center;
		}

		.modal-title {
			margin: 0;
		}

		.modal-body {
			max-height: 60vh;
			overflow-y: auto;
		}
	</style>
</head>

<body>
	<!-- HEADER -->
	<header>
		<!-- TOP HEADER -->
		<div id="top-header">
			<div class="container">
				<ul class="header-links pull-left">
					<li><a href="#"><i class="fa fa-phone"></i> +95941009461</a></li>
					<li><a href="#"><i class="fa fa-envelope-o"></i> kaungsattoo@ucstgi.edu.mm</a></li>
					<li><a href="#"><i class="fa fa-map-marker"></i>Taunggyi, Myanmar</a></li>
				</ul>
				<ul class="header-links pull-right">
					<li><a href="#"><i class="fa fa-inr"></i> MMK</a></li>
					<li>
						<?php
						if (isset($_SESSION['uid'])) {
							$result = mysqli_query($con, "SELECT otp FROM user_otp WHERE user_id = '$_SESSION[uid]'");
							$row = mysqli_fetch_assoc($result);
							$stored_otp = $row['otp'];
						}
						if (isset($_SESSION["uid"]) && ($_SESSION["stored_otp"]) == $stored_otp) {
							$sql = "SELECT first_name FROM user_info WHERE user_id='$_SESSION[uid]'";
							$query = mysqli_query($con, $sql);
							$row = mysqli_fetch_array($query);

							echo '
                               <div class="dropdownn">
                                  <a href="#" class="dropdownn" data-toggle="modal" data-target="#myModal" ><i class="fa fa-user-o"></i> HI ' . $row["first_name"] . '</a>
                                  <div class="dropdownn-content">
                                    <a href="" data-toggle="modal" data-target="#profile"><i class="fa fa-user-circle" aria-hidden="true" ></i>My Profile</a>
                                    <a href="logout.php"  ><i class="fa fa-sign-in" aria-hidden="true"></i>Log out</a>
                                  </div>
                                </div>';
						} else {
							echo '
                                <div class="dropdownn">
                                  <a href="#" class="dropdownn" data-toggle="modal" data-target="#myModal" ><i class="fa fa-user-o"></i> My Account</a>
                                  <div class="dropdownn-content">
                                    <a href="" data-toggle="modal" data-target="#Modal_login"><i class="fa fa-sign-in" aria-hidden="true" ></i>Login</a>
                                    <a href="" data-toggle="modal" data-target="#Modal_register"><i class="fa fa-user-plus" aria-hidden="true"></i>Register</a>
                                  </div>
                                </div>';
						}
						?>
					</li>
				</ul>
			</div>
		</div>
		<!-- /TOP HEADER -->

		<!-- MAIN HEADER -->
		<div id="header">
			<div class="container">
				<div class="row">
					<!-- LOGO -->
					<div class="col-md-3">
						<div class="header-logo">
							<a href="index.php" class="logo">
								<font style="font-style:normal; font-size: 33px;color: #000000;font-family: serif">
									MegaShop
								</font>
							</a>
						</div>
					</div>
					<!-- /LOGO -->

					<!-- SEARCH BAR -->
					<div class="col-md-6">
						<div class="header-search">
							<form>
								<select class="input-select">
									<option value="0">All Categories</option>
									<option value="1">Men</option>
									<option value="1">Women </option>
								</select>
								<input class="input" id="search" type="text" placeholder="Search here">
								<button type="submit" id="search_btn" class="search-btn">Search</button>
							</form>
						</div>
					</div>
					<!-- /SEARCH BAR -->

					<!-- ACCOUNT -->
					<div class="col-md-3 clearfix">
						<div class="header-ctn">
							<div id="orderss">
								<span>
									<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24">
										<path fill="white" d="m21.706 5.292l-2.999-2.999A1 1 0 0 0 18 2H6a1 1 0 0 0-.707.293L2.294 5.292A1 1 0 0 0 2 6v13c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6a1 1 0 0 0-.294-.708M6.414 4h11.172l1 1H5.414zM4 19V7h16l.002 12z" />
										<path fill="white" d="M14 9h-4v3H7l5 5l5-5h-3z" />
									</svg>
								</span>
								<span id="order_qty" class="order_qtyst">0</span>
								<span style="color:white; cursor: pointer;">Your Orders</span>
							</div>
							<!-- Cart -->
							<div class="dropdown">
								<a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
									<i class="fa fa-shopping-cart"></i>
									<span>Your Cart</span>
									<div class="badge qty">0</div>
								</a>
								<div class="cart-dropdown">
									<div class="cart-list" id="cart_product">
									</div>
									<div class="cart-btns">
										<a href="cart.php" style="width:100%;"><i class="fa fa-edit"></i> edit cart</a>
									</div>
								</div>
							</div>
							<!-- /Cart -->

							<!-- Menu Toogle -->
							<div class="menu-toggle">
								<a href="#">
									<i class="fa fa-bars"></i>
									<span>Menu</span>
								</a>
							</div>
							<!-- /Menu Toogle -->
						</div>
					</div>
					<!-- /ACCOUNT -->
				</div>
				<!-- row -->
			</div>
			<!-- container -->
		</div>
		<!-- /MAIN HEADER -->
	</header>
	<!-- /HEADER -->

	<nav id='navigation'>
		<div class="container" id="get_category_home">
		</div>
	</nav>

	<!-- NAVIGATION -->

	<!-- Existing modals for login and registration -->
	<div class="modal fade" id="Modal_login" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<?php include "login_form.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="Modal_register" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<?php include "register_form.php"; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Orders Modal -->
	<div id="ordersModal" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document" style="width:1000px">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Your Orders</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="    margin-left: 87%;
">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="ordersContent">
						<!-- Orders will be loaded here via JavaScript -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- JavaScript for fetching and displaying orders -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			fetch('fetch_orders.php')
				.then(response => response.json())
				.then(data => {
					document.getElementById('order_qty').innerHTML = data.orders.length; // Update order quantity
				})
		})
		document.querySelector('#orderss').addEventListener('click', function() {
			$('#ordersModal').modal('show');

			// Fetch and display orders
			fetch('fetch_orders.php')
				.then(response => response.json())
				.then(data => {
					let content = '<table class="table table-condensed">' +
						'<thead>' +
						'<tr>' +
						'<th>Order ID</th>' +
						'<th>Product Title</th>' +
						'<th>Quantity</th>' +
						'<th>Transaction ID</th>' +
						'<th>Status</th>' +
						'</tr>' +
						'</thead>' +
						'<tbody>';

					data.orders.forEach(order => {
						content += '<tr>' +
							'<td>' + order.order_id + '</td>' +
							'<td>' + order.product_title + '</td>' + // Use product title instead of ID
							'<td>' + order.qty + '</td>' +
							'<td>' + order.trx_id + '</td>' +
							'<td>' + order.p_status + '</td>' +
							'</tr>';
					});

					content += '</tbody></table>';
					document.getElementById('order_qty').innerHTML = data.orders.length
					document.getElementById('ordersContent').innerHTML = content;
				})
				.catch(error => console.error('Error fetching orders:', error));
		});
	</script>
</body>

</html>