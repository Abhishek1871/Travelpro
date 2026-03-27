<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: manage_places.php");
    exit();
}

// Fetch existing place
$sql = "SELECT * FROM places WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$place = $result->fetch_assoc();

if (!$place) {
    header("Location: manage_places.php");
    exit();
}

// Decode itinerary JSON
$itinerary = json_decode($place['itinerary'] ?? '[]', true) ?: [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $discount = $_POST['discount'] ?? 0;
    $desc = $_POST['description'];
    $location = $_POST['location'] ?? $name;
    
    // Highlights
    $duration = $_POST['duration'] ?? 'Flexible';
    $group_size = $_POST['group_size'] ?? '1-50 People';
    $languages = $_POST['languages'] ?? 'English, Hindi';
    
    // Inclusions
    $inc_hotel = isset($_POST['inc_hotel']) ? 1 : 0;
    $inc_meals = isset($_POST['inc_meals']) ? 1 : 0;
    $inc_tours = isset($_POST['inc_tours']) ? 1 : 0;
    $inc_transfers = isset($_POST['inc_transfers']) ? 1 : 0;
    $inc_insurance = isset($_POST['inc_insurance']) ? 1 : 0;
    $inc_support = isset($_POST['inc_support']) ? 1 : 0;
    $inc_custom = $_POST['inc_custom'] ?? '';
    
    // Exclusions
    $exc_flights = isset($_POST['exc_flights']) ? 1 : 0;
    $exc_visa = isset($_POST['exc_visa']) ? 1 : 0;
    $exc_personal = isset($_POST['exc_personal']) ? 1 : 0;
    $exc_tips = isset($_POST['exc_tips']) ? 1 : 0;
    $exc_custom = $_POST['exc_custom'] ?? '';
    
    // Itinerary (as JSON)
    $itinerary_titles = $_POST['itin_title'] ?? [];
    $itinerary_descs = $_POST['itin_desc'] ?? [];
    $itinerary_data = [];
    for ($i = 0; $i < count($itinerary_titles); $i++) {
        if (!empty($itinerary_titles[$i]) || !empty($itinerary_descs[$i])) {
            $itinerary_data[] = [
                'title' => $itinerary_titles[$i],
                'description' => $itinerary_descs[$i]
            ];
        }
    }
    $itinerary_json = json_encode($itinerary_data);
    
    // Image handling
    $image_path = $place['image_path']; // Keep existing by default
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
        $image_path = "assets/images/" . basename($_FILES["image"]["name"]);
    } elseif (!empty($_POST['image_url'])) {
        $image_path = $_POST['image_url'];
    }

    $sql = "UPDATE places SET 
            name=?, category=?, image_path=?, price=?, discount_percent=?, description=?, location=?,
            duration=?, group_size=?, languages=?,
            inc_hotel=?, inc_meals=?, inc_tours=?, inc_transfers=?, inc_insurance=?, inc_support=?, inc_custom=?,
            exc_flights=?, exc_visa=?, exc_personal=?, exc_tips=?, exc_custom=?, itinerary=?
            WHERE id=?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssdssssssiiiiiisiiiiisi", 
        $name, $category, $image_path, $price, $discount, $desc, $location,
        $duration, $group_size, $languages,
        $inc_hotel, $inc_meals, $inc_tours, $inc_transfers, $inc_insurance, $inc_support, $inc_custom,
        $exc_flights, $exc_visa, $exc_personal, $exc_tips, $exc_custom, $itinerary_json, $id
    );
    
    if ($stmt->execute()) {
        header("Location: manage_places.php?msg=updated");
        exit();
    } else {
        $error = "Error updating package: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Package - Admin</title>
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
        <!-- Sidebar -->
        
        
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
                            <a href="manage_places.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all bg-purple-600 text-white shadow-lg shadow-purple-900/20">
                                <i class="fas fa-map-location-dot text-lg"></i>
                                <span class="font-bold text-sm">Packages</span>
                            </a>
                            <a href="manage_booking.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
                                <i class="fas fa-calendar-check text-lg"></i>
                                <span class="font-bold text-sm">Bookings</span>
                            </a>
                            <a href="manage_vehicles.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all text-gray-400 hover:text-white hover:bg-white/5">
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
            <h1 class="text-2xl font-bold mb-6">Edit Package: <?php echo htmlspecialchars($place['name']); ?></h1>
            
            <?php if(isset($error)) echo "<div class='bg-red-100 text-red-700 p-4 rounded mb-6'>$error</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data" class="max-w-4xl">
                <!-- Basic Info -->
                <div class="bg-white p-6 rounded-xl shadow mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Basic Information</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Place Name *</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($place['name']); ?>" class="w-full px-4 py-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Category *</label>
                            <select name="category" class="w-full px-4 py-3 border rounded-lg">
                                <option value="Adventure" <?php echo $place['category'] == 'Adventure' ? 'selected' : ''; ?>>Adventure</option>
                                <option value="Family" <?php echo $place['category'] == 'Family' ? 'selected' : ''; ?>>Family</option>
                                <option value="Friends" <?php echo $place['category'] == 'Friends' ? 'selected' : ''; ?>>Friends</option>
                                <option value="Solo" <?php echo $place['category'] == 'Solo' ? 'selected' : ''; ?>>Solo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">New Image Upload</label>
                            <input type="file" name="image" class="w-full px-4 py-3 border rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Current: <?php echo htmlspecialchars($place['image_path']); ?></p>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">OR Image URL</label>
                            <input type="url" name="image_url" value="<?php echo htmlspecialchars($place['image_path']); ?>" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Price (₹) *</label>
                            <input type="number" name="price" value="<?php echo $place['price']; ?>" class="w-full px-4 py-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Discount (%)</label>
                            <input type="number" name="discount" value="<?php echo $place['discount_percent']; ?>" class="w-full px-4 py-3 border rounded-lg" min="0" max="100">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" value="<?php echo htmlspecialchars($place['location'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-lg" required><?php echo htmlspecialchars($place['description']); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Highlights -->
                <div class="bg-purple-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-star mr-2 text-purple-500"></i>Package Highlights</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Duration</label>
                            <input type="text" name="duration" value="<?php echo htmlspecialchars($place['duration'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Group Size</label>
                            <input type="text" name="group_size" value="<?php echo htmlspecialchars($place['group_size'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Languages</label>
                            <input type="text" name="languages" value="<?php echo htmlspecialchars($place['languages'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Inclusions -->
                <div class="bg-green-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-check-circle mr-2 text-green-500"></i>What's Included</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_hotel" class="w-5 h-5" <?php echo $place['inc_hotel'] ? 'checked' : ''; ?>> Hotel Accommodation
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_meals" class="w-5 h-5" <?php echo $place['inc_meals'] ? 'checked' : ''; ?>> Breakfast & Dinner
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_tours" class="w-5 h-5" <?php echo $place['inc_tours'] ? 'checked' : ''; ?>> Sightseeing Tours
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_transfers" class="w-5 h-5" <?php echo $place['inc_transfers'] ? 'checked' : ''; ?>> Airport Transfers
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_insurance" class="w-5 h-5" <?php echo $place['inc_insurance'] ? 'checked' : ''; ?>> Travel Insurance
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_support" class="w-5 h-5" <?php echo $place['inc_support'] ? 'checked' : ''; ?>> 24/7 Support
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-gray-700 mb-2">Additional Inclusions</label>
                        <input type="text" name="inc_custom" value="<?php echo htmlspecialchars($place['inc_custom'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                    </div>
                </div>

                <!-- Exclusions -->
                <div class="bg-red-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-times-circle mr-2 text-red-500"></i>What's Not Included</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_flights" class="w-5 h-5" <?php echo $place['exc_flights'] ? 'checked' : ''; ?>> International Flights
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_visa" class="w-5 h-5" <?php echo $place['exc_visa'] ? 'checked' : ''; ?>> Visa Fees
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_personal" class="w-5 h-5" <?php echo $place['exc_personal'] ? 'checked' : ''; ?>> Personal Expenses
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_tips" class="w-5 h-5" <?php echo $place['exc_tips'] ? 'checked' : ''; ?>> Tips & Gratuities
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-gray-700 mb-2">Additional Exclusions</label>
                        <input type="text" name="exc_custom" value="<?php echo htmlspecialchars($place['exc_custom'] ?? ''); ?>" class="w-full px-4 py-3 border rounded-lg">
                    </div>
                </div>

                <!-- Itinerary -->
                <div class="bg-blue-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-route mr-2 text-blue-500"></i>Sample Itinerary</h3>
                    <div id="itineraryContainer">
                        <?php if (count($itinerary) > 0): ?>
                            <?php foreach ($itinerary as $i => $day): ?>
                            <div class="itinerary-day mb-3 grid grid-cols-12 gap-3">
                                <input type="text" name="itin_title[]" value="<?php echo htmlspecialchars($day['title']); ?>" class="col-span-4 px-4 py-2 border rounded-lg">
                                <input type="text" name="itin_desc[]" value="<?php echo htmlspecialchars($day['description']); ?>" class="col-span-7 px-4 py-2 border rounded-lg">
                                <button type="button" onclick="this.parentElement.remove()" class="col-span-1 px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200"><i class="fas fa-trash"></i></button>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="itinerary-day mb-3 grid grid-cols-12 gap-3">
                                <input type="text" name="itin_title[]" class="col-span-4 px-4 py-2 border rounded-lg" placeholder="Day 1 - Arrival">
                                <input type="text" name="itin_desc[]" class="col-span-8 px-4 py-2 border rounded-lg" placeholder="Airport pickup, hotel check-in">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" onclick="addItineraryRow()" class="mt-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                        <i class="fas fa-plus mr-2"></i>Add Day
                    </button>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                        <i class="fas fa-save mr-2"></i>Update Package
                    </button>
                    <a href="manage_places.php" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addItineraryRow() {
            const container = document.getElementById('itineraryContainer');
            const dayNum = container.querySelectorAll('.itinerary-day').length + 1;
            const newDay = document.createElement('div');
            newDay.className = 'itinerary-day mb-3 grid grid-cols-12 gap-3';
            newDay.innerHTML = `
                <input type="text" name="itin_title[]" class="col-span-4 px-4 py-2 border rounded-lg" placeholder="Day ${dayNum}">
                <input type="text" name="itin_desc[]" class="col-span-7 px-4 py-2 border rounded-lg" placeholder="Description">
                <button type="button" onclick="this.parentElement.remove()" class="col-span-1 px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200"><i class="fas fa-trash"></i></button>
            `;
            container.appendChild(newDay);
        }
    </script>
</body>
</html>