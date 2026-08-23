<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; background: var(--secondary-gradient); color: var(--dark-color); border-radius: 0 0 50px 50px; margin-bottom: 4rem;">
    <div class="container">
        <h1 class="display-4 fw-bold animate__animated animate__fadeInDown">Contact Us</h1>
        <p class="lead mt-3 animate__animated animate__fadeInUp">We'd love to hear from you. Get in touch with our team.</p>
    </div>
</section>

<!-- Contact Form & Info -->
<section class="container py-5 mb-5">
    <div class="row g-5">
        <div class="col-lg-6">
            <h3 class="fw-bold mb-4">Send us a Message</h3>
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="contact-handler.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required placeholder="John Doe">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="john@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label fw-bold">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required placeholder="How can we help?">
                    </div>
                    <div class="mb-4">
                        <label for="message" class="form-label fw-bold">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required placeholder="Write your message here..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-custom w-100">Send Message</button>
                </form>
            </div>
        </div>
        
        <div class="col-lg-6">
            <h3 class="fw-bold mb-4">Contact Information</h3>
            <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-light">
                <div class="d-flex align-items-center mb-4">
                    <div class="text-primary me-3 fs-2"><i class="bi bi-geo-alt-fill"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Our Headquarters</h5>
                        <p class="text-muted mb-0">123 Commerce Blvd, Tech District<br>Innovation City, NY 10001</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <div class="text-primary me-3 fs-2"><i class="bi bi-telephone-fill"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Phone Number</h5>
                        <p class="text-muted mb-0">+1 (555) 123-4567<br>Mon-Fri, 9am-6pm EST</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <div class="text-primary me-3 fs-2"><i class="bi bi-envelope-fill"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1">Email Address</h5>
                        <p class="text-muted mb-0">support@vibrantshop.com</p>
                    </div>
                </div>
            </div>
            
            <!-- Map Placeholder -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="height: 250px;">
                <img src="https://via.placeholder.com/800x400?text=Map+Location" class="w-100 h-100" style="object-fit: cover;" alt="Map">
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>