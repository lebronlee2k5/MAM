<?php

$DBConnect = mysqli_connect("localhost", "root", "", "shop_mam");

if (!$DBConnect) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>