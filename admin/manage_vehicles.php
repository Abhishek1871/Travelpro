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
    <link rel="stylesheet" href="../asset/css/style.css">

    <style>
        .fa-spin-hover:hover { animation: fa-spin 2s infinite linear; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
    </style>

</head>
<body class="bg-slate-50">
    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex justify-between items-center fixed w-full z-40 top-0 border-b border-white/5 shadow-lg">
        <div class="flex items-center gap-2">
            <i class="fas fa-plane-departure text-purple-500"></i>
            <span class="font-bold tracking-tight uppercase text-sm">TravelPro Admin</span>
        </div>
        <button onclick="document.getElementById('admin-sidebar').classList.toggle('-translate-x-full')" class="focus:outline-none w-10 h-10 flex items-center justify-center bg-white/5 rounded-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div class="flex min-h-screen pt-16 md:pt-0">
        
        
        <aside id="admin-sidebar" class="w-64 bg-slate-900 text-white fixed h-full z-50 transform -translate-x-full md:translate-x-0 transition-all duration-300 border-r border-white/5">
            <div class="p-6 border-b border-white/5 flex justify-between items-center bg-slate-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center shadow-lg shadow-purple-900/40">
                        <i class="fas fa-plane-departure text-sm"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">TravelPro <span class="text-purple-500 text-xs align-top bg-purple-500/10 px-2 py-0.5 rounded-full ml-1">ADMIN</span></span>
                </div>
                <button onclick="document.getElementById('admin-sidebar').classList.add('-translate-x-full')" class="md:hidden text-gray-400 hover:text-white focus:outline-none">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="flex flex-col h-[calc(100%-80px)] justify-between">
                <nav class="p-4 space-y-8 overflow-y-auto custom-scrollbar">
                    <!-- Main Navigation -->
                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4 px-2">Main Menu</p>
                        <div class="space-y-1">
                            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-layer-group text-lg"></i>
                                <span class="font-bold text-sm">Dashboard</span>
                            </a>
                            <a href="manage_places.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-map-location-dot text-lg"></i>
                                <span class="font-bold text-sm">Packages</span>
                            </a>
                            <a href="manage_booking.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-calendar-check text-lg"></i>
                                <span class="font-bold text-sm">Bookings</span>
                            </a>
                            <a href="manage_vehicles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all bg-purple-600 text-white shadow-lg shadow-purple-900/20">
                                <i class="fas fa-car-side text-lg"></i>
                                <span class="font-bold text-sm">Vehicles</span>
                            </a>
                        </div>
                    </div>

                    <!-- Website Tools -->
                    <div>
                        <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-4 px-2">Website View</p>
                        <div class="space-y-1">
                            <a href="../index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-home text-lg"></i>
                                <span class="font-bold text-sm">Home Page</span>
                            </a>
                            <a href="../about.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-info-circle text-lg"></i>
                                <span class="font-bold text-sm">About Us</span>
                            </a>
                            <a href="../index.php#contact" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                                <i class="fas fa-envelope text-lg"></i>
                                <span class="font-bold text-sm">Contact Us</span>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- Admin Footer -->
                <div class="p-4 border-t border-white/5 bg-slate-950/30">
                    <div class="flex items-center justify-between mb-4 px-2">
                        <div class="flex items-center gap-2 text-gray-400">
                            <i class="fas fa-circle text-[8px] text-green-500 animate-pulse"></i>
                            <span class="text-[10px] font-bold uppercase tracking-widest leading-none">Admin Online</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="#" class="text-gray-500 hover:text-white transition-colors">
                                <i class="fas fa-bell text-sm"></i>
                            </a>
                            <a href="#" class="text-yellow-500 hover:text-yellow-400 transition-colors">
                                <i class="fas fa-cog fa-spin-hover text-sm"></i>
                            </a>
                        </div>
                    </div>
                    <a href="logout.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-400 hover:bg-red-500/10 transition-all font-bold text-sm">
                        <i class="fas fa-power-off"></i>
                        <span>Sign Out</span>
                    </a>
                </div>
            </div>
        </aside>



        <div class="md:ml-64 flex-1 p-4 md:p-8 w-full max-w-full overflow-hidden">
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
                <div class="overflow-x-auto w-full max-w-full">
                        <table class="w-full text-left min-w-[600px]">
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
    </div>
</body>
</html>
