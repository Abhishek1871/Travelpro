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
    <link rel="stylesheet" href="asset/css/style.css">
</head>
<body class="bg-gray-100">
    <nav id="navbar" class="bg-black/20 backdrop-blur-md shadow-2xl fixed w-full z-[100] top-0 border-b border-white/5 h-20">
        <div class="max-w-7xl mx-auto px-6 h-full flex justify-between items-center w-full">
            <!-- Branding -->
            <a href="index.html" class="flex items-center space-x-2 group">
                <i class="fas fa-plane-departure text-2xl text-purple-600 transform group-hover:-translate-y-1 transition-transform"></i>
                <span class="text-2xl font-black text-white tracking-tight">TravelPro</span>
            </a>

            <!-- Right Side Elements -->
            <div class="flex items-center space-x-10">
                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-10">
                    <a href="index.html" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Home</a>
                    <a href="about.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">About Us</a>
                    <a href="index.html#contact" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Contact Us</a>
                    <!-- Gear Icon -->
                    <a href="#" class="text-yellow-400 hover:text-yellow-300 transition-colors">
                        <i class="fas fa-cog text-lg"></i>
                    </a>
                </div>

                <!-- Icons & Buttons -->
                <div class="flex items-center space-x-6">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- User Section -->
                        <div class="flex items-center space-x-6">
                            <div class="hidden sm:flex flex-col items-end border-r border-white/10 pr-4 mr-2">
                                <span class="text-[10px] text-white/40 font-bold uppercase tracking-widest leading-none mb-1">Signed in as</span>
                                <span class="text-sm font-bold text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="user/profile.php" class="text-white hover:text-purple-400 transition text-sm font-bold flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-lg text-purple-600"></i>Profile
                                </a>
                                <a href="user/my_bookings.php" class="bg-purple-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-purple-700 hover:shadow-lg transition-all shadow-md shadow-purple-900/20">My Bookings</a>
                                <a href="user/logout.php" class="text-red-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-power-off text-lg"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Auth Actions -->
                        <div class="flex items-center space-x-4">
                            <a href="user/login.php" class="px-6 py-2 border-2 border-purple-800/30 text-purple-500 font-bold rounded-lg hover:border-purple-600 hover:bg-purple-600/10 transition-all text-sm backdrop-blur-sm bg-black/10">Login</a>
                            <a href="user/register.php" class="px-7 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 hover:shadow-lg transition-all text-sm">Register</a>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Link -->
                    <a href="admin/login.php" class="flex items-center space-x-2 text-white/40 hover:text-white/60 transition-colors group">
                        <i class="fas fa-lock text-sm"></i>
                        <span class="text-[13px] font-medium tracking-wide">Admin</span>
                    </a>

                    <!-- Mobile Burger -->
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="lg:hidden w-11 h-11 flex items-center justify-center rounded-xl bg-white/5 text-white border border-white/10 hover:bg-white/10 transition focus:outline-none">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden bg-black/95 backdrop-blur-xl border-t border-white/5 shadow-2xl absolute w-full top-full left-0 z-[100] p-8">
            <div class="flex flex-col items-center space-y-8">
                <a href="index.html" class="text-lg font-bold text-white tracking-widest uppercase">Home</a>
                <a href="about.php" class="text-lg font-bold text-white tracking-widest uppercase">About Us</a>
                <a href="index.html#contact" class="text-lg font-bold text-white tracking-widest uppercase">Contact Us</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4 text-center">
                        <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-2">
                            <p class="text-xs text-white/40 font-bold uppercase tracking-widest mb-1">Authenticated Account</p>
                            <p class="text-lg font-black text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        </div>
                        <a href="user/profile.php" class="w-full py-4 border-2 border-white/10 text-white rounded-2xl font-bold">My Profile</a>
                        <a href="user/my_bookings.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg">My Bookings</a>
                        <a href="user/logout.php" class="text-red-400 font-bold py-2">Sign Out</a>
                    </div>
                <?php else: ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4">
                        <a href="user/login.php" class="w-full py-4 border-2 border-purple-800/30 text-purple-600 rounded-2xl font-bold text-center">Log In</a>
                        <a href="user/register.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold transition shadow-lg text-center">Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div>
        <section class="hero-section flex items-center justify-center text-white text-center min-h-screen py-32">
            <div class="max-w-4xl px-4 fade-in">
                <h1 class="text-5xl md:text-8xl font-black mb-6 tracking-tight">Discover Your Dream <br>Destination</h1>
                <p class="text-lg md:text-2xl mb-12 text-white/90 max-w-2xl mx-auto leading-relaxed">Explore amazing places around the world with our exclusive tourism packages</p>
                <div class="flex justify-center">
                    <a href="#packages" class="inline-flex items-center px-10 py-5 bg-purple-600 text-white rounded-full font-bold hover:bg-purple-700 transition transform hover:scale-105 shadow-xl shadow-purple-900/20 text-lg">
                        <i class="fas fa-compass mr-3"></i>Explore Packages
                    </a>
                </div>
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