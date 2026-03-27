<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$place_id = isset($_POST['place_id']) ? $_POST['place_id'] : null;
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : null;
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : null;

if (!$place_id || !$from_date || !$to_date) {
    echo "Invalid Request";
    exit();
}

// Fetch place details
$sql = "SELECT * FROM places WHERE id = $place_id";
$result = $conn->query($sql);
$place = $result->fetch_assoc();

// Calculate duration and price
$start = strtotime($from_date);
$end = strtotime($to_date);
$days = ceil(abs($end - $start) / 86400) + 1;
$total_price = $days * $place['price'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Secure Payment</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-slate-50 min-h-screen">
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
                    <div class="flex items-center space-x-4">
                        <a href="profile.php" class="text-white hover:text-purple-400 transition text-sm font-bold flex items-center">
                            <i class="fas fa-user-circle mr-2 text-lg text-purple-600"></i>Profile
                        </a>
                        <a href="my_bookings.php" class="bg-purple-600/20 text-purple-400 border border-purple-500/30 px-6 py-2 rounded-xl text-xs font-bold hover:bg-purple-600 hover:text-white transition-all">Back to Bookings</a>
                    </div>

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
                
                <div class="w-full pt-8 border-t border-white/10 flex flex-col space-y-4">
                    <a href="profile.php" class="w-full py-4 border-2 border-white/10 text-white rounded-2xl font-bold text-center">My Profile</a>
                    <a href="my_bookings.php" class="w-full py-4 bg-purple-600 text-white rounded-2xl font-bold shadow-lg text-center">My Bookings</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex items-center justify-center w-full min-h-screen pt-24 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="bg-purple-600 p-6 text-white flex justify-between items-center">
            <h2 class="text-xl font-bold"><i class="fas fa-lock mr-2"></i>Secure Payment</h2>
            <div class="text-right">
                <p class="text-xs opacity-80">Total Amount</p>
                <p class="text-2xl font-bold">₹<?php echo number_format($total_price); ?></p>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-gray-50 p-4 rounded-lg mb-6 border border-gray-100">
                <h3 class="font-semibold text-gray-800 mb-2"><?php echo htmlspecialchars($place['name']); ?></h3>
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>From:</span>
                    <span><?php echo $from_date; ?></span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>To:</span>
                    <span><?php echo $to_date; ?></span>
                </div>
            </div>

            <script>
                function togglePayment(method) {
                    if(method === 'card') {
                        document.getElementById('cardForm').classList.remove('hidden');
                        document.getElementById('upiForm').classList.add('hidden');
                        document.getElementById('btnCard').classList.add('border-purple-600', 'text-purple-600');
                        document.getElementById('btnUpi').classList.remove('border-purple-600', 'text-purple-600');
                        // Toggle Required
                        document.getElementById('cardNumber').required = true;
                        document.getElementById('upiId').required = false;
                    } else {
                        document.getElementById('cardForm').classList.add('hidden');
                        document.getElementById('upiForm').classList.remove('hidden');
                        document.getElementById('btnUpi').classList.add('border-purple-600', 'text-purple-600');
                        document.getElementById('btnCard').classList.remove('border-purple-600', 'text-purple-600');
                        // Toggle Required
                        document.getElementById('cardNumber').required = false;
                        document.getElementById('upiId').required = true;
                    }
                }
            </script>

            <div class="flex mb-6 border-b">
                <button type="button" onclick="togglePayment('card')" id="btnCard" class="flex-1 pb-2 border-b-2 border-purple-600 text-purple-600 font-medium">Card</button>
                <button type="button" onclick="togglePayment('upi')" id="btnUpi" class="flex-1 pb-2 border-b-2 border-transparent text-gray-500 font-medium">UPI</button>
            </div>

            <form action="book.php" method="POST">
                <input type="hidden" name="place_id" value="<?php echo $place_id; ?>">
                <input type="hidden" name="from_date" value="<?php echo $from_date; ?>">
                <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
                <input type="hidden" name="total_price" value="<?php echo $total_price; ?>">
                
                <!-- Card Form -->
                <div id="cardForm">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2 text-sm">Card Number</label>
                        <div class="relative">
                            <input type="text" id="cardNumber" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500 pl-10" placeholder="0000 0000 0000 0000" required maxlength="19">
                            <i class="fas fa-credit-card absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-gray-700 mb-2 text-sm">Expiry Date</label>
                            <input type="text" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500" placeholder="MM/YY" maxlength="5">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2 text-sm">CVV</label>
                            <div class="relative">
                                <input type="password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500 pl-10" placeholder="123" maxlength="3">
                                <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- UPI Form -->
                <div id="upiForm" class="hidden mb-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2 text-sm">UPI ID</label>
                        <div class="relative">
                            <input type="text" id="upiId" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:border-purple-500 pl-10" placeholder="username@bank">
                            <i class="fas fa-mobile-alt absolute left-3 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex justify-center space-x-4 opacity-70 grayscale hover:grayscale-0 transition-all">
                        <i class="fab fa-google-pay text-4xl"></i>
                        <i class="fab fa-amazon-pay text-4xl"></i>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-bold shadow-lg">
                    <i class="fas fa-check-circle mr-2"></i>Pay & Confirm Booking
                </button>
            </form>
            
            <div class="flex justify-center mt-6 space-x-3 text-2xl text-gray-400">
                <i class="fab fa-cc-visa"></i>
                <i class="fab fa-cc-mastercard"></i>
                <i class="fab fa-cc-amex"></i>
                <i class="fab fa-cc-paypal"></i>
            </div>
            <div class="text-center mt-4">
                <a href="../index.php" class="text-sm text-gray-500 hover:text-gray-700">Cancel Payment</a>
            </div>
        </div>
    </div>
</body>
</html>
