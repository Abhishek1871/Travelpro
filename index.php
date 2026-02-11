<?php
session_start();
include 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelPro - Tourism Package Management System</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg fixed w-full z-50 top-0">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-plane-departure text-2xl text-purple-600"></i>
                    <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-500 bg-clip-text text-transparent">TravelPro</span>
                </div>
                <div class="flex items-center space-x-6">
                    <a href="index.php" class="text-gray-700 hover:text-purple-600">Home</a>
                    <a href="#packages" class="text-gray-700 hover:text-purple-600">Packages</a>
                    <a href="about.php" class="text-gray-700 hover:text-purple-600">About</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <a href="user/profile.php" class="text-gray-700 hover:text-purple-600">Profile</a>
                        <a href="user/my_bookings.php" class="text-gray-700 hover:text-purple-600">Bookings</a>
                        <a href="user/logout.php" class="text-red-600 hover:text-red-700">Logout</a>
                    <?php else: ?>
                        <a href="user/login.php" class="text-purple-600 font-medium">Login</a>
                        <a href="user/register.php" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">Register</a>
                    <?php endif; ?>
                    <a href="admin/login.php" class="text-gray-500 text-sm"><i class="fas fa-lock"></i> Admin</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="pt-16">
        <section class="hero-section flex items-center justify-center text-white text-center">
            <div class="max-w-3xl px-4 fade-in">
                <h1 class="text-4xl md:text-6xl font-bold mb-4">Discover Your Dream Destination</h1>
                <p class="text-xl mb-8">Explore amazing places around the world with our exclusive tourism packages</p>
                <a href="#packages" class="inline-block px-8 py-3 bg-purple-600 text-white rounded-full hover:bg-purple-700 transition">Explore Packages</a>
            </div>
        </section>

        <section id="packages" class="py-16 bg-gray-100">
            <div class="max-w-7xl mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-4">Our Tourism Packages</h2>
                
                <!-- Search & Filters -->
                <div class="mb-10">
                    <form action="#packages" method="GET" class="flex flex-col gap-4 mb-6">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                            <!-- Search -->
                            <div class="relative w-full md:w-96">
                                <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search destinations..." class="w-full px-4 py-3 pl-10 rounded-full border focus:outline-none focus:border-purple-500 shadow-sm">
                                <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                            </div>
                            
                            <!-- Categories -->
                            <div class="flex flex-wrap gap-2 justify-center">
                                <?php
                                $cats = ['All', 'Adventure', 'Family', 'Friends', 'Solo'];
                                $currentCat = $_GET['category'] ?? 'All';
                                foreach($cats as $cat) {
                                    $activeClass = $currentCat == $cat ? 'bg-purple-600 text-white' : 'bg-white text-gray-600 hover:bg-purple-50';
                                    echo "<a href='?category=$cat&price=" . ($_GET['price'] ?? 'all') . "#packages' class='px-4 py-2 rounded-full shadow-sm transition $activeClass'>$cat</a>";
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="flex justify-center">
                            <div class="flex items-center space-x-2 bg-white p-2 rounded-full shadow-sm border">
                                <span class="text-gray-500 pl-3"><i class="fas fa-filter mr-1"></i>Filter Price:</span>
                                <select name="price" onchange="this.form.submit()" class="bg-transparent text-gray-700 focus:outline-none cursor-pointer">
                                    <option value="all" <?php echo (!isset($_GET['price']) || $_GET['price'] == 'all') ? 'selected' : ''; ?>>Any Price</option>
                                    <option value="low" <?php echo (isset($_GET['price']) && $_GET['price'] == 'low') ? 'selected' : ''; ?>>Low (< ₹30,000)</option>
                                    <option value="medium" <?php echo (isset($_GET['price']) && $_GET['price'] == 'medium') ? 'selected' : ''; ?>>Medium (₹30,000 - ₹80,000)</option>
                                    <option value="high" <?php echo (isset($_GET['price']) && $_GET['price'] == 'high') ? 'selected' : ''; ?>>High (> ₹80,000)</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php
                    $search = $_GET['search'] ?? '';
                    $category = $_GET['category'] ?? 'All';
                    $priceFilter = $_GET['price'] ?? 'all';
                    
                    $sql = "SELECT * FROM places WHERE 1=1";
                    if ($search) {
                        $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
                    }
                    if ($category != 'All') {
                        $sql .= " AND category = '$category'";
                    }
                    if ($priceFilter == 'low') {
                        $sql .= " AND price < 30000";
                    } elseif ($priceFilter == 'medium') {
                        $sql .= " AND price BETWEEN 30000 AND 80000";
                    } elseif ($priceFilter == 'high') {
                        $sql .= " AND price > 80000";
                    }

                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $discountBadge = $row['discount_percent'] > 0 ? "<div class='absolute top-4 right-0 bg-gradient-to-r from-pink-500 to-yellow-400 text-white px-3 py-1 rounded-l-full text-xs font-bold shadow-md'>{$row['discount_percent']}% OFF</div>" : "";
                            
                            $priceDisplay = "";
                            if ($row['discount_percent'] > 0) {
                                $discountedPrice = round($row['price'] * (1 - $row['discount_percent']/100));
                                $priceDisplay = "<div class='flex flex-col items-end'><span class='text-xs text-gray-400 line-through'>₹" . number_format($row["price"]) . "</span><span class='bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-semibold'>₹" . number_format($discountedPrice) . "</span></div>";
                            } else {
                                $priceDisplay = "<span class='bg-purple-100 text-purple-600 px-3 py-1 rounded-full text-sm font-semibold'>₹" . number_format($row["price"]) . "</span>";
                            }

                            echo '<div class="bg-white rounded-xl overflow-hidden shadow-lg card-hover transition-all duration-300 relative">';
                            echo $discountBadge;
                            echo '<div class="relative">';
                            echo '<img src="' . htmlspecialchars($row["image_path"]) . '" class="package-image" alt="' . htmlspecialchars($row["name"]) . '">';
                            echo '<span class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm text-purple-700 px-3 py-1 rounded-full text-xs font-bold shadow-sm">' . htmlspecialchars($row["category"]) . '</span>';
                            echo '</div>';
                            echo '<div class="p-6">';
                            echo '<div class="flex justify-between items-start mb-2">';
                            echo '<h3 class="text-xl font-semibold">' . htmlspecialchars($row["name"]) . '</h3>';
                            echo $priceDisplay;
                            echo '</div>';
                            echo '<p class="text-gray-600 mb-4 line-clamp-3">' . htmlspecialchars($row["description"]) . '</p>';
                            echo '</div></div>';
                        }
                    } else {
                        echo '<p class="text-center col-span-3 text-gray-500 py-8">No packages found matching your criteria.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
