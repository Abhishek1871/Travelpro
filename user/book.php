<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $place_id = $_POST['place_id'];
    $from_place = $_POST['from_place'];
    $total_distance = $_POST['total_distance'];
    $num_people = $_POST['num_people'];
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $total_price = $_POST['total_price'];

    $vehicle_id = $_POST['vehicle_id'];

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, place_id, vehicle_id, from_place, total_distance, num_people, from_date, to_date, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iiisiiissd", $user_id, $place_id, $vehicle_id, $from_place, $total_distance, $num_people, $from_date, $to_date, $total_price);

    if ($stmt->execute()) {
        header("Location: my_bookings.php?msg=booked");
    }
    else {
        echo "Error: " . $conn->error;
    }
}
?>
