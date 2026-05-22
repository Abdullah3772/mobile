<?php
// admin_login.php
session_start();
include 'config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // No Security Request Override Matcher rule
    if ($username === 'abdullah' && $password === '12345') {
        $_SESSION['admin_auth'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Wrong structural credentials, boss!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gateway Access</title>
    <!-- TAILWIND CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FONT AWESOME FOR ICONS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- GOOGLE FONTS -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Poppins', 'sans-serif'],
                }
            }
        }
    }
    </script>

    <style>
    /* Premium Background Gradient Layer */
    .premium-bg {
        background: radial-gradient(circle at 50% 50%, #0f172a 0%, #020617 100%);
    }

    /* Animated Cyber Mesh Effect */
    .cyber-grid {
        background-image: linear-gradient(rgba(14, 165, 233, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(14, 165, 233, 0.03) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    /* Glassmorphism Card with subtle Cyan Glow */
    .glass-card {
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(14, 165, 233, 0.15);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4),
            0 0 40px rgba(14, 165, 233, 0.03);
    }
    </style>
</head>

<body class="premium-bg cyber-grid min-h-screen flex items-center justify-center p-4 antialiased">

    <!-- BACK TO STORE BUTTON -->
    <div class="absolute top-6 left-6">
        <a href="index.php"
            class="text-xs text-slate-400 hover:text-cyan-400 transition-colors flex items-center gap-2 bg-slate-900/50 px-4 py-2 rounded-xl border border-slate-800/80 backdrop-blur">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            Back to Shop
        </a>
    </div>

    <!-- MAIN ACCESS CARD -->
    <div
        class="max-w-md w-full glass-card p-8 rounded-[28px] relative overflow-hidden transition-all duration-300 hover:border-cyan-500/30">

        <!-- Top Tech Accent Bar -->
        <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-blue-600 via-cyan-400 to-emerald-500">
        </div>

        <!-- Brand Header Section -->
        <div class="text-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-cyan-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-cyan-500/10">
                <i class="fa-solid fa-shield-halved text-white text-lg"></i>
            </div>
            <h2 class="text-xl font-extrabold text-white tracking-tight mb-1">
                Abdullah Mobile World
            </h2>
            <p class="text-xs text-slate-400 font-medium">
                Secure Terminal Gateway Access
            </p>
        </div>

        <!-- ERROR PROMPT -->
        <?php if($error): ?>
        <div
            class="bg-red-500/10 border border-red-500/20 text-red-400 text-xs p-3.5 rounded-xl font-medium mb-5 flex items-center gap-2.5">
            <i class="fa-solid fa-circle-exclamation text-sm"></i>
            <span><?php echo $error; ?></span>
        </div>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="POST" class="space-y-4">

            <!-- Username Input Group -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 text-xs">
                    <i class="fa-solid fa-user-shield"></i>
                </span>
                <input type="text" name="username" placeholder="Admin ID" required autocomplete="off"
                    class="w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all duration-200">
            </div>

            <!-- Password Input Group -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500 text-xs">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input type="password" name="password" placeholder="Access Code" required
                    class="w-full pl-11 pr-4 py-3 bg-slate-950/60 border border-slate-800 rounded-xl text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 transition-all duration-200">
            </div>

            <!-- Security Check Notice -->
            <div class="flex items-center gap-2 pt-1 text-[11px] text-slate-500">
                <i class="fa-solid fa-circle-info text-cyan-500/70"></i>
                <span>IP and access sessions are securely managed.</span>
            </div>

            <!-- Submit Button with Premium Hover Effect -->
            <button type="submit"
                class="w-full mt-2 bg-gradient-to-r from-blue-600 via-cyan-500 to-cyan-400 hover:opacity-95 text-slate-950 font-bold py-3 rounded-xl text-sm transition-all duration-300 shadow-md shadow-cyan-500/10 hover:shadow-cyan-500/20 active:scale-[0.99] flex items-center justify-center gap-2">
                <i class="fa-solid fa-key text-xs"></i>
                Verify & Enter
            </button>
        </form>

        <!-- Footer Accent -->
        <p class="text-center text-[10px] text-slate-600 mt-8 tracking-wide uppercase font-semibold">
            &copy; 2026 Powered by Premium Terminal v2
        </p>
    </div>

</body>

</html>