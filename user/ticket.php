<?php
session_start();
include '../config/db.php';

session_start();
include '../config/db.php';

// Allow User OR Admin to access
$user_id = $_SESSION['user_id'] ?? null;
$is_admin = isset($_SESSION['admin_logged_in']);

if (!$user_id && !$is_admin) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Invalid Booking ID";
    exit();
}

$booking_id = $_GET['id'];

// Fetch booking details
if ($is_admin) {
    // Admin can see any booking
    $sql = "SELECT b.*, p.name as place_name, p.image_path, u.name as user_name, v.name as vehicle_name, v.type as vehicle_type
            FROM bookings b 
            JOIN places p ON b.place_id = p.id 
            JOIN users u ON b.user_id = u.id
            LEFT JOIN vehicles v ON b.vehicle_id = v.id
            WHERE b.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
}
else {
    // Users can only see their own
    $sql = "SELECT b.*, p.name as place_name, p.image_path, v.name as vehicle_name, v.type as vehicle_type
            FROM bookings b 
            JOIN places p ON b.place_id = p.id 
            LEFT JOIN vehicles v ON b.vehicle_id = v.id
            WHERE b.id = ? AND b.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $booking_id, $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Booking not found or access denied.";
    exit();
}

$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt #<?php echo $booking_id; ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap'); 
        * { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100 p-8" onload="window.print()">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-lg border-t-8 border-purple-600">
        <div class="flex justify-between items-center mb-8 border-b pb-6">
            <div class="flex items-center space-x-2">
                <i class="fas fa-plane-departure text-3xl text-purple-600"></i>
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">TravelPro</h1>
                    <p class="text-gray-500 text-sm">Official Booking Receipt</p>
                </div>
            </div>
            <div class="text-right">
                <p class="font-mono text-xl font-bold text-purple-600">#<?php echo $booking['id']; ?></p>
                <p class="text-sm text-gray-400">Date: <?php echo date('M d, Y', strtotime($booking['created_at'])); ?></p>
            </div>
        </div>
        
        <div class="flex gap-6 mb-8">
            <img src="../<?php echo htmlspecialchars($booking['image_path']); ?>" class="w-32 h-32 rounded-lg object-cover bg-gray-200">
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-gray-800 mb-1"><?php echo htmlspecialchars($booking['place_name']); ?></h2>
                <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mt-2">
                    <p class="col-span-2 text-lg font-bold text-purple-700 border-b pb-1 mb-2"><i class="fas fa-user-tag mr-2"></i>Passenger: <?php echo htmlspecialchars($booking['passenger_name'] ?? 'N/A'); ?></p>
                    <p><i class="fas fa-map-marker-alt mr-2 text-purple-500"></i>From: <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['from_place']); ?></span></p>
                    <p><i class="fas fa-road mr-2 text-purple-500"></i>Distance: <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['total_distance']); ?> Km</span></p>
                    <p><i class="fas fa-users mr-2 text-purple-500"></i>Travelers: <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['num_people']); ?></span></p>
                    <p><i class="fas fa-bus mr-2 text-purple-500"></i>Vehicle: <span class="font-semibold text-gray-800">
                        <?php echo htmlspecialchars($booking['vehicle_name'] ?? 'N/A') . ' (' . htmlspecialchars($booking['vehicle_type'] ?? '') . ')'; ?>
                    </span></p>
                    <p><i class="fas fa-info-circle mr-2 text-purple-500"></i>Status: <span class="font-semibold uppercase <?php echo $booking['status'] == 'Confirmed' ? 'text-green-600' : 'text-gray-600'; ?>"><?php echo $booking['status']; ?></span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-8 bg-purple-50 p-6 rounded-xl border border-purple-100">
            <div>
                <p class="text-xs text-purple-500 uppercase tracking-wider font-semibold">Check-in Date</p>
                <p class="font-bold text-lg text-gray-800"><?php echo $booking['from_date']; ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs text-purple-500 uppercase tracking-wider font-semibold">Check-out Date</p>
                <p class="font-bold text-lg text-gray-800"><?php echo $booking['to_date']; ?></p>
            </div>
        </div>

        <div class="flex justify-between items-center border-t border-dashed border-gray-300 pt-6">
            <div>
                <p class="text-gray-600 text-sm">Payment Method</p>
                <p class="font-semibold uppercase text-gray-800"><i class="fas fa-wallet mr-2 text-gray-400"></i><?php echo htmlspecialchars($booking['payment_method'] ?? 'N/A'); ?></p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500 mb-1">Total Paid Amount</p>
                <p class="text-4xl font-bold text-gray-800">₹<?php echo number_format($booking['total_price']); ?></p>
            </div>
        </div>
        
        <div class="mt-12 text-center">
            <p class="text-gray-400 text-xs">This is a computer generated receipt. Thank you for choosing TravelPro.</p>
            <div class="mt-4 flex justify-center space-x-6 text-gray-300">
                <i class="fas fa-globe"></i>
                <i class="fas fa-envelope"></i>
                <i class="fas fa-phone"></i>
            </div>
        </div>
    </div>
</body>
</html>