document.addEventListener('DOMContentLoaded', () => {
    // --- Sticky Navigation ---
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // --- Hero Slider (Crossfade) ---
    const slides = document.querySelectorAll('.hero-slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        const slideInterval = 5000;

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(nextSlide, slideInterval);
    }

    // --- Scroll Reveal Animation ---
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target); // Animates only once
            }
        });
    }, {
        root: null,
        threshold: 0.15, // Trigger when 15% visible
        rootMargin: "0px"
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // --- Mobile Menu Toggle ---
    const mobileBtn = document.querySelector('.mobile-menu-btn');
    const navLinks = document.querySelector('.nav-links');

    if (mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            const isClosed = navLinks.style.display === 'none' || navLinks.style.display === '';
            navLinks.style.display = isClosed ? 'flex' : 'none';

            if (isClosed) {
                navLinks.style.flexDirection = 'column';
                navLinks.style.position = 'absolute';
                navLinks.style.top = '70px';
                navLinks.style.left = '0';
                navLinks.style.width = '100%';
                navLinks.style.background = 'white';
                navLinks.style.padding = '20px';
                navLinks.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
            }
        });
    }

    // --- Smooth Scroll ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// FIX: Ye missing function add kiya hai contact form success animation ke liye
function showSuccessAnimation(message) {
    // Create overlay
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.background = 'rgba(0,0,0,0.5)';
    overlay.style.zIndex = '9998';
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';

    // Create popup box
    const div = document.createElement('div');
    div.style.background = 'white';
    div.style.padding = '40px';
    div.style.borderRadius = '20px';
    div.style.boxShadow = '0 10px 40px rgba(0,0,0,0.2)';
    div.style.textAlign = 'center';
    div.style.animation = 'popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275)';

    // Keyframes for pop animation
    const styleSheet = document.createElement("style");
    styleSheet.innerText = `
        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }
    `;
    document.head.appendChild(styleSheet);

    div.innerHTML = `
        <div style="font-size: 4rem; color: #2ecc71; margin-bottom: 20px;">
            <ion-icon name="checkmark-circle"></ion-icon>
        </div>
        <h2 style="margin-bottom: 10px; color: #333;">Awesome!</h2>
        <p style="color: #666; font-size: 1.1rem;">${message}</p>
        <button id="closeSuccessBtn" style="margin-top:25px; padding:10px 30px; border:none; background:#6c5ce7; color:white; border-radius:50px; cursor:pointer; font-weight:bold; font-size:1rem; transition:0.3s;">
            Continue
        </button>
    `;

    overlay.appendChild(div);
    document.body.appendChild(overlay);

    // Close logic
    document.getElementById('closeSuccessBtn').addEventListener('click', () => {
        overlay.style.opacity = '0';
        overlay.style.transition = '0.3s';
        setTimeout(() => overlay.remove(), 300);
    });
}