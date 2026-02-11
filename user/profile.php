<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_sql = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($user_sql);
$user = $user_result->fetch_assoc();

$bookings_sql = "SELECT COUNT(*) as count FROM bookings WHERE user_id = $user_id";
$bookings_result = $conn->query($bookings_sql);
$bookings_count = $bookings_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-purple-600">TravelPro</a>
            <div>
                <a href="../index.php" class="text-gray-600 hover:text-purple-600 mr-4">Home</a>
                <a href="logout.php" class="text-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <div class="pt-24 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b bg-purple-50 rounded-t-2xl">
                <h1 class="text-2xl font-bold text-gray-800 text-center">My Profile</h1>
            </div>
            
            <div class="p-8">
                <div class="flex flex-col items-center mb-8">
                    <div class="w-24 h-24 bg-purple-100 rounded-full flex items-center justify-center mb-4 border-4 border-white shadow-lg">
                        <i class="fas fa-user text-4xl text-purple-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800"><?php echo htmlspecialchars($user['name']); ?></h3>
                    <p class="text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="mt-2 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">Active User</span>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-purple-600">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Member Since</p>
                            <p class="font-medium text-gray-800"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm mr-4 text-purple-600">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Bookings</p>
                            <p class="font-medium text-gray-800"><?php echo $bookings_count; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-8 pt-6 border-t">
                    <a href="my_bookings.php" class="block w-full text-center py-3 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 transition font-medium">
                        View Booking History
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
