<?php
require 'includes/config.php';
require 'includes/functions.php';

$breads_stmt = $pdo->query("SELECT name, price, image_path FROM breads");
$breads = $breads_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Welcome to Brede Yuru</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-image: url('assets/images/brede lights.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: #343a40;
        }

        /* HEADER */
        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 20px 20px;
            background: linear-gradient(90deg, #1f1c2c, #ffc107);
            color: white;
            border-radius: 50px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .logo-container {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        .logo-container img {
            height: 80px;
            width: auto;
            border-radius: 12px;
            background: transparent;
        }

        .header-text {
            text-align: center;
        }

        .header-text h1 {
            font-size: 2.5em;
            margin: 0;
        }

        .header-text p {
            font-size: 1.1em;
            margin-top: 5px;
        }

        /* HERO SECTION */
        .hero {
            text-align: center;
            padding: 40px 20px;
        }

        .hero img {
            max-width: 250px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            animation: float 4s ease-in-out infinite;
        }

        .hero h2 {
            font-size: 1.8em;
            margin: 20px 0 10px;
            color: #ffc107;
        }

        .hero p {
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 60px;
            flex-wrap: wrap;
            margin-top: 350px;
            text-decoration: underline white;
        }

        .cta-buttons a {
            background: linear-gradient(135deg, #ff6f61, #ffc107);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            transition: transform 0.2s ease;
        }

        .cta-buttons a:hover {
            transform: scale(1.05);
        }

        /* FEATURES */
        .features {
            max-width: 1600px;
            margin: 40px auto 0 auto;
            padding: 0 20px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
        }

        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h3 {
            color: #ffc107;
            margin-bottom: 8px;
        }

        /* FLOAT ANIMATION */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        /* Slideshow container */
        .sandwich-bg-slider {
            position: absolute;
            top: 55%;
            left: 50%;
            width: 600px;
            height: 400px;
            transform: translate(-50%, -50%);
            z-index: -1; /* behind text */
        }

        /* Each slide */
        .sandwich-bg-slider .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            transition: filter 0.3s ease, opacity 0.3s ease;
        }

        .sandwich-bg-slider .swiper-slide.elevated {
            transform: translateY(-50px) !important;
            transition: transform 0.3s ease;
        }

        .sandwich-bg-slider .swiper-slide.active-slide {
            transform: translateY(0) !important;
            transition: transform 0.3s ease;
        }

        .circle-bg {
            max-width: 400px;  /* Increase from 300px */
            max-height: 400px; /* Increase from 300px */
            border-radius: 50%;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 40px rgba(0,0,0,0.3);
        }

        .sandwich-bg-slider {
            width: 700px;  /* increase for more room */
            height: 500px;
            position: absolute;
            top: 55%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
        }

        .circle-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.9) saturate(1.2);
            border-radius: 50%;
            user-select: none;
            pointer-events: none;
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .sandwich-bg-slider {
                width: 250px;
                height: 250px;
            }
            .circle-bg {
                max-width: 200px;
                max-height: 200px;
            }
        }

        /* MOBILE RESPONSIVE HEADER */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                padding: 20px 10px;
                border-bottom-left-radius: 30px;
                border-bottom-right-radius: 30px;
                text-align: center;
            }

            .logo-container {
                position: static;
                transform: none;
                margin-bottom: 10px;
            }

            .logo-container img {
                height: 60px;
                width: auto;
            }

            .header-text h1 {
                font-size: 1.8em;
            }

            .header-text p {
                font-size: 1em;
            }
        }

        @media (max-width: 768px) {
            .sandwich-bg-slider {
                width: 400px;
                height: 400px;
            }
            .circle-bg {
                max-width: 300px;
                max-height: 300px;
            }
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo-container">
            <img src="assets/images/Logo/Bred.png" alt="Brede Yuru Logo" />
        </div>
        <div class="header-text">
            <h1>Brede Yuru 🥪</h1>
            <p>Enjoy our delicious food</p>
        </div>
    </div>

    <div class="sandwich-bg-slider swiper">
        <div class="swiper-wrapper">
            <?php foreach ($breads as $bread): ?>
                <div class="swiper-slide">
                    <div class="circle-bg">
                        <img src="<?= htmlspecialchars($bread['image_path'] ?: 'assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($bread['name']) ?>" />
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="cta-buttons">
        <a href="login.php">Order Now</a>
        <a href="register.php">Join Us</a>
    </div>

    <div class="features">
        <div class="features-grid">
            <div class="feature-card">
                <h3>🥖 Fresh Bread</h3>
                <p>Baked daily for unbeatable taste.</p>
            </div>
            <div class="feature-card">
                <h3>🍅 Premium Ingredients</h3>
                <p>Only the freshest veggies & meats.</p>
            </div>
            <div class="feature-card">
                <h3>⚡ Fast Service</h3>
                <p>Ready when you are.</p>
            </div>
            <div class="feature-card">
                <h3>📱 Easy Ordering</h3>
                <p>From phone to plate in minutes.</p>
            </div>
        </div>
    </div>

<script>
    var swiper = new Swiper('.sandwich-bg-slider', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 3,
        loop: true,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: false,
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        on: {
            init: function () {
                updateBlur(this);
            },
            slideChangeTransitionEnd: function () {
                updateBlur(this);
            }
        }
    });

    function updateBlur(swiper) {
        swiper.slides.forEach(slideEl => {
            slideEl.style.filter = 'blur(5px)';
            slideEl.style.opacity = '0.5';
            slideEl.classList.remove('elevated');
            slideEl.classList.remove('active-slide');
        });

        const activeSlide = swiper.slides[swiper.activeIndex];
        if (activeSlide) {
            activeSlide.style.filter = 'blur(0)';
            activeSlide.style.opacity = '1';
            activeSlide.classList.add('active-slide');
        }

        const prevIndex = (swiper.activeIndex - 1 + swiper.slides.length) % swiper.slides.length;
        if (swiper.slides[prevIndex]) {
            swiper.slides[prevIndex].style.filter = 'blur(3px)';
            swiper.slides[prevIndex].style.opacity = '0.7';
            swiper.slides[prevIndex].classList.add('elevated');
        }

        const nextIndex = (swiper.activeIndex + 1) % swiper.slides.length;
        if (swiper.slides[nextIndex]) {
            swiper.slides[nextIndex].style.filter = 'blur(3px)';
            swiper.slides[nextIndex].style.opacity = '0.7';
            swiper.slides[nextIndex].classList.add('elevated');
        }
    }

</script>

</body>
</html>
