<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 50vh; display: flex; align-items: center; justify-content: center; text-align: center; background: var(--primary-gradient); color: white; border-radius: 0 0 50px 50px; margin-bottom: 4rem;">
    <div class="container">
        <h1 class="display-3 fw-bold animate__animated animate__fadeInDown">About Us</h1>
        <p class="lead mt-3 animate__animated animate__fadeInUp">We are dedicated to bringing you the best products at the most affordable prices.</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="container py-5">
    <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
            <h2 class="display-5 fw-bold mb-4">Our Story</h2>
            <p class="text-muted lead">Founded in 2026, VibrantShop started as a small dream to revolutionize online shopping. We believe that premium gadgets and stylish fashion should be accessible to everyone, everywhere.</p>
            <p class="text-muted mb-4">Over the years, we've partnered with top-tier manufacturers to bring you curated collections that you won't find anywhere else. Our commitment to quality, affordability, and exceptional customer service has made us a leader in the e-commerce space.</p>
            <a href="products/index.php" class="btn btn-custom btn-lg">Explore Our Products</a>
        </div>
        <div class="col-lg-6">
            <img src="https://img.freepik.com/free-vector/teamwork-concept-landing-page_52683-20165.jpg" alt="Our Story" class="img-fluid rounded-4 shadow-lg">
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="container py-5 mb-5 bg-light rounded-4 shadow-sm" style="padding: 4rem 2rem;">
    <div class="text-center mb-5">
        <h2 class="display-5 fw-bold">Our Core Values</h2>
        <div class="mx-auto bg-primary" style="height: 4px; width: 60px; border-radius: 2px;"></div>
    </div>
    <div class="row text-center g-4">
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                <div class="text-primary mb-3" style="font-size: 3rem;"><i class="bi bi-shield-check"></i></div>
                <h4 class="fw-bold">Quality First</h4>
                <p class="text-muted">We never compromise on the quality of our products. Every item is rigorously tested.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                <div class="text-primary mb-3" style="font-size: 3rem;"><i class="bi bi-heart"></i></div>
                <h4 class="fw-bold">Customer Focus</h4>
                <p class="text-muted">Our customers are at the heart of everything we do. We strive to exceed expectations.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                <div class="text-primary mb-3" style="font-size: 3rem;"><i class="bi bi-globe"></i></div>
                <h4 class="fw-bold">Sustainability</h4>
                <p class="text-muted">We are committed to eco-friendly practices and sustainable packaging for a better future.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>