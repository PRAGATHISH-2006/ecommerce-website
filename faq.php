<?php 
require_once 'includes/db_connect.php';
require_once 'includes/header.php'; 
?>

<!-- Hero Section -->
<section class="hero-section" style="height: 40vh; display: flex; align-items: center; justify-content: center; text-align: center; background: var(--primary-gradient); color: white; border-radius: 0 0 50px 50px; margin-bottom: 4rem;">
    <div class="container">
        <h1 class="display-4 fw-bold animate__animated animate__fadeInDown">Frequently Asked Questions</h1>
        <p class="lead mt-3 animate__animated animate__fadeInUp">Find answers to common questions about our products and services.</p>
    </div>
</section>

<!-- FAQ Accordion Section -->
<section class="container py-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="accordion accordion-flush shadow-sm rounded-4" id="faqAccordion">
                
                <!-- FAQ Item 1 -->
                <div class="accordion-item rounded-4 mb-3 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            How long does shipping take?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Standard shipping typically takes 3-5 business days within the continental US. Express shipping is available at checkout for 1-2 day delivery. International shipping times vary between 7-14 days.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="accordion-item rounded-4 mb-3 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            What is your return policy?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            We offer a hassle-free 30-day return policy. If you're not completely satisfied with your purchase, you can return it in its original condition for a full refund or exchange. Return shipping is free for all domestic orders.
                        </div>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="accordion-item rounded-4 mb-3 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Do you ship internationally?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Yes! We ship to over 50 countries worldwide. International shipping costs are calculated at checkout based on your location and the weight of your order.
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Item 4 -->
                <div class="accordion-item rounded-4 border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold rounded-4" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            How can I track my order?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Once your order ships, you will receive a confirmation email containing a tracking number. You can also track your order directly on our website by visiting the <a href="track-order.php">Track Order</a> page.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>