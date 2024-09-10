<?php
include "db.php";
session_start();

if (isset($_SESSION['uid'])) {
    $user_id = $_SESSION['uid'];

    // Fetch orders for the logged-in user
    $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $sql);

    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }

    // Fetch product titles for the products in the orders
    $product_ids = array_column($orders, 'product_id');
    $product_ids = implode(',', array_map('intval', $product_ids)); // Sanitize and format

    $sql = "SELECT product_id, product_title FROM products WHERE product_id IN ($product_ids)";
    $result = mysqli_query($con, $sql);
    $product_titles = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $product_titles[$row['product_id']] = $row['product_title'];
    }

    // Add product titles to the orders
    foreach ($orders as &$order) {
        $order['product_title'] = isset($product_titles[$order['product_id']]) ? $product_titles[$order['product_id']] : 'Unknown';
    }

    // Return the orders with product titles
    echo json_encode(['orders' => $orders]);
} else {
    echo json_encode(['orders' => []]);
}
