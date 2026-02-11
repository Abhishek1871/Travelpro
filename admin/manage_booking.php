<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = $_GET['action'] == 'approve' ? 'Confirmed' : 'Cancelled';
    $conn->query("UPDATE bookings SET status='$status' WHERE id=$id");
    header("Location: manage_bookings.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Bookings</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white fixed h-full">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-xl font-bold">TravelPro Admin</h1>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="manage_places.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-suitcase mr-2"></i> Manage Places</a>
                <a href="manage_bookings.php" class="block px-6 py-3 bg-gray-700"><i class="fas fa-calendar-check mr-2"></i> Bookings</a>
                <a href="add_place.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-plus mr-2"></i> Add Place</a>
                <a href="logout.php" class="block px-6 py-3 text-red-400 hover:bg-gray-700 mt-4"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </nav>
        </aside>

        <div class="ml-64 flex-1 p-8">
            <h1 class="text-2xl font-bold mb-8">Manage Bookings</h1>
            
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">User</th>
                            <th class="p-4">Package</th>
                            <th class="p-4">Dates</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, u.name as user_name, p.name as place_name FROM bookings b 
                                JOIN users u ON b.user_id = u.id 
                                JOIN places p ON b.place_id = p.id 
                                ORDER BY b.created_at DESC";
                        $result = $conn->query($sql);
                        
                        while($row = $result->fetch_assoc()): 
                            $statusClass = '';
                            if($row['status'] == 'Confirmed') $statusClass = 'text-green-600 bg-green-100';
                            elseif($row['status'] == 'Cancelled') $statusClass = 'text-red-600 bg-red-100';
                            else $statusClass = 'text-yellow-600 bg-yellow-100';
                        ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">#<?php echo $row['id']; ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['user_name']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['place_name']); ?></td>
                            <td class="p-4 text-sm"><?php echo $row['from_date'] . '<br>to ' . $row['to_date']; ?></td>
                            <td class="p-4"><span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                            <td class="p-4 flex items-center">
                                <?php if($row['status'] == 'Pending'): ?>
                                <a href="?action=approve&id=<?php echo $row['id']; ?>" class="text-green-600 hover:text-green-800 mr-3" title="Approve"><i class="fas fa-check"></i></a>
                                <a href="?action=reject&id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-800 mr-3" title="Reject"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                                <a href="../user/ticket.php?id=<?php echo $row['id']; ?>" target="_blank" class="text-purple-600 hover:text-purple-800" title="View Report"><i class="fas fa-file-invoice"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>