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
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
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
