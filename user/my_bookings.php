<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Bookings</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="../index.php" class="text-2xl font-bold text-purple-600">TravelPro</a>
            <div>
                <span class="mr-4">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="../index.php" class="text-gray-600 hover:text-purple-600 mr-4">Home</a>
                <a href="logout.php" class="text-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <div class="pt-24 max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold mb-8">My Bookings</h1>
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'booked') echo '<div class="bg-green-100 text-green-700 p-4 rounded mb-6">Payment Successful! Booking Confirmed.</div>'; ?>
        
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Package</th>
                        <th class="p-4">Dates</th>
                        <th class="p-4">Total Price</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Booked On</th>
                        <th class="p-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $uid = $_SESSION['user_id'];
                    $sql = "SELECT b.*, p.name as place_name, p.image_path FROM bookings b JOIN places p ON b.place_id = p.id WHERE b.user_id = $uid ORDER BY b.created_at DESC";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $statusClass = '';
                            if($row['status'] == 'Confirmed') $statusClass = 'text-green-600 bg-green-100';
                            elseif($row['status'] == 'Cancelled') $statusClass = 'text-red-600 bg-red-100';
                            else $statusClass = 'text-yellow-600 bg-yellow-100';
                            
                            echo '<tr class="border-b hover:bg-gray-50">';
                            echo '<td class="p-4 flex items-center gap-3">';
                            echo '<img src="../' . $row['image_path'] . '" class="w-16 h-12 object-cover rounded">';
                            echo '<span class="font-medium">' . htmlspecialchars($row['place_name']) . '</span>';
                            echo '</td>';
                            echo '<td class="p-4 text-gray-600">' . $row['from_date'] . ' to ' . $row['to_date'] . '</td>';
                            echo '<td class="p-4 text-purple-600 font-bold">₹' . number_format($row['total_price']) . '</td>';
                            echo '<td class="p-4"><span class="px-3 py-1 rounded-full text-sm font-semibold ' . $statusClass . '">' . $row['status'] . '</span></td>';
                            echo '<td class="p-4 text-gray-500 text-sm">' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                            echo '<td class="p-4">';
                            echo '<a href="ticket.php?id=' . $row['id'] . '" target="_blank" class="text-purple-600 hover:text-purple-800 font-semibold text-sm flex items-center"><i class="fas fa-file-invoice mr-1"></i> Receipt</a>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6" class="p-8 text-center text-gray-500">No bookings found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
