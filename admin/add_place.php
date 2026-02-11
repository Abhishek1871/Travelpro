<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

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
    $itinerary = [];
    for ($i = 0; $i < count($itinerary_titles); $i++) {
        if (!empty($itinerary_titles[$i]) || !empty($itinerary_descs[$i])) {
            $itinerary[] = [
                'title' => $itinerary_titles[$i],
                'description' => $itinerary_descs[$i]
            ];
        }
    }
    $itinerary_json = json_encode($itinerary);
    
    // Image upload handling (local project storage)
    function saveUploadedImage($fileKey) {
        if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== 0) return null;

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $mime = mime_content_type($_FILES[$fileKey]['tmp_name']);
        if (!isset($allowed[$mime])) return null;

        $target_dir = "../assets/images/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = $allowed[$mime];
        $safeName = preg_replace('/[^a-zA-Z0-9-_]/', '_', pathinfo($_FILES[$fileKey]['name'], PATHINFO_FILENAME));
        $filename = $safeName . '_' . time() . '_' . rand(1000,9999) . '.' . $ext;
        $target_file = $target_dir . $filename;

        if (!move_uploaded_file($_FILES[$fileKey]['tmp_name'], $target_file)) return null;

        return "assets/images/" . $filename;
    }

    $image_path = saveUploadedImage('image1');
    $image2_path = saveUploadedImage('image2');
    $image3_path = saveUploadedImage('image3');

    if (!$image_path) {
        $error = "Image 1 upload is required (JPG/PNG/WEBP).";
    } else {
        $sql = "INSERT INTO places (name, category, image_path, image2_path, image3_path, price, discount_percent, description, location, 
                duration, group_size, languages, 
                inc_hotel, inc_meals, inc_tours, inc_transfers, inc_insurance, inc_support, inc_custom,
                exc_flights, exc_visa, exc_personal, exc_tips, exc_custom, itinerary) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdisssssiiiiiisiiiiis", 
            $name, $category, $image_path, $image2_path, $image3_path, $price, $discount, $desc, $location,
            $duration, $group_size, $languages,
            $inc_hotel, $inc_meals, $inc_tours, $inc_transfers, $inc_insurance, $inc_support, $inc_custom,
            $exc_flights, $exc_visa, $exc_personal, $exc_tips, $exc_custom, $itinerary_json
        );
    }
    
    if (!isset($error)) {
        if ($stmt->execute()) {
            $success = "Package added successfully!";
        } else {
            $error = "Error adding package: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Place - Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-gray-100">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white fixed h-full">
            <div class="p-6 border-b border-gray-700">
                <h1 class="text-xl font-bold">TravelPro Admin</h1>
            </div>
            <nav class="mt-6">
                <a href="dashboard.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
                <a href="manage_places.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-suitcase mr-2"></i> Manage Places</a>
                <a href="add_place.php" class="block px-6 py-3 bg-gray-700"><i class="fas fa-plus mr-2"></i> Add Place</a>
                <a href="manage_bookings.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-calendar-check mr-2"></i> Bookings</a>
                <a href="manage_vehicles.php" class="block px-6 py-3 hover:bg-gray-700"><i class="fas fa-bus mr-2"></i> Transport</a>
                <a href="logout.php" class="block px-6 py-3 text-red-400 hover:bg-gray-700 mt-4"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
            </nav>
        </aside>

        <div class="ml-64 flex-1 p-8">
            <h1 class="text-2xl font-bold mb-6">Add New Package</h1>
            
            <?php if(isset($success)) echo "<div class='bg-green-100 text-green-700 p-4 rounded mb-6'>$success</div>"; ?>
            <?php if(isset($error)) echo "<div class='bg-red-100 text-red-700 p-4 rounded mb-6'>$error</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data" class="max-w-4xl">
                <!-- Basic Info -->
                <div class="bg-white p-6 rounded-xl shadow mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i>Basic Information</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Place Name *</label>
                            <input type="text" name="name" class="w-full px-4 py-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Category *</label>
                            <select name="category" class="w-full px-4 py-3 border rounded-lg">
                                <option value="Adventure">Adventure</option>
                                <option value="Family">Family</option>
                                <option value="Friends">Friends</option>
                                <option value="Solo">Solo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Image 1 Upload *</label>
                            <input type="file" name="image1" accept="image/png,image/jpeg,image/webp" class="w-full px-4 py-3 border rounded-lg" required>
                            <p class="text-xs text-gray-500 mt-1">Used as the main package image & first slider image.</p>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Image 2 Upload (optional)</label>
                            <input type="file" name="image2" accept="image/png,image/jpeg,image/webp" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Image 3 Upload (optional)</label>
                            <input type="file" name="image3" accept="image/png,image/jpeg,image/webp" class="w-full px-4 py-3 border rounded-lg">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Price (₹) *</label>
                            <input type="number" name="price" class="w-full px-4 py-3 border rounded-lg" required>
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Discount (%)</label>
                            <input type="number" name="discount" class="w-full px-4 py-3 border rounded-lg" value="0" min="0" max="100">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Location</label>
                            <input type="text" name="location" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., Bali, Indonesia">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-3 border rounded-lg" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Highlights -->
                <div class="bg-purple-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-star mr-2 text-purple-500"></i>Package Highlights</h3>
                    <div class="grid md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Duration</label>
                            <input type="text" name="duration" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., 5 Days / 4 Nights">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Group Size</label>
                            <input type="text" name="group_size" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., 2-20 People">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Languages</label>
                            <input type="text" name="languages" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., English, Hindi">
                        </div>
                    </div>
                </div>

                <!-- Inclusions -->
                <div class="bg-green-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-check-circle mr-2 text-green-500"></i>What's Included</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_hotel" class="w-5 h-5" checked> Hotel Accommodation
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_meals" class="w-5 h-5" checked> Breakfast & Dinner
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_tours" class="w-5 h-5" checked> Sightseeing Tours
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_transfers" class="w-5 h-5" checked> Airport Transfers
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_insurance" class="w-5 h-5" checked> Travel Insurance
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="inc_support" class="w-5 h-5" checked> 24/7 Support
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-gray-700 mb-2">Additional Inclusions (comma-separated)</label>
                        <input type="text" name="inc_custom" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., Free WiFi, Spa Access">
                    </div>
                </div>

                <!-- Exclusions -->
                <div class="bg-red-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-times-circle mr-2 text-red-500"></i>What's Not Included</h3>
                    <div class="grid md:grid-cols-2 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_flights" class="w-5 h-5" checked> International Flights
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_visa" class="w-5 h-5" checked> Visa Fees
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_personal" class="w-5 h-5" checked> Personal Expenses
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="exc_tips" class="w-5 h-5" checked> Tips & Gratuities
                        </label>
                    </div>
                    <div class="mt-3">
                        <label class="block text-gray-700 mb-2">Additional Exclusions (comma-separated)</label>
                        <input type="text" name="exc_custom" class="w-full px-4 py-3 border rounded-lg" placeholder="e.g., Travel Vaccinations">
                    </div>
                </div>

                <!-- Itinerary -->
                <div class="bg-blue-50 p-6 rounded-xl mb-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-route mr-2 text-blue-500"></i>Sample Itinerary</h3>
                    <div id="itineraryContainer">
                        <div class="itinerary-day mb-3 grid grid-cols-12 gap-3">
                            <input type="text" name="itin_title[]" class="col-span-4 px-4 py-2 border rounded-lg" placeholder="Day 1 - Arrival">
                            <input type="text" name="itin_desc[]" class="col-span-8 px-4 py-2 border rounded-lg" placeholder="Airport pickup, hotel check-in">
                        </div>
                        <div class="itinerary-day mb-3 grid grid-cols-12 gap-3">
                            <input type="text" name="itin_title[]" class="col-span-4 px-4 py-2 border rounded-lg" placeholder="Day 2 - Exploration">
                            <input type="text" name="itin_desc[]" class="col-span-8 px-4 py-2 border rounded-lg" placeholder="Full day sightseeing">
                        </div>
                        <div class="itinerary-day mb-3 grid grid-cols-12 gap-3">
                            <input type="text" name="itin_title[]" class="col-span-4 px-4 py-2 border rounded-lg" placeholder="Day 3 - Departure">
                            <input type="text" name="itin_desc[]" class="col-span-8 px-4 py-2 border rounded-lg" placeholder="Check-out and transfer">
                        </div>
                    </div>
                    <button type="button" onclick="addItineraryRow()" class="mt-2 px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                        <i class="fas fa-plus mr-2"></i>Add Day
                    </button>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-semibold">
                        <i class="fas fa-save mr-2"></i>Save Package
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