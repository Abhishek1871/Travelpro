<?php
session_start();
include '../config/db.php';

// Handle Add Review
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_review'])) {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login to submit a review']);
        exit();
    }
    
    $user_id = $_SESSION['user_id'];
    $place_id = $_POST['place_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    
    // Check if user already reviewed this place
    $check = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND place_id = ?");
    $check->bind_param("ii", $user_id, $place_id);
    $check->execute();
    
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You have already reviewed this package']);
        exit();
    }
    
    $stmt = $conn->prepare("INSERT INTO reviews (place_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $place_id, $user_id, $rating, $comment);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error submitting review']);
    }
    exit();
}

// Get Reviews for a Place
if (isset($_GET['place_id'])) {
    $place_id = $_GET['place_id'];
    
    $sql = "SELECT r.*, u.name as user_name FROM reviews r 
            JOIN users u ON r.user_id = u.id 
            WHERE r.place_id = ? 
            ORDER BY r.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $place_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
    
    // Calculate average
    $avg_sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE place_id = ?";
    $avg_stmt = $conn->prepare($avg_sql);
    $avg_stmt->bind_param("i", $place_id);
    $avg_stmt->execute();
    $avg_result = $avg_stmt->get_result()->fetch_assoc();
    
    echo json_encode([
        'reviews' => $reviews,
        'average' => round($avg_result['avg_rating'], 1) ?: 0,
        'total' => $avg_result['total']
    ]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Package Reviews</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-50">
    <nav id="navbar" class="bg-gray-900/95 backdrop-blur-md shadow-2xl fixed w-full z-[100] top-0 border-b border-white/5 h-20">
        <div class="max-w-7xl mx-auto px-6 h-full flex justify-between items-center w-full">
            <!-- Branding -->
            <a href="../index.php" class="flex items-center space-x-2 group">
                <i class="fas fa-plane-departure text-2xl text-purple-600 transform group-hover:-translate-y-1 transition-transform"></i>
                <span class="text-2xl font-black text-white tracking-tight">TravelPro</span>
            </a>

            <!-- Right Side Elements -->
            <div class="flex items-center space-x-10">
                <!-- Navigation Links -->
                <div class="hidden lg:flex items-center space-x-10">
                    <a href="../index.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Home</a>
                    <a href="../about.php" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">About Us</a>
                    <a href="../index.php#contact" class="text-[13px] font-bold text-white/90 hover:text-white tracking-widest uppercase transition-colors">Contact Us</a>
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
                                <a href="profile.php" class="text-white hover:text-purple-400 transition text-sm font-bold flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-lg text-purple-600"></i>Profile
                                </a>
                                <a href="my_bookings.php" class="bg-purple-600 text-white px-6 py-2.5 rounded-xl text-xs font-bold hover:bg-purple-700 hover:shadow-lg transition-all shadow-md shadow-purple-900/20">My Bookings</a>
                                <a href="logout.php" class="text-red-400 hover:text-red-500 transition-colors">
                                    <i class="fas fa-power-off text-lg"></i>
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center space-x-4">
                            <a href="login.php" class="px-6 py-2 border-2 border-purple-800/30 text-purple-500 font-bold rounded-lg hover:border-purple-600 hover:bg-purple-600/10 transition-all text-sm backdrop-blur-sm bg-black/10">Login</a>
                            <a href="register.php" class="px-7 py-3 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 hover:shadow-lg transition-all text-sm">Register</a>
                        </div>
                    <?php endif; ?>

                    <!-- Admin Link -->
                    <a href="../admin/login.php" class="flex items-center space-x-2 text-white/40 hover:text-white/60 transition-colors group">
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
        <div id="mobile-menu" class="hidden lg:hidden bg-gray-900/95 backdrop-blur-xl border-t border-white/5 shadow-2xl absolute w-full top-full left-0 z-[100] p-8">
            <div class="flex flex-col items-center space-y-8">
                <a href="../index.php" class="text-lg font-bold text-white tracking-widest uppercase">Home</a>
                <a href="../about.php" class="text-lg font-bold text-white tracking-widest uppercase">About Us</a>
                <a href="../index.php#contact" class="text-lg font-bold text-white tracking-widest uppercase">Contact Us</a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4 text-center">
                        <div class="bg-white/5 p-6 rounded-2xl border border-white/10 mb-2">
                            <p class="text-xs text-white/40 font-bold uppercase tracking-widest mb-1">Authenticated Account</p>
                            <p class="text-lg font-black text-white"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                        </div>
                        <a href="profile.php" class="w-full py-4 border-2 border-white/10 text-white rounded-2xl font-bold">My Profile</a>
                        <a href="my_bookings.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg">My Bookings</a>
                        <a href="logout.php" class="text-red-400 font-bold py-2">Sign Out</a>
                    </div>
                <?php else: ?>
                    <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4">
                        <a href="login.php" class="w-full py-4 border-2 border-purple-800/30 text-purple-600 rounded-2xl font-bold text-center">Log In</a>
                        <a href="register.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold transition shadow-lg text-center">Create Account</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto p-8 pt-32">
        <h1 class="text-2xl font-bold mb-6">Package Reviews</h1>
        
        <?php
        $place_id = $_GET['id'] ?? 0;
        $place = $conn->query("SELECT * FROM places WHERE id = $place_id")->fetch_assoc();
        
        if ($place):
        ?>
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <div class="flex items-center gap-4 mb-4">
                <img src="../<?php echo $place['image_path']; ?>" class="w-24 h-24 object-cover rounded-lg">
                <div>
                    <h2 class="text-xl font-bold"><?php echo htmlspecialchars($place['name']); ?></h2>
                    <p class="text-gray-500">₹<?php echo number_format($place['price']); ?> per person</p>
                </div>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
            <!-- Add Review Form -->
            <form method="POST" class="bg-purple-50 p-4 rounded-lg mb-6">
                <input type="hidden" name="add_review" value="1">
                <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                <h3 class="font-semibold mb-3">Write a Review</h3>
                <div class="mb-3">
                    <label class="block text-sm text-gray-600 mb-1">Your Rating</label>
                    <select name="rating" class="px-4 py-2 border rounded-lg" required>
                        <option value="5">★★★★★ (5 - Excellent)</option>
                        <option value="4">★★★★☆ (4 - Good)</option>
                        <option value="3">★★★☆☆ (3 - Average)</option>
                        <option value="2">★★☆☆☆ (2 - Poor)</option>
                        <option value="1">★☆☆☆☆ (1 - Terrible)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="block text-sm text-gray-600 mb-1">Your Comment</label>
                    <textarea name="comment" rows="3" class="w-full px-4 py-2 border rounded-lg" placeholder="Share your experience..." required></textarea>
                </div>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Submit Review</button>
            </form>
            <?php else: ?>
            <div class="bg-gray-100 p-4 rounded-lg mb-6 text-center">
                <p><a href="login.php" class="text-purple-600 font-semibold">Login</a> to write a review.</p>
            </div>
            <?php endif; ?>
            
            <!-- Reviews List -->
            <h3 class="font-semibold mb-4">All Reviews</h3>
            <?php
            $reviews = $conn->query("SELECT r.*, u.name as user_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.place_id = $place_id ORDER BY r.created_at DESC");
            
            if ($reviews->num_rows > 0):
                while($review = $reviews->fetch_assoc()):
            ?>
            <div class="border-b py-4">
                <div class="flex justify-between items-start mb-2">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold"><?php echo htmlspecialchars($review['user_name']); ?></p>
                            <p class="text-xs text-gray-400"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="bg-yellow-50 px-2 py-1 rounded">
                        <span class="text-yellow-500">★</span>
                        <span class="font-bold"><?php echo $review['rating']; ?></span>
                    </div>
                </div>
                <p class="text-gray-600"><?php echo htmlspecialchars($review['comment']); ?></p>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <p class="text-gray-400 text-center py-4">No reviews yet. Be the first!</p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <p class="text-red-500">Package not found.</p>
        <?php endif; ?>
        
        <a href="../index.php" class="text-purple-600 hover:underline"><i class="fas fa-arrow-left mr-1"></i> Back to Packages</a>
    </div>
</body>
</html>
