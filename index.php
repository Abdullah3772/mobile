<?php
include 'config/db.php';

/*
========================================
PREMIUM MOBILE SHOP WEBSITE
FULLY FIXED VERSION
========================================
*/

// =========================
// FETCH LIVE SETTINGS
// =========================
$settings_res = $conn->query("SELECT * FROM shop_settings WHERE id = 1");

$shop_settings = ($settings_res && $settings_res->num_rows > 0)
    ? $settings_res->fetch_assoc()
    : [
        'announcement_text' => '🔥 Welcome to Abdullah Mobile World',
        'is_announcement_active' => 1,
        'whatsapp_target_phone' => '+94771234567'
    ];

$live_announcement = $shop_settings['announcement_text'];
$announcement_active = $shop_settings['is_announcement_active'];
$dynamic_whatsapp = preg_replace('/[^0-9]/', '', $shop_settings['whatsapp_target_phone']);

// =========================
// ALLOWED CATEGORIES
// =========================
$allowed_categories = ['new','used','covers','tempered','accessories'];
$category = isset($_GET['cat']) ? $_GET['cat'] : 'new';

if(!in_array($category,$allowed_categories)){
    $category = 'new';
}

// =========================
// SEARCH
// =========================
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// =========================
// QUERY PRODUCTS
// =========================
if(!empty($search)){

    $searchTerm = "%{$search}%";

    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND is_available = 1 AND name LIKE ? ORDER BY id DESC");

    $stmt->bind_param("ss",$category,$searchTerm);

}else{

    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND is_available = 1 ORDER BY id DESC");

    $stmt->bind_param("s",$category);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($shop_name ?? 'Abdullah Mobile World'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --premium-gradient: linear-gradient(135deg, #0044cc, #002288);
        --premium-blue: #0044cc;
        --brand-logo-color: #0033aa;

        --bg-body: #f5f7fb;
        --bg-card: #ffffff;
        --bg-img-box: #eef2f7;
        --text-main: #0b132b;
        --text-muted: #4e5d78;
        --border-color: rgba(0, 68, 204, 0.08);
        --header-blur-bg: rgba(255, 255, 255, 0.92);
    }

    [data-bs-theme="dark"] {
        --premium-gradient: linear-gradient(135deg, #00f0ff, #0077ff);
        --premium-blue: #38bdf8;
        --brand-logo-color: #00f0ff;

        --bg-body: #070a13;
        --bg-card: #111726;
        --bg-img-box: #0a0e1a;
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --border-color: rgba(0, 240, 255, 0.1);
        --header-blur-bg: rgba(17, 23, 38, 0.92);
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: var(--bg-body);
        color: var(--text-main);
        padding-bottom: 90px;
        transition: .3s ease;
    }

    .logo-text {
        color: var(--brand-logo-color);
        font-weight: 800;
        text-decoration: none;
    }

    .premium-topbar {
        background: linear-gradient(90deg, #002288, #0044cc, #0077ff);
        color: white;
        font-size: .75rem;
        padding: 10px 0;
    }

    .glass-header {
        background: var(--header-blur-bg);
        backdrop-filter: blur(20px);
        border-bottom: 1px solid var(--border-color);
    }

    .nav-link-custom {
        color: var(--text-muted);
        text-decoration: none;
        font-size: .9rem;
        padding: .55rem .9rem;
        border-radius: 14px;
        transition: .25s ease;
        font-weight: 600;
    }

    .nav-link-custom:hover {
        background: rgba(0, 68, 204, 0.08);
        color: var(--premium-blue);
    }

    .active-premium {
        background: var(--premium-gradient);
        color: white !important;
    }

    [data-bs-theme="dark"] .active-premium {
        color: #07111c !important;
    }

    .announcement-bar {
        background: linear-gradient(135deg, #00c6ff, #0072ff);
        color: white;
        padding: 14px 20px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 114, 255, 0.15);
    }

    .hero-container {
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid var(--border-color);
    }

    .carousel-item img {
        animation: slowZoom 8s linear infinite alternate;
    }

    @keyframes slowZoom {
        from {
            transform: scale(1);
        }

        to {
            transform: scale(1.08);
        }
    }

    .hero-banner-img {
        filter: brightness(0.65);
    }

    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.2));
        z-index: 1;
    }

    .hero-content {
        position: absolute;
        top: 50%;
        left: 7%;
        transform: translateY(-50%);
        z-index: 5;
        color: white;
        max-width: 500px;
    }

    .hero-content h1 {
        font-size: clamp(1.7rem, 4vw, 3.2rem);
        font-weight: 800;
        margin-bottom: 12px;
    }

    .hero-content p {
        font-size: .95rem;
        opacity: .9;
        margin: 0;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 8px 14px;
        border-radius: 50px;
        margin-bottom: 18px;
        font-size: .8rem;
        font-weight: 700;
    }

    .floating-phones {
        position: absolute;
        right: 5%;
        top: 50%;
        transform: translateY(-50%);
        width: 350px;
        height: 100%;
        z-index: 4;
    }

    .floating-phone {
        position: absolute;
        width: 120px;
        object-fit: contain;
        filter: drop-shadow(0 20px 35px rgba(0, 0, 0, 0.35));
        animation: floatPhone 4s ease-in-out infinite;
    }

    .phone-1 {
        top: 12%;
        right: 180px;
        rotate: -12deg;
    }

    .phone-2 {
        top: 32%;
        right: 60px;
        width: 145px;
        animation-delay: 1s;
        rotate: 8deg;
    }

    .phone-3 {
        bottom: 8%;
        right: 220px;
        width: 105px;
        animation-delay: 2s;
        rotate: -6deg;
    }

    @keyframes floatPhone {
        0% {
            translate: 0 0px;
        }

        50% {
            translate: 0 -12px;
        }

        100% {
            translate: 0 0px;
        }
    }

    @media(max-width:768px) {

        .hero-content {
            left: 20px;
            right: 20px;
            max-width: 100%;
        }

        .hero-content h1 {
            font-size: 1.5rem;
        }

        .hero-content p {
            font-size: .8rem;
        }

        .floating-phones {
            width: 180px;
            right: 0;
            opacity: .9;
        }

        .floating-phone {
            width: 70px;
        }

        .phone-1 {
            right: 90px;
        }

        .phone-2 {
            width: 90px;
            right: 20px;
        }

        .phone-3 {
            width: 60px;
            right: 120px;
        }
    }

    to {
        transform: scale(1.08);
    }
    }

    .search-wrapper {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 8px;
    }

    [data-bs-theme="dark"] .search-wrapper {
        background: rgba(17, 23, 38, 0.7);
    }

    .search-input {
        border: none;
        background: transparent;
        color: var(--text-main);
        padding-left: 45px;
    }

    .search-input:focus {
        box-shadow: none;
        background: transparent;
        color: var(--text-main);
    }

    .search-icon-left {
        position: absolute;
        left: 20px;
        color: var(--text-muted);
    }

    .premium-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 28px;
        padding: 14px;
        overflow: hidden;
        position: relative;
        transition: .35s ease;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        height: 100%;
    }

    .premium-card:hover {
        transform: translateY(-8px);
        border-color: rgba(0, 240, 255, .3);
        box-shadow: 0 25px 45px rgba(0, 119, 255, .12);
    }

    .img-container {
        background: var(--bg-img-box);
        border-radius: 22px;
        aspect-ratio: 1/1;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        position: relative;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: .35s ease;
    }

    .premium-card:hover .product-image {
        transform: scale(1.05);
    }

    .badge-condition {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #0b132b;
        color: white;
        border-radius: 10px;
        padding: 5px 10px;
        font-size: .65rem;
        font-weight: 700;
        z-index: 5;
    }

    .product-title {
        font-size: .9rem;
        font-weight: 600;
        margin-top: 15px;
        min-height: 45px;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--premium-blue);
    }

    .btn-premium {
        background: var(--premium-gradient);
        color: white !important;
        border: none;
        border-radius: 14px;
        font-size: .8rem;
        font-weight: 700;
        padding: 11px 14px;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    [data-bs-theme="dark"] .btn-premium {
        color: #07111c !important;
    }

    .btn-whatsapp {
        background: #10b981;
        color: white !important;
        border-radius: 14px;
        font-size: .8rem;
        font-weight: 700;
        padding: 11px 14px;
        text-decoration: none;
        display: block;
        text-align: center;
    }

    .mobile-nav-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: var(--header-blur-bg);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--border-color);
        z-index: 999;
        display: flex;
        padding: 10px 5px;
        border-radius: 22px 22px 0 0;
    }

    .mobile-nav-item {
        flex: 1;
        text-align: center;
        color: var(--text-muted);
        text-decoration: none;
        font-size: .7rem;
        font-weight: 600;
    }

    .mobile-nav-item i {
        display: block;
        font-size: 1.1rem;
        margin-bottom: 4px;
    }

    .active-mob {
        color: var(--premium-blue);
    }

    .floating-theme-btn {
        position: fixed;
        bottom: 100px;
        right: 20px;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        border: none;
        background: var(--premium-gradient);
        color: white;
        z-index: 999;
    }

    .premium-modal .modal-content {
        background: var(--bg-card);
        border-radius: 28px;
        border: 1px solid var(--border-color);
    }

    .modal-img-wrap {
        background: var(--bg-img-box);
        border-radius: 22px;
        padding: 20px;
    }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="premium-topbar">
        <div class="container-xl d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span>
                <i class="fa-solid fa-location-dot me-2"></i>
                <?php echo htmlspecialchars($shop_address ?? 'Store Location'); ?>
            </span>

            <span>
                <i class="fa-solid fa-phone me-2"></i>
                <?php echo htmlspecialchars($shop_phone ?? 'Phone Number'); ?>
            </span>
        </div>
    </div>

    <!-- HEADER -->
    <header class="glass-header sticky-top py-3">

        <div class="container-xl d-flex justify-content-between align-items-center">

            <a href="index.php" class="logo-text fs-4">
                Abdullah Mobile World
            </a>

            <nav class="d-none d-md-flex align-items-center gap-2">

                <?php
                $menus = [
                    'new'=>'New Phones',
                    'used'=>'2nd Phones',
                    'covers'=>'Back Covers',
                    'tempered'=>'Tempered',
                    'accessories'=>'Accessories'
                ];

                foreach($menus as $key=>$label):
                ?>

                <a href="index.php?cat=<?php echo $key; ?>"
                    class="nav-link-custom <?php echo ($category === $key) ? 'active-premium' : ''; ?>">
                    <?php echo $label; ?>
                </a>

                <?php endforeach; ?>

            </nav>

            <a href="admin_login.php" class="btn-premium px-3 py-2">
                <i class="fa-solid fa-user-gear me-1"></i>
                Admin
            </a>

        </div>
    </header>

    <!-- ANNOUNCEMENT -->
    <?php if($announcement_active == 1 && !empty($live_announcement)): ?>

    <div class="container-xl mt-4">

        <div class="announcement-bar d-flex align-items-center gap-3">
            <i class="fa-solid fa-bullhorn fs-5"></i>

            <marquee behavior="scroll" direction="left" scrollamount="5" class="fw-semibold small">
                <?php echo htmlspecialchars($live_announcement); ?>
            </marquee>
        </div>

    </div>

    <?php endif; ?>

    <!-- BANNER -->
    <div class="container-xl mt-4">

        <div class="hero-container shadow-sm position-relative overflow-hidden">

            <div id="premiumHeroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel"
                data-bs-interval="2800">

                <div class="carousel-inner" style="aspect-ratio: 21/9; max-height:380px;">

                    <div class="carousel-item active h-100 position-relative">

                        <img src="banner1.jpg" class="w-100 h-100 object-fit-cover hero-banner-img"
                            onerror="this.src='https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?q=80&w=1400&auto=format&fit=crop'">

                        <div class="hero-overlay"></div>

                        <div class="hero-content">
                            <span class="hero-badge">
                                <i class="fa-solid fa-fire"></i>
                                Trending Devices
                            </span>

                            <h1>Latest Premium Smartphones</h1>

                            <p>
                                iPhone • Samsung • Xiaomi • OnePlus • Vivo
                            </p>
                        </div>

                        <div class="floating-phones">
                            <img src="https://fdn2.gsmarena.com/vv/pics/samsung/samsung-galaxy-s23-5g-1.jpg"
                                class="floating-phone phone-1">

                            <img src="https://fdn2.gsmarena.com/vv/pics/apple/apple-iphone-15-pro-max-1.jpg"
                                class="floating-phone phone-2">

                            <img src="https://fdn2.gsmarena.com/vv/pics/xiaomi/xiaomi-redmi-note-13-pro-plus-1.jpg"
                                class="floating-phone phone-3">
                        </div>

                    </div>

                    <div class="carousel-item h-100 position-relative">

                        <img src="banner2.jpg" class="w-100 h-100 object-fit-cover hero-banner-img"
                            onerror="this.src='https://images.unsplash.com/photo-1598327105666-5b89351aff97?q=80&w=1400&auto=format&fit=crop'">

                        <div class="hero-overlay"></div>

                        <div class="hero-content">
                            <span class="hero-badge">
                                <i class="fa-solid fa-bolt"></i>
                                Special Deals
                            </span>

                            <h1>Massive Mobile Offers</h1>

                            <p>
                                Exchange • Installments • Brand New Stock
                            </p>
                        </div>

                        <div class="floating-phones">
                            <img src="https://fdn2.gsmarena.com/vv/pics/xiaomi/xiaomi-14-ultra-1.jpg"
                                class="floating-phone phone-1">

                            <img src="https://fdn2.gsmarena.com/vv/pics/oneplus/oneplus-12-1.jpg"
                                class="floating-phone phone-2">

                            <img src="https://fdn2.gsmarena.com/vv/pics/google/google-pixel-8-pro-1.jpg"
                                class="floating-phone phone-3">
                        </div>

                    </div>

                    <div class="carousel-item h-100 position-relative">

                        <img src="banner3.jpg" class="w-100 h-100 object-fit-cover hero-banner-img"
                            onerror="this.src='https://images.unsplash.com/photo-1580910051074-3eb694886505?q=80&w=1400&auto=format&fit=crop'">

                        <div class="hero-overlay"></div>

                        <div class="hero-content">
                            <span class="hero-badge">
                                <i class="fa-solid fa-mobile-screen"></i>
                                Accessories Hub
                            </span>

                            <h1>Covers • Glass • Accessories</h1>

                            <p>
                                Premium Quality Mobile Accessories Available
                            </p>
                        </div>

                        <div class="floating-phones">
                            <img src="https://fdn2.gsmarena.com/vv/pics/apple/apple-iphone-14-pro-max-1.jpg"
                                class="floating-phone phone-1">

                            <img src="https://fdn2.gsmarena.com/vv/pics/vivo/vivo-v30-1.jpg"
                                class="floating-phone phone-2">

                            <img src="https://fdn2.gsmarena.com/vv/pics/oppo/oppo-reno11-f-1.jpg"
                                class="floating-phone phone-3">
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- MAIN -->
    <main class="container-xl py-4">

        <!-- SEARCH -->
        <div class="row justify-content-center mb-5">

            <div class="col-md-8 col-lg-6">

                <form method="GET" class="search-wrapper d-flex align-items-center position-relative">

                    <input type="hidden" name="cat" value="<?php echo htmlspecialchars($category); ?>">

                    <i class="fa-solid fa-magnifying-glass search-icon-left"></i>

                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        class="form-control search-input flex-grow-1" placeholder="Search premium devices...">

                    <button type="submit" class="btn-premium px-4 py-2">
                        Search
                    </button>

                </form>

            </div>

        </div>

        <!-- TITLE -->
        <div class="d-flex align-items-center gap-2 mb-4">
            <span class="d-inline-block rounded-pill"
                style="width:4px;height:24px;background:var(--premium-gradient);"></span>

            <h2 class="fw-bold fs-5 m-0 text-capitalize">
                <?php echo htmlspecialchars($category); ?> Collection
            </h2>
        </div>

        <!-- PRODUCTS -->
        <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 g-3 g-md-4">

            <?php if($result && $result->num_rows > 0): ?>

            <?php while($row = $result->fetch_assoc()):

                $image = !empty($row['image_url'])
                    ? $row['image_url']
                    : 'https://via.placeholder.com/400x400?text=No+Image';
            ?>

            <div class="col">

                <div class="premium-card">

                    <div class="img-container">

                        <?php if(!empty($row['condition_badge'])): ?>

                        <span class="badge-condition">
                            <?php echo htmlspecialchars($row['condition_badge']); ?>
                        </span>

                        <?php endif; ?>

                        <img src="<?php echo htmlspecialchars($image); ?>" class="product-image" loading="lazy"
                            onclick="openPremiumModal('<?php echo htmlspecialchars($image); ?>','<?php echo htmlspecialchars(addslashes($row['name'])); ?>','LKR <?php echo number_format($row['price'],2); ?>')"
                            style="cursor:pointer;">

                    </div>

                    <div class="mt-2">

                        <h3 class="product-title">
                            <?php echo htmlspecialchars($row['name']); ?>
                        </h3>

                        <p class="product-price my-1">
                            LKR <?php echo number_format($row['price'],2); ?>
                        </p>

                        <div class="d-grid gap-2 mt-3">

                            <a href="book.php?id=<?php echo $row['id']; ?>" class="btn-premium">
                                Book Now
                            </a>

                            <a href="https://wa.me/<?php echo $dynamic_whatsapp; ?>?text=I+want+to+buy:+<?php echo urlencode($row['name']); ?>"
                                target="_blank" class="btn-whatsapp">

                                <i class="fa-brands fa-whatsapp me-1"></i>
                                WhatsApp

                            </a>

                        </div>

                    </div>

                </div>

            </div>

            <?php endwhile; ?>

            <?php else: ?>

            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-box-open fs-1 opacity-50 mb-3"></i>
                <h5>No Products Found 😅</h5>
            </div>

            <?php endif; ?>

        </div>

    </main>

    <!-- MOBILE NAV -->
    <div class="mobile-nav-bar d-flex d-md-none">

        <?php
        $mobileMenus = [
            'new'=>['mobile-screen','New'],
            'used'=>['rotate','2nd'],
            'covers'=>['shield','Covers'],
            'tempered'=>['tablet-screen-button','Glass'],
            'accessories'=>['plug','Accs']
        ];

        foreach($mobileMenus as $key=>$item):
        ?>

        <a href="index.php?cat=<?php echo $key; ?>"
            class="mobile-nav-item <?php echo ($category === $key) ? 'active-mob' : ''; ?>">

            <i class="fa-solid fa-<?php echo $item[0]; ?>"></i>
            <span><?php echo $item[1]; ?></span>

        </a>

        <?php endforeach; ?>

    </div>

    <!-- THEME BTN -->
    <button onclick="toggleDarkMode()" class="floating-theme-btn">
        <i id="theme-icon" class="fa-solid fa-moon"></i>
    </button>

    <!-- MODAL -->
    <div class="modal fade premium-modal" id="quickModal" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered modal-sm">

            <div class="modal-content border-0 shadow-lg">

                <div class="modal-body position-relative">

                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal"></button>

                    <div class="modal-img-wrap mb-3">
                        <img id="modalImage" class="img-fluid rounded" style="max-height:240px;object-fit:contain;">
                    </div>

                    <h4 id="modalTitle" class="fs-6 fw-bold mb-1"></h4>
                    <p id="modalPrice" class="product-price fs-5 m-0"></p>

                </div>

            </div>

        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
    const themeIcon = document.getElementById('theme-icon');
    const htmlElement = document.documentElement;

    if (localStorage.getItem('theme') === 'dark') {
        htmlElement.setAttribute('data-bs-theme', 'dark');
        themeIcon.className = 'fa-solid fa-sun';
    }

    function toggleDarkMode() {

        if (htmlElement.getAttribute('data-bs-theme') === 'dark') {

            htmlElement.setAttribute('data-bs-theme', 'light');
            localStorage.setItem('theme', 'light');
            themeIcon.className = 'fa-solid fa-moon';

        } else {

            htmlElement.setAttribute('data-bs-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeIcon.className = 'fa-solid fa-sun';
        }
    }

    let bsModal = new bootstrap.Modal(document.getElementById('quickModal'));

    function openPremiumModal(image, title, price) {

        document.getElementById('modalImage').src = image;
        document.getElementById('modalTitle').innerText = title;
        document.getElementById('modalPrice').innerText = price;

        bsModal.show();
    }
    </script>

</body>

</html>