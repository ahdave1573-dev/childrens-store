<?php
// order_success.php
require_once 'includes/header.php';

// Generate a random Order ID
$order_id = '#ORD-' . date('Y') . '-' . mt_rand(1000, 9999);
?>

<style>
.success-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 70vh;
    background-color: var(--background, #f9fafb);
    padding: 2rem;
    font-family: inherit;
    position: relative;
    overflow: hidden;
}

.success-card {
    background: #ffffff;
    max-width: 500px;
    width: 100%;
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    animation: slideUpFade 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
    transform: translateY(30px);
    position: relative;
    z-index: 10;
}

.icon-wrapper {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 90px;
    height: 90px;
    background-color: #dcfce7;
    border-radius: 50%;
    margin-bottom: 1.5rem;
    animation: iconPop 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) 0.3s forwards;
    transform: scale(0);
}

.icon-wrapper ion-icon {
    font-size: 4rem;
    color: #16a34a;
}

.success-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.success-message {
    font-size: 1.1rem;
    color: #4b5563;
    margin-bottom: 2rem;
    line-height: 1.5;
}

.order-id-box {
    background-color: #f3f4f6;
    padding: 1rem;
    border-radius: 8px;
    font-size: 1.3rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 2rem;
    border: 2px dashed #d1d5db;
    letter-spacing: 1px;
}

.btn-continue {
    display: inline-block;
    background-color: var(--primary, #4f46e5);
    color: #ffffff;
    padding: 1rem 2rem;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
    box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.1), 0 2px 4px -1px rgba(79, 70, 229, 0.06);
}

.btn-continue:hover {
    background-color: var(--primary-dark, #4338ca);
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.2), 0 4px 6px -2px rgba(79, 70, 229, 0.1);
}

.btn-continue:active {
    transform: translateY(0);
}

/* Animations */
@keyframes slideUpFade {
    0% {
        opacity: 0;
        transform: translateY(30px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes iconPop {
    0% {
        transform: scale(0);
    }
    80% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
    }
}

/* Confetti */
.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    opacity: 0;
    z-index: 1;
}
</style>

<div class="success-container">
    <div class="success-card">
        <div class="icon-wrapper">
            <ion-icon name="checkmark"></ion-icon>
        </div>
        <h1 class="success-title">Thank You!</h1>
        <p class="success-message">Your order has been placed successfully.</p>
        
        <div class="order-id-box">
            Order ID: <?php echo htmlspecialchars($order_id); ?>
        </div>
        
        <a href="index.php" class="btn-continue">Continue Shopping</a>
    </div>
</div>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<script>
// Lightweight dynamic confetti using Web Animations API
document.addEventListener('DOMContentLoaded', () => {
    const colors = ['#fca5a5', '#fcd34d', '#86efac', '#93c5fd', '#d8b4fe', '#fdba74'];
    const container = document.querySelector('.success-container');
    const confettiCount = 50;
    
    for (let i = 0; i < confettiCount; i++) {
        const confetti = document.createElement('div');
        confetti.classList.add('confetti');
        
        // Random style setup
        const color = colors[Math.floor(Math.random() * colors.length)];
        const left = Math.random() * 100;
        const size = Math.random() * 8 + 6;
        const delay = Math.random() * 0.5;
        const duration = Math.random() * 2 + 1.5;
        
        confetti.style.backgroundColor = color;
        confetti.style.left = `${left}%`;
        confetti.style.top = '-20px';
        
        // Mix it up between squares and circles
        if (Math.random() > 0.5) confetti.style.borderRadius = '50%';
        
        confetti.style.width = `${size}px`;
        confetti.style.height = `${size}px`;
        
        // Randomize the fall path
        const animateX = Math.random() * 200 - 100;
        const rotation = Math.random() * 720;
        
        const keyframes = [
            { transform: `translate3d(0,0,0) rotate(0deg)`, opacity: 1 },
            { transform: `translate3d(${animateX}px, 100vh, 0) rotate(${rotation}deg)`, opacity: 0 }
        ];
        
        const options = {
            duration: duration * 1000,
            delay: delay * 1000,
            easing: 'cubic-bezier(.37,0,.63,1)',
            fill: 'forwards'
        };
        
        container.appendChild(confetti);
        
        // Use WAAPI for smooth animation independent of main CSS
        const animation = confetti.animate(keyframes, options);
        animation.onfinish = () => confetti.remove();
    }
});
</script>

<?php
// Silently include footer if it exists
if (file_exists('includes/footer.php')) {
    require_once 'includes/footer.php'; 
}
?>
