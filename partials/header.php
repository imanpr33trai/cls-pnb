<?php
// CRUCIAL: Must be the very first line of the file.
include_once(__DIR__ . '/../config/config.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->

    <!-- Primary Meta Tags -->
    <!-- Primary Meta Tags -->
    <!-- Primary Meta Tags -->
    <title>Post Free Classified Ads | Advertise Your Business for Free</title>
    <meta name="title" content="Post Free Classified Ads | Advertise Your Business for Free">
    <meta name="description"
        content="Post your free classified ads on the leading cross-category platform in Punjab. Promote your business, services, or deals at zero cost!">
    <meta name="keywords"
        content="Free Classified Ads, Punjab Ads, Post Ad, Online Advertising, Business Promotion, Buy Sell Punjab">
    <meta name="author" content="Punjab Classified">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://punjabclassified.com/" />

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://punjabclassified.com/">
    <meta property="og:title" content="Post Free Classified Ads | Advertise Your Business for Free">
    <meta property="og:description"
        content="List your business or service for free in Punjab. Get noticed by thousands of buyers daily.">
    <meta property="og:image" content="https://punjabclassified.com/share-image.jpg">
    <meta property="og:site_name" content="Punjab Classified">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://punjabclassified.com/">
    <meta name="twitter:title" content="Post Free Classified Ads | Advertise Your Business for Free">
    <meta name="twitter:description"
        content="Post your free classified ads in Punjab. Reach your customers with zero cost and instant exposure.">
    <meta name="twitter:image" content="https://punjabclassified.com/share-image.jpg">

    <!-- Schema.org for Google -->
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Punjab Classified",
            "url": "https://punjabclassified.com/",
            "potentialAction": {
                "@type": "SearchAction",
                "target": "https://punjabclassified.com/search?q={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
    </script>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">

    <script src="https://cdn.tiny.cloud/1/4hdqtud4lm7rr5ajjcpq1i0wmf7iyocm5hgfjs5oyqmw5m8p/tinymce/6/tinymce.min.js"
        referrerpolicy="origin"
        onerror="tinyMceFallback()"></script>

    <script>
        function tinyMceFallback() {
            console.error('TinyMCE CDN failed to load.');
            // Show a message or fallback editor
            document.getElementById('editor-container').innerHTML = `
        <p style="color: red;">Editor failed to load. Please check your internet or try again later.</p>
        <textarea name="content" rows="10" cols="50" placeholder="Write here..."></textarea>
    `;
        }
    </script>


    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/output.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/19.2.16/css/intlTelInput.css" />
    <style>
        .intl-tel-input {
            width: 100%;
        }

        .is-invalid {
            border-color: red;
        }

        .is-valid {
            border-color: green;
        }

        .iti {
            width: 100%;
        }

        /* Style for the validation message */
        .invalid-feedback {
            display: block;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: 2px;
        }

        .invalid {
            border-color: red !important;
        }

        .password-strength {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>



    <title>Punjab Classified</title>
    <!-- <style>
        @media (max-width: 768px) {
            header nav {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style> -->
    <link rel="stylesheet" href="/assets/css/product-card.css">
</head>

<body>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });
        });
    </script>
    <div id="voiceSearchOverlay" class="voice-overlay d-none">
        <div class="close-btn" onclick="document.getElementById('voiceSearchOverlay').style.display='none';">&times;
        </div>
        <div class="voice-box">
            <p id="spokenText">Listening...</p>
            <div id="voiceSearchResults"></div>
        </div>
    </div>
    <header class=" shadow-md lg:px-5 xl:px-7 bg-[#191919] lg:flex-wrap">
        <nav class="container mx-auto px-4 sm:px-6 text-tiny lg:px-6 xl:px-8 flex items-center justify-between h-16 lg:h-14 xl:h-16 md:h-28">
            <!-- Logo -->
            <div class="flex-shrink-0 >
                <a href=" <?= $base_url ?>" class="flex items-center">
                <img class="h-10 lg:h-9 xl:h-10 w-auto" src="<?php echo $base_url; ?>assets/images/pnb-logo-full.svg" alt="Punjab Classified Logo" />
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex text-tiny items-center gap-3.5 space-x-4 text-sm lg:text-xs lg:gap-4 lg:space-x-3 xl:text-sm xl:gap-3.5 xl:space-x-4">
                <a href="/articles" class="theme-btn ">Articles</a>

                <?php include 'inner-search.php'; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_url; ?>logout.php" class="text-white flex items-center">
                        <img src="<?php echo $base_url; ?>assets/images/user.svg" alt="User Icon" class="h-5 w-5 lg:h-4 lg:w-4 xl:h-5 xl:w-5 mr-1">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>login.php" class=" flex items-center text-white">
                        <img src="<?php echo $base_url; ?>assets/images/user.svg" alt="User Icon" class="h-5 w-5 lg:h-4 lg:w-4 xl:h-5 xl:w-5 mr-1">
                        Login
                    </a>
                <?php endif; ?>

                <a href="<?= POST_AD_URL ?>" class="theme-btn px-4 py-2 lg:px-3 lg:py-1.5 xl:px-4 xl:py-2 shadow-sm">+ Post Ad</a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                    <span class="sr-only">Open main menu</span>
                    <!-- Hamburger Icon -->
                    <svg class="h-6 w-6 block" id="hamburger-icon" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <!-- Close Icon -->
                    <svg class="h-6 w-6 hidden" id="close-icon" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Menu -->
        <div class="md:hidden hidden" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="/articles" class="block px-3 py-2  text-base font-medium text-white ">Articles</a>



                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo $base_url; ?>logout.php" class="block px-3 py-2  text-base text-white font-medium>Logout</a>
                <?php else: ?>
                    <a href=" <?php echo $base_url; ?>login.php" class="block px-3 py-2 text-white hover:text-white/9 hover:bg-brand-100 text-base font-medium">Login</a>
                <?php endif; ?>

                <a href="<?= POST_AD_URL ?>" class="block w-full text-left  text-white px-3 py-2 text-base font-medium mt-2">+ Post Ad</a>
            </div>
        </div>
    </header>