<?php
include "db.php";
include "header.php";
?>

<style>
	.row-checkout {
		display: flex;
		flex-wrap: wrap;
		margin: 0 -16px;
	}

	.col-25 {
		flex: 25%;
	}

	.col-50 {
		flex: 50%;
	}

	.col-75 {
		flex: 75%;
	}

	.col-25,
	.col-50,
	.col-75 {
		padding: 0 16px;
	}

	.container-checkout {
		background-color: #f2f2f2;
		padding: 5px 20px 15px 20px;
		border: 1px solid lightgrey;
		border-radius: 3px;
	}

	input[type=text] {
		width: 100%;
		margin-bottom: 20px;
		padding: 12px;
		border: 1px solid #ccc;
		border-radius: 3px;
	}

	label {
		margin-bottom: 10px;
		display: block;
	}

	.icon-container {
		margin-bottom: 20px;
		padding: 7px 0;
		font-size: 24px;
	}

	.checkout-btn {
		background-color: #4CAF50;
		color: white;
		padding: 12px;
		margin: 10px 0;
		border: none;
		width: 100%;
		border-radius: 3px;
		cursor: pointer;
		font-size: 17px;
	}

	.checkout-btn:hover {
		background-color: #45a049;
	}

	hr {
		border: 1px solid lightgrey;
	}

	span.price {
		float: right;
		color: grey;
	}

	@media (max-width: 800px) {
		.row-checkout {
			flex-direction: column-reverse;
		}

		.col-25 {
			margin-bottom: 20px;
		}
	}
</style>

<section class="section">

	<div class="container-fluid">
		<div class="row-checkout">
			<?php
			if (isset($_SESSION["uid"])) {
				$sql = "SELECT * FROM user_info WHERE user_id='$_SESSION[uid]'";
				$query = mysqli_query($con, $sql);
				$row = mysqli_fetch_array($query);

				echo '
            <div class="col-75">
                <div class="container-checkout">
                <form id="checkout_form" action="checkout_process.php" method="POST" class="was-validated">

                    <div class="row-checkout">
                    
                    <div class="col-50">
                        <h3>Billing Address</h3>
                        <label for="fname"><i class="fa fa-user"></i> Full Name</label>
                        <input type="text" id="fname" class="form-control" name="firstname" pattern="^[a-zA-Z ]+$"  value="' . $row["first_name"] . ' ' . $row["last_name"] . '">
                        <label for="email"><i class="fa fa-envelope"></i> Email</label>
                        <input type="text" id="email" name="email" class="form-control" pattern="^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9]+(\.[a-z]{2,4})$" value="' . $row["email"] . '" required>
                        <label for="adr"><i class="fa fa-address-card-o"></i> Address</label>
                        <input type="text" id="adr" name="address" class="form-control" value="' . $row["address1"] . '" required>
                        <label for="city"><i class="fa fa-institution"></i> City</label>
                        <input type="text" id="city" name="city" class="form-control" value="' . $row["address2"] . '" pattern="^[a-zA-Z ]+$" required>

                        <div class="row">
                        <div class="col-50">
                            <label for="state">State</label>
                            <input type="text" id="state" name="state" class="form-control" pattern="^[a-zA-Z ]+$" required>
                        </div>
                        </div>
                    </div>
                    
                    <div class="col-50">
                        <h3>Payment</h3>
                        <label for="fname">Accepted Cards</label>
                        <div class="icon-container">
                            <span><img src="./img/kbz.png" alt="Accepted Cards" width="100" height="48" style="border-radius: 5px;"></span>
                            <span><img src="./img/wave.png" alt="Accepted Cards" width="100" height="48" style="border-radius: 5px;"></span>
                            <span><img src="./img/cblogo.png" alt="Accepted Cards" width="100" height="48" style="border-radius: 5px;"></span>
                            <span><img src="./img/ayalogo.png" alt="Accepted Cards" width="110" height="57" style="border-radius: 5px; margin-top:6px"></span>
                        </div>

                        <label for="cname">Phone Number</label>
                        <input type="text" id="cname" name="phoneNumber" class="form-control" required>
                        
                        <div class="form-group" id="card-number-field">
                            <label for="cardNumber">Transaction Number</label>
                            <input type="text" class="form-control" id="cardNumber" name="cardNumber" required>
                        </div>

                        <div class="form-group">
                            <label for="">Payment Slip Photo</label>
                            <input type="file" class="form-control" id="imageUpload">
                            <img id="imagePreview" src="" alt="Image Preview" style="display: none; width: 300px; height: 500px;" class="form-control">
                        </div>
                    </div>
                    </div>
                    <label><input type="CHECKBOX" name="q" class="roomselect" value="conform" required> Shipping address same as billing</label>
					<script>
					document.getElementById("imageUpload").addEventListener("change", function(event) {
						const file = event.target.files[0];
						if (file) {
							const reader = new FileReader();
							reader.onload = function(e) {
								const preview = document.getElementById("imagePreview");
								preview.src = e.target.result;
								preview.style.display = "block";
							};
							reader.readAsDataURL(file);
						}
					});
				</script>';

				$i = 1;
				$total = 0;
				$total_count = $_POST['total_count'];
				while ($i <= $total_count) {
					$item_name_ = $_POST['item_name_' . $i];
					$amount_ = $_POST['amount_' . $i];
					$quantity_ = $_POST['quantity_' . $i];
					$total = $total + $amount_;
					$sql = "SELECT product_id FROM products WHERE product_title='$item_name_'";
					$query = mysqli_query($con, $sql);
					$row = mysqli_fetch_array($query);
					$product_id = $row["product_id"];
					echo "    
                        <input type='hidden' name='prod_id_$i' value='$product_id'>
                        <input type='hidden' name='prod_price_$i' value='$amount_'>
                        <input type='hidden' name='prod_qty_$i' value='$quantity_'>
                        ";
					$i++;
				}

				echo '    
                <input type="hidden" name="total_count" value="' . $total_count . '">
                <input type="hidden" name="total_price" value="' . $total . '">
                <input type="submit" id="submit" value="Continue to checkout" class="checkout-btn">
                </form>
                </div>
            </div>';
			} else {
				echo "<script>window.location.href = 'cart.php'</script>";
			}
			?>

			<div class="col-25">
				<div class="container-checkout">
					<?php
					if (isset($_POST["cmd"])) {

						$user_id = $_POST['custom'];

						$i = 1;
						echo "
                        <h4>Cart 
                        <span class='price' style='color:black'>
                        <i class='fa fa-shopping-cart'></i> 
                        <b>$total_count</b>
                        </span>
                        </h4>

                        <table class='table table-condensed'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Product Title</th>
                                <th>Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        ";
						$total = 0;
						while ($i <= $total_count) {
							$item_name_ = $_POST['item_name_' . $i];
							$item_number_ = $_POST['item_number_' . $i];
							$amount_ = $_POST['amount_' . $i];
							$quantity_ = $_POST['quantity_' . $i];
							$total = $total + $amount_;
							$sql = "SELECT product_id FROM products WHERE product_title='$item_name_'";
							$query = mysqli_query($con, $sql);
							$row = mysqli_fetch_array($query);
							$product_id = $row["product_id"];
							echo "
                                <tr>
                                    <td>$i</td>
                                    <td>$item_name_</td>
                                    <td>$quantity_</td>
                                    <td>$amount_</td>
                                </tr>";
							$i++;
						}
						echo "
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan='2'></th>
                                <th>Total</th>
                                <th>$total</th>
                            </tr>
                        </tfoot>
                        </table>
                        ";
					}
					?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
include "footer.php";
?>