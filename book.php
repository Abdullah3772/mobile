<?php
// book.php
include 'config/db.php';

$id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $id AND is_available = 1";
$res = $conn->query($sql);
$product = $res->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit();
}

$success = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $delivery = mysqli_real_escape_string($conn, $_POST['delivery_method']);
    
    $insert = "INSERT INTO bookings (product_id, customer_phone, delivery_method) VALUES ($id, '$phone', '$delivery')";
    if ($conn->query($insert)) {
        $success = true;
        
        // --- WHATSAPP REDIRECTION SCRIPT CREATION ---
        $message_text = "Hello! I want to order a product:\n\n"
                      . "📦 Item: " . $product['name'] . "\n"
                      . "💵 Price: LKR " . number_format($product['price'], 2) . "\n"
                      . "📱 Contact Phone: " . $phone . "\n"
                      . "📍 Mode: " . ($delivery == 'COD' ? 'Cash on Delivery' : 'In-Shop Pickup & Negotiate');
        
        $whatsapp_url = "https://api.whatsapp.com/send?phone=" . preg_replace('/[^0-9]/', '', $shop_phone) . "&text=" . urlencode($message_text);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col justify-center p-4">

    <div class="max-w-md w-full mx-auto bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
        <?php if($success): ?>
        <div class="text-center py-6">
            <div
                class="w-14 h-14 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-2xl mb-4">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-900">Order Reserved Internally!</h2>
            <p class="text-xs text-gray-500 mt-2 px-4">Now click the button below to instantly notify the admin team via
                WhatsApp to secure your slot.</p>
            <a href="<?php echo $whatsapp_url; ?>" target="_blank"
                class="mt-6 inline-flex items-center justify-center gap-2 w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-md text-sm transition">
                <i class="fa-brands fa-whatsapp text-lg"></i> Send Order via WhatsApp
            </a>
            <a href="index.php" class="block text-xs font-semibold text-gray-400 mt-4 underline">Back to Shop Home</a>
        </div>
        <?php else: ?>
        <a href="index.php" class="text-xs font-semibold text-gray-400 mb-4 inline-block"><i
                class="fa-solid fa-arrow-left"></i> Cancel & Return</a>

        <div class="flex gap-4 items-center bg-gray-50 p-3 rounded-2xl mb-6 border border-gray-100">
            <img src="<?php echo $product['image_url']; ?>" class="w-16 h-16 object-contain rounded-lg bg-white p-1">
            <div>
                <h3 class="text-xs font-bold text-gray-800"><?php echo $product['name']; ?></h3>
                <p class="text-sm font-black text-blue-600 mt-1">LKR <?php echo number_format($product['price'], 2); ?>
                </p>
            </div>
        </div>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Your Phone
                    Number</label>
                <input type="tel" name="customer_phone" required placeholder="e.g. 0771234567"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-medium text-sm focus:outline-none focus:border-blue-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Fulfillment
                    Method</label>
                <div class="grid grid-cols-2 gap-3">
                    <label
                        class="border-2 border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-blue-600 transition [&:has(input:checked)]:border-blue-600 [&:has(input:checked)]:bg-blue-50/40">
                        <input type="radio" name="delivery_method" value="COD" checked class="sr-only">
                        <i class="fa-solid fa-truck text-lg text-gray-600"></i>
                        <span class="text-xs font-bold">Home Delivery</span>
                    </label>
                    <label
                        class="border-2 border-gray-200 rounded-xl p-3 flex flex-col items-center justify-center gap-1 cursor-pointer hover:border-blue-600 transition [&:has(input:checked)]:border-blue-600 [&:has(input:checked)]:bg-blue-50/40">
                        <input type="radio" name="delivery_method" value="Shop_Pickup" class="sr-only">
                        <i class="fa-solid fa-shop text-lg text-gray-600"></i>
                        <span class="text-xs font-bold">In-Shop Pickup</span>
                    </label>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-3.5 rounded-xl shadow-md transition pt-4">
                Confirm Order Placement
            </button>
        </form>
        <?php endif; ?>
    </div>

</body>

</html>