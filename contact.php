<?php
session_start();
require_once __DIR__ . '/config/database.php';

$msg = "";
$status = ""; // success or error

if (isset($_POST['send'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    
    // Create table if not exists
    $conn->query("CREATE TABLE IF NOT EXISTS `contacts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL,
      `message` text NOT NULL,
      `status` enum('Unread','Read') DEFAULT 'Unread',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);
    
    if ($stmt->execute()) {
        $status = "success";
    }
}
?>
<?php
$page_title = "Contact Us | Childrens-Store";
$page_desc = "Have questions about our kids clothing or toys? Get in touch with our team today. We are happy to help!";
$page_keywords = "contact childrens store, customer support kids toys, phone number, email address";
require_once 'includes/header.php';
?>


<style>
.contact-container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 5%;
}

.contact-card {
    background: #fff;
    border-radius: 24px;
    border: 2px solid var(--soft-bg);
    padding: 3rem 2.5rem;
    box-shadow: 0 10px 30px rgba(159, 107, 85, 0.05);
    margin-bottom: 3rem;
}

.contact-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group label {
    font-weight: 700;
    font-size: 0.95rem;
    color: var(--kids-brown);
    margin-bottom: 8px;
    font-family: 'Quicksand', sans-serif;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 15px;
    border-radius: 15px;
    border: 2px solid var(--soft-bg);
    background: #FFFBF9;
    font-family: inherit;
    font-size: 0.95rem;
    color: var(--dark);
    outline: none;
    transition: all 0.3s ease;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: var(--kids-blue);
    background: #fff;
    box-shadow: 0 0 10px rgba(84, 180, 235, 0.1);
}

.contact-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 3rem;
}

.info-card {
    background: #fff;
    border-radius: 20px;
    border: 2px solid var(--soft-bg);
    padding: 2rem;
    text-align: center;
    box-shadow: 0 6px 20px rgba(159, 107, 85, 0.03);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.info-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 25px rgba(159, 107, 85, 0.08);
}

.info-card i {
    font-size: 2rem;
    margin-bottom: 12px;
}

.info-card h4 {
    font-family: 'Quicksand', sans-serif;
    font-size: 1.15rem;
    color: var(--dark);
    margin-bottom: 6px;
}

.info-card p {
    color: var(--light-grey);
    font-size: 0.95rem;
}

.alert-success {
    background: #E2F6EA;
    color: var(--kids-green-dark);
    padding: 15px;
    border-radius: 15px;
    margin-bottom: 20px;
    font-weight: 600;
    text-align: center;
    border: 2px solid rgba(114, 209, 143, 0.2);
}

@media (max-width: 768px) {
    .contact-form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="contact-container">
    <h1 style="margin-bottom: 0.8rem; color: var(--dark); font-family: 'Quicksand', sans-serif; font-size: 2.5rem; text-align: center; position: relative;">
        Get in Touch
        <span style="display: block; width: 60px; height: 5px; background: var(--kids-pink); margin: 10px auto 0; border-radius: 10px;"></span>
    </h1>
    <p style="text-align: center; color: var(--light-grey); margin-bottom: 3rem; font-weight: 500;">We'd love to hear from you!</p>

    <div class="contact-card">
        <?php if($status == "success"): ?>
            <div class="alert-success">
                <i class="fas fa-check-circle" style="margin-right: 6px;"></i> Message Sent Successfully! Thank you.
            </div>
        <?php endif; ?>
        
        <form method="post" id="contactForm">
            <div class="contact-form-grid">
                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="name" required placeholder="Enter your full name">
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required placeholder="Enter your email address">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label>Message</label>
                <textarea name="message" required placeholder="Write your message here..." style="height: 150px; resize: none;"></textarea>
            </div>

            <button type="submit" name="send" class="btn-buy" style="width: 100%; border: none; padding: 15px; font-size: 1.1rem; border-radius: 50px;">
                Send Message <i class="fas fa-paper-plane" style="margin-left: 8px;"></i>
            </button>
        </form>
    </div>

    <!-- Additional Info Cards -->
    <div class="contact-info-grid">
        <div class="info-card">
            <i class="fas fa-user" style="color: var(--kids-blue);"></i>
            <h4>Contact Person</h4>
            <p>Anshul</p>
        </div>
        <div class="info-card">
            <i class="fas fa-phone-alt" style="color: var(--kids-green);"></i>
            <h4>Phone Number</h4>
            <p>+91 88499 19418</p>
        </div>
        <div class="info-card">
            <i class="fas fa-envelope" style="color: var(--kids-pink);"></i>
            <h4>Email Address</h4>
            <p>ahdave1573@gmail.com</p>
        </div>
        <div class="info-card">
            <i class="fas fa-map-marker-alt" style="color: var(--kids-orange);"></i>
            <h4>Store Location</h4>
            <p>Wonderland City, 560001</p>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

