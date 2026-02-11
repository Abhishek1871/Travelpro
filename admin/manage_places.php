<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM places WHERE id=$id");
    header("Location: manage_places.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Places</title>
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
                <a href="manage_places.php" class="block px-6 py-3 bg-gray-700"><i class="fas fa-suitcase mr-2"></i> Manage Places</a>
                <a href="manage_bookings.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-calendar-check mr-2"></i> Bookings</a>
                <a href="add_place.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-plus mr-2"></i> Add Place</a>
                <a href="logout.php" class="block px-6 py-3 text-red-400 hover:bg-gray-700 mt-4"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </nav>
        </aside>

        <div class="ml-64 flex-1 p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Manage Packages</h1>
                <a href="add_place.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Add New</a>
            </div>
            
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded mb-6">Package updated successfully!</div>
            <?php endif; ?>
            
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Name</th>
                            <th class="p-4">Category</th>
                            <th class="p-4">Price</th>
                            <th class="p-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM places");
                        while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4"><?php echo $row['id']; ?></td>
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="p-4"><?php echo htmlspecialchars($row['category']); ?></td>
                            <td class="p-4 text-green-600">₹<?php echo number_format($row['price']); ?></td>
                            <td class="p-4 flex gap-3">
                                <a href="edit_place.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i> Edit</a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Delete this package?')"><i class="fas fa-trash"></i> Delete</a>
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
