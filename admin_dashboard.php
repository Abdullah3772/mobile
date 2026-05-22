<?php
// admin_dashboard.php
session_start();
if (!isset($_SESSION['admin_auth']) || $_SESSION['admin_auth'] !== true) {
    header("Location: admin_login.php");
    exit();
}
include 'config/db.php';

// Success / Error messages
$msg = ""; $err = "";

// 1. DYNAMIC SHOP SETTINGS PROCESSOR
if (isset($_POST['update_settings'])) {
    $ann_text = mysqli_real_escape_string($conn, $_POST['announcement_text']);
    $ann_status = isset($_POST['is_announcement_active']) ? 1 : 0;
    $wa_phone = mysqli_real_escape_string($conn, $_POST['whatsapp_target_phone']);

    $check_settings = $conn->query("SELECT id FROM shop_settings WHERE id = 1");
    if ($check_settings && $check_settings->num_rows > 0) {
        $conn->query("UPDATE shop_settings SET announcement_text='$ann_text', is_announcement_active=$ann_status, whatsapp_target_phone='$wa_phone' WHERE id=1");
    } else {
        $conn->query("INSERT INTO shop_settings (id, announcement_text, is_announcement_active, whatsapp_target_phone) VALUES (1, '$ann_text', $ann_status, '$wa_phone')");
    }
    header("Location: admin_dashboard.php?msg=Settings Updated Successfully");
    exit();
}

// Fetch current shop settings
$settings_res = $conn->query("SELECT * FROM shop_settings WHERE id = 1");
$shop_settings = ($settings_res && $settings_res->num_rows > 0) ? $settings_res->fetch_assoc() : [
    'announcement_text' => '🔥 Special Offer Active!',
    'is_announcement_active' => 1,
    'whatsapp_target_phone' => '+94771234567'
];

// 2. DELETE PRODUCT LOGIC
if (isset($_GET['delete_product'])) {
    $del_id = intval($_GET['delete_product']);
    $res = $conn->query("SELECT image_url, more_images FROM products WHERE id = $del_id");
    if ($res && $res->num_rows > 0) {
        $p_data = $res->fetch_assoc();
        if (!empty($p_data['image_url']) && file_exists($p_data['image_url'])) @unlink($p_data['image_url']);
        if (!empty($p_data['more_images'])) {
            $extra_imgs = explode(',', $p_data['more_images']);
            foreach ($extra_imgs as $img) {
                $img = trim($img);
                if (!empty($img) && file_exists($img)) @unlink($img);
            }
        }
        $conn->query("DELETE FROM products WHERE id = $del_id");
    }
    header("Location: admin_dashboard.php?msg=Product Removed");
    exit();
}

// 3. ADD & EDIT PRODUCT PROCESSOR
if (isset($_POST['save_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $price = floatval($_POST['price']);
    $badge = mysqli_real_escape_string($conn, $_POST['condition_badge']);

    $is_edit = isset($_POST['product_id']) && !empty($_POST['product_id']);
    $prod_id = $is_edit ? intval($_POST['product_id']) : 0;

    if (!is_dir('uploads')) mkdir('uploads', 0755, true);

    $final_img_url = '';
    $existing_extra_images = '';

    if ($is_edit) {
        $old_res = $conn->query("SELECT image_url, more_images FROM products WHERE id = $prod_id");
        $old_data = $old_res ? $old_res->fetch_assoc() : null;
        if ($old_data) {
            $final_img_url = $old_data['image_url'];
            $existing_extra_images = $old_data['more_images'];
        }
    }

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK && !empty($_FILES['image_file']['name'])) {
        $file_ext = strtolower(pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION));
        $allowed_main = ['jpg','jpeg','png','webp','gif'];
        if (!in_array($file_ext, $allowed_main)) {
            // if invalid, keep old image
            if ($is_edit && !empty($existing_extra_images)) {
                // nothing
            }
        } else {
            $new_main_name = 'prod_' . time() . '.' . $file_ext;
            $target_main = 'uploads/' . $new_main_name;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $target_main)) {
                if ($is_edit && !empty($final_img_url) && file_exists($final_img_url)) @unlink($final_img_url);
                $final_img_url = $target_main;
            }
        }
    } elseif (empty($final_img_url) && isset($_POST['image_url'])) {
        $final_img_url = mysqli_real_escape_string($conn, $_POST['image_url']);
    }

    // ===== Gallery Upload (MAX 5) =====
    $max_gallery = 5;

    // existing extra images (array)
    $existing_array = [];
    if ($is_edit && !empty($existing_extra_images)) {
        $tmp = explode(',', $existing_extra_images);
        foreach ($tmp as $t) {
            $t = trim($t);
            if ($t !== '') $existing_array[] = $t;
        }
        // keep only first 5 in edit (recommended)
        $existing_array = array_slice($existing_array, 0, $max_gallery);
    }

    $uploaded_extra_paths = [];
    if (isset($_FILES['more_photos'])) {
        $names = $_FILES['more_photos']['name'];
        $errors = $_FILES['more_photos']['error'];
        $tmps  = $_FILES['more_photos']['tmp_name'];

        $count_added = 0;

        // how many slots left?
        $slots_left = $max_gallery - count($existing_array);
        if ($slots_left < 0) $slots_left = 0;

        for ($key = 0; $key < count($names); $key++) {
            if ($count_added >= $slots_left) break;

            if (!isset($errors[$key]) || $errors[$key] !== UPLOAD_ERR_OK) continue;
            if (empty($names[$key])) continue;

            $ext = strtolower(pathinfo($names[$key], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','webp','gif'];
            if (!in_array($ext, $allowed)) continue;

            $path = 'uploads/extra_' . time() . '_' . $key . '.' . $ext;

            if (move_uploaded_file($tmps[$key], $path)) {
                $uploaded_extra_paths[] = $path;
                $count_added++;
            }
        }
    }

    // combine for final
    $final_more_images_arr = [];
    if ($is_edit) {
        $final_more_images_arr = array_merge($existing_array, $uploaded_extra_paths);
        $final_more_images_arr = array_slice($final_more_images_arr, 0, $max_gallery);
    } else {
        $final_more_images_arr = $uploaded_extra_paths;
        $final_more_images_arr = array_slice($final_more_images_arr, 0, $max_gallery);
    }

    $final_more_images = implode(',', $final_more_images_arr);
    $final_more_images = trim($final_more_images, ',');

    if ($is_edit) {
        $conn->query("UPDATE products 
            SET name='$name', category='$cat', price=$price, condition_badge='$badge', 
                image_url='$final_img_url', more_images='$final_more_images' 
            WHERE id=$prod_id");
    } else {
        $conn->query("INSERT INTO products 
            (name, category, price, condition_badge, image_url, more_images, is_available) 
            VALUES ('$name', '$cat', $price, '$badge', '$final_img_url', '$final_more_images', 1)");
    }
    header("Location: admin_dashboard.php?msg=Inventory Updated");
    exit();
}

// Toggle Stock
if (isset($_GET['toggle_stock'])) {
    $pid = intval($_GET['toggle_stock']);
    $new_status = intval($_GET['status']) === 1 ? 0 : 1;
    $conn->query("UPDATE products SET is_available = $new_status WHERE id = $pid");
    header("Location: admin_dashboard.php?filter=" . ($_GET['filter'] ?? 'all'));
    exit();
}

// Data Fetching
$current_filter = $_GET['filter'] ?? 'all';
$sql = ($current_filter !== 'all') ? "SELECT * FROM products WHERE category = '$current_filter' ORDER BY id DESC" : "SELECT * FROM products ORDER BY id DESC";
$products = $conn->query($sql);
$bookings = $conn->query("SELECT b.*, p.name as prod_name FROM bookings b JOIN products p ON b.product_id = p.id ORDER BY b.id DESC LIMIT 10");

// Stats
$total_prods = $conn->query("SELECT count(id) as total FROM products")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT count(id) as total FROM bookings")->fetch_assoc()['total'];
$out_of_stock = $conn->query("SELECT count(id) as total FROM products WHERE is_available = 0")->fetch_assoc()['total'];

$edit_prod = null;
if (isset($_GET['edit_product'])) {
    $edit_id = intval($_GET['edit_product']);
    $edit_prod = $conn->query("SELECT * FROM products WHERE id = $edit_id")->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Plus Jakarta Sans', 'sans-serif']
                }
            }
        }
    }
    </script>
    <style>
    body {
        background: #020617;
        background-image: radial-gradient(circle at 50% -20%, #1e293b, #020617);
        min-height: 100vh;
    }

    .glass-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
        height: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 10px;
    }
    </style>
</head>

<body class="text-slate-200 antialiased p-4 md:p-8">

    <div class="max-w-7xl mx-auto space-y-8">

        <!-- HEADER -->
        <header class="flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <h1
                    class="text-3xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white to-slate-400">
                    Control Center
                </h1>
                <p class="text-slate-500 text-sm font-medium mt-1">Welcome back, Administrator</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="index.php"
                    class="glass-card px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-800 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-shop text-blue-400"></i> View Store
                </a>
                <a href="logout.php"
                    class="bg-red-500/10 text-red-400 border border-red-500/20 px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-red-500/20 transition-all">
                    Logout
                </a>
            </div>
        </header>

        <!-- STATS SECTION -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Total Products</p>
                        <h2 class="text-2xl font-bold mt-1"><?php echo $total_prods; ?></h2>
                    </div>
                    <div class="w-10 h-10 bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500">
                        <i class="fa-solid fa-box"></i>
                    </div>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">New Bookings</p>
                        <h2 class="text-2xl font-bold mt-1"><?php echo $total_bookings; ?></h2>
                    </div>
                    <div
                        class="w-10 h-10 bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Hidden Items</p>
                        <h2 class="text-2xl font-bold mt-1"><?php echo $out_of_stock; ?></h2>
                    </div>
                    <div class="w-10 h-10 bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500">
                        <i class="fa-solid fa-eye-slash"></i>
                    </div>
                </div>
            </div>
            <div class="glass-card p-5 rounded-2xl">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-slate-500 text-xs font-bold uppercase tracking-wider">Status</p>
                        <h2 class="text-sm font-bold mt-2 text-emerald-400 flex items-center gap-2">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full animate-ping"></span> Live System
                        </h2>
                    </div>
                    <div class="w-10 h-10 bg-purple-500/10 rounded-lg flex items-center justify-center text-purple-500">
                        <i class="fa-solid fa-server"></i>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
        <div
            class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm flex items-center gap-3 animate-bounce">
            <i class="fa-solid fa-check-circle"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- LEFT COLUMN: FORMS -->
            <div class="lg:col-span-4 space-y-6">

                <!-- SHOP SETTINGS -->
                <section class="glass-card p-6 rounded-3xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                    <h3
                        class="text-sm font-bold uppercase tracking-widest text-emerald-400 mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-gears"></i> Global Config
                    </h3>
                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="update_settings" value="1">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-2">WhatsApp
                                Gateway</label>
                            <input type="text" name="whatsapp_target_phone"
                                value="<?php echo htmlspecialchars($shop_settings['whatsapp_target_phone']); ?>"
                                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"
                                placeholder="+94...">
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-[10px] font-bold text-slate-500 uppercase">Live Announcement</label>
                                <input type="checkbox" name="is_announcement_active"
                                    <?php echo $shop_settings['is_announcement_active'] ? 'checked' : ''; ?>
                                    class="w-4 h-4 accent-emerald-500">
                            </div>
                            <textarea name="announcement_text" rows="2"
                                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all"><?php echo htmlspecialchars($shop_settings['announcement_text']); ?></textarea>
                        </div>
                        <button
                            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-xs transition-all shadow-lg shadow-emerald-900/20">
                            Update Settings
                        </button>
                    </form>
                </section>

                <!-- PRODUCT FORM -->
                <section class="glass-card p-6 rounded-3xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-purple-500"></div>
                    <h3 class="text-sm font-bold uppercase tracking-widest text-blue-400 mb-6 flex items-center gap-2">
                        <i class="fa-solid <?php echo $edit_prod ? 'fa-edit' : 'fa-plus-circle'; ?>"></i>
                        <?php echo $edit_prod ? 'Edit Product' : 'Add New Product'; ?>
                    </h3>

                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <?php if($edit_prod): ?>
                        <input type="hidden" name="product_id" value="<?php echo $edit_prod['id']; ?>">
                        <?php endif; ?>
                        <input type="hidden" name="save_product" value="1">

                        <input type="text" name="name" required placeholder="Product Name"
                            value="<?php echo $edit_prod['name'] ?? ''; ?>"
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm outline-none focus:border-blue-500 transition-all">

                        <select name="category"
                            class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm outline-none focus:border-blue-500">
                            <?php 
                            $cats = ['new'=>'New Phones', 'used'=>'Used Phones', 'covers'=>'Covers', 'tempered'=>'Tempered', 'accessories'=>'Accessories'];
                            foreach($cats as $k => $v) {
                                $sel = ($edit_prod['category'] ?? '') == $k ? 'selected' : '';
                                echo "<option value='$k' $sel>$v</option>";
                            }
                            ?>
                        </select>

                        <div class="grid grid-cols-2 gap-3">
                            <input type="number" name="price" required placeholder="Price LKR"
                                value="<?php echo $edit_prod['price'] ?? ''; ?>"
                                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm outline-none focus:border-blue-500">
                            <input type="text" name="condition_badge" placeholder="Badge (e.g. 99%)"
                                value="<?php echo $edit_prod['condition_badge'] ?? ''; ?>"
                                class="w-full bg-slate-900/50 border border-slate-800 rounded-xl p-3 text-sm outline-none focus:border-blue-500">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Main Photo</label>
                            <input type="file" name="image_file"
                                class="text-xs text-slate-400 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-500/10 file:text-blue-400 hover:file:bg-blue-500/20">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-slate-500 uppercase">Gallery Photos</label>

                            <input type="file" name="more_photos[]" multiple accept="image/*" id="more_photos"
                                class="text-xs text-slate-400 block w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-purple-500/10 file:text-purple-400 hover:file:bg-purple-500/20">

                            <p class="text-[10px] text-slate-500">
                                Max 5 gallery photos only.
                            </p>
                        </div>

                        <button
                            class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 rounded-xl text-xs transition-all">
                            <?php echo $edit_prod ? 'Save Changes' : 'Publish Product'; ?>
                        </button>

                        <?php if($edit_prod): ?>
                        <a href="admin_dashboard.php"
                            class="block text-center text-xs text-slate-500 hover:text-white">Cancel Edit</a>
                        <?php endif; ?>
                    </form>

                    <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const input = document.getElementById('more_photos');
                        if (!input) return;

                        input.addEventListener('change', () => {
                            if (!input.files) return;
                            const max = 5;
                            if (input.files.length > max) {
                                alert('You can upload maximum 5 gallery photos.');
                                input.value = '';
                            }
                        });
                    });
                    </script>
                </section>

            </div>

            <!-- RIGHT COLUMN: TABLES -->
            <div class="lg:col-span-8 space-y-6">
                <!-- BOOKINGS -->
                <section class="glass-card rounded-3xl overflow-hidden">
                    <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-amber-400 flex items-center gap-2">
                            <i class="fa-solid fa-clock-rotate-left"></i> Recent Bookings
                        </h3>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-900/50 text-slate-500 text-[10px] uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-4">Product</th>
                                    <th class="px-6 py-4">Customer</th>
                                    <th class="px-6 py-4">Method</th>
                                    <th class="px-6 py-4 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <?php while($b = $bookings->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="px-6 py-4 font-semibold"><?php echo $b['prod_name']; ?></td>
                                    <td class="px-6 py-4 font-mono text-xs text-slate-400">
                                        <?php echo $b['customer_phone']; ?></td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="px-2 py-1 bg-blue-500/10 text-blue-400 rounded text-[10px] font-bold uppercase"><?php echo $b['delivery_method']; ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="https://wa.me/<?php echo $b['customer_phone']; ?>"
                                            class="text-emerald-500 hover:text-emerald-400">
                                            <i class="fa-brands fa-whatsapp text-lg"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- INVENTORY -->
                <section class="glass-card rounded-3xl overflow-hidden">
                    <div
                        class="p-6 border-b border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-purple-400 flex items-center gap-2">
                            <i class="fa-solid fa-layer-group"></i> Inventory Matrix
                        </h3>
                        <div class="flex gap-2 overflow-x-auto pb-2 md:pb-0 w-full md:w-auto custom-scrollbar">
                            <?php foreach(['all', 'new', 'used', 'covers', 'tempered', 'accessories'] as $f): ?>
                            <a href="?filter=<?php echo $f; ?>"
                                class="px-3 py-1.5 rounded-lg text-[10px] font-bold uppercase border <?php echo $current_filter == $f ? 'bg-purple-500 border-purple-500 text-white' : 'border-slate-800 text-slate-500'; ?>">
                                <?php echo $f; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-slate-900/50 text-slate-500 text-[10px] uppercase font-bold">
                                <tr>
                                    <th class="px-6 py-4">Item</th>
                                    <th class="px-6 py-4">Price</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/50">
                                <?php while($p = $products->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-800/20 transition-colors">
                                    <td class="px-6 py-4 flex items-center gap-3">
                                        <img src="<?php echo $p['image_url']; ?>"
                                            class="w-10 h-10 rounded-lg object-cover bg-slate-800">
                                        <div>
                                            <p class="font-bold text-slate-200"><?php echo $p['name']; ?></p>
                                            <p class="text-[10px] text-slate-500 uppercase">
                                                <?php echo $p['category']; ?></p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-blue-400">LKR
                                        <?php echo number_format($p['price']); ?></td>
                                    <td class="px-6 py-4">
                                        <a href="?toggle_stock=<?php echo $p['id']; ?>&status=<?php echo $p['is_available']; ?>&filter=<?php echo $current_filter; ?>"
                                            class="px-2 py-1 rounded text-[9px] font-black uppercase <?php echo $p['is_available'] ? 'bg-emerald-500/10 text-emerald-500' : 'bg-red-500/10 text-red-500'; ?>">
                                            <?php echo $p['is_available'] ? 'In Stock' : 'Hidden'; ?>
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <a href="?edit_product=<?php echo $p['id']; ?>"
                                            class="p-2 bg-blue-500/10 text-blue-400 rounded-lg hover:bg-blue-500/20 transition-all">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="?delete_product=<?php echo $p['id']; ?>"
                                            onclick="return confirm('Delete this item?')"
                                            class="p-2 bg-red-500/10 text-red-400 rounded-lg hover:bg-red-500/20 transition-all">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>
    </div>

</body>

</html>