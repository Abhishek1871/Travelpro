<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM vehicles WHERE id=$id");
    header("Location: manage_vehicles.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO vehicles (name, type, capacity, price_per_day) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssid", $name, $type, $capacity, $price);
    $stmt->execute();
    header("Location: manage_vehicles.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Transport</title>
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
                <a href="manage_places.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-suitcase mr-2"></i> Packages</a>
                <a href="manage_bookings.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-calendar-check mr-2"></i> Bookings</a>
                <a href="manage_vehicles.php" class="block px-6 py-3 bg-gray-700"><i class="fas fa-bus mr-2"></i> Transport</a>
                <a href="logout.php" class="block px-6 py-3 text-red-400 hover:bg-gray-700 mt-4"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </nav>
        </aside>

        <div class="ml-64 flex-1 p-8">
            <h1 class="text-2xl font-bold mb-8">Manage Transport Vehicles</h1>
            
            <!-- Add Vehicle Form -->
            <div class="bg-white p-6 rounded-xl shadow mb-8">
                <h3 class="text-lg font-semibold mb-4">Add New Vehicle</h3>
                <form method="POST" class="grid grid-cols-5 gap-4">
                    <input type="text" name="name" placeholder="Vehicle Name" class="col-span-2 px-4 py-2 border rounded" required>
                    <select name="type" class="px-4 py-2 border rounded">
                        <option value="SUV">SUV</option>
                        <option value="Sedan">Sedan</option>
                        <option value="Bus">Bus</option>
                        <option value="Minibus">Minibus</option>
                    </select>
                    <input type="number" name="capacity" placeholder="Seats" class="px-4 py-2 border rounded" required>
                    <input type="number" name="price" placeholder="Price/Day" class="px-4 py-2 border rounded" required>
                    <button type="submit" class="col-span-5 bg-purple-600 text-white py-2 rounded hover:bg-purple-700">Add Vehicle</button>
                </form>
            </div>

            <!-- Vehicles List -->
            <div class="bg-white rounded-xl shadow overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Vehicle Name</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Capacity</th>
                            <th class="p-4">Price/Day</th>
                            <th class="p-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = $conn->query("SELECT * FROM vehicles");
                        while($row = $result->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-4">#<?php echo $row['id']; ?></td>
                            <td class="p-4 font-medium"><?php echo htmlspecialchars($row['name']); ?></td>
                            <td class="p-4"><?php echo $row['type']; ?></td>
                            <td class="p-4"><?php echo $row['capacity']; ?> Seats</td>
                            <td class="p-4 text-green-600 font-bold">₹<?php echo number_format($row['price_per_day']); ?></td>
                            <td class="p-4">
                                <a href="?delete=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Delete this vehicle?')"><i class="fas fa-trash"></i></a>
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
