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
        referrerpolicy="origin"></script>

    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->
    <!-- other meta tags -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
    <style>


    </style>
    <link rel="stylesheet" href="/assets/css/product-card.css">
</head>

<body>
    <div id="voiceSearchOverlay" class="voice-overlay d-none">
        <div class="close-btn" onclick="document.getElementById('voiceSearchOverlay').style.display='none';">&times;
        </div>
        <div class="voice-box">
            <p id="spokenText">Listening...</p>
            <div id="voiceSearchResults"></div>
        </div>
    </div>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container justify-content-center">
                <a class="navbar-brand" href="<?= $base_url ?>"><img
                        src="<?php echo $base_url; ?>assets/images/pnb-logo-full.svg" alt="" /></a>

                <div class="d-none d-md-flex align-items-center justify-content-between w-100 header-links-new">
                    <!-- <button type="button" class="theme-btn">Articles</button> -->
                    <a href="<?= ARTICLES_URL ?>" class="theme-btn text-decoration-none">Articles</a>
                    <?php include 'inner-search.php'; ?>







                    <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- User is logged in -->
                    <a href="<?php echo $base_url; ?>logout.php" class="ms-3 text-white text-decoration-none"><img
                            src="<?php echo $base_url; ?>assets/images/user.svg" alt="" class="me-2">Logout</a>
                    <?php else: ?>
                    <!-- Not logged in -->
                    <a href="<?php echo $base_url; ?>login.php" class="text-white text-decoration-none">
                        <img src="<?php echo $base_url; ?>assets/images/user.svg" alt="" class="me-2">Login
                    </a>
                    <?php endif; ?>

                    <!-- <button type="button" class="theme-btn">+ Post Ad</button> -->
                    <a href="<?= POST_AD_URL ?>" class="theme-btn text-decoration-none ">+ Post
                        Ad</a>
                </div>

                <button class="navbar-toggler d-lg-none d-md-none" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span><img src="<?php echo $base_url; ?>assets/images/humbergar.svg" alt=""></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarText">
                    <ul class="navbar-nav d-flex d-md-none">
                        <li class="nav-item">
                            <a class="nav-link" href="#">Articles</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Post Ads</a>
                        </li>
                        <li class="nav-item">
                            <?php if (isset($_SESSION['user_id'])): ?>
                            <a class="nav-link" href="<?php echo $base_url; ?>logout.php">Logout</a>
                            <?php else: ?>
                            <a class="nav-link" href="<?php echo $base_url; ?>login.php">Login</a>
                            <?php endif; ?>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </header>