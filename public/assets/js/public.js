document.addEventListener('DOMContentLoaded', function () {
    initNavToggle();
    initHeroSlider();
    initBackLinks();
});

// ปุ่ม "กลับหน้าก่อนหน้า" (เช่นหน้า 404) - ใช้ History กลับไปหน้าก่อน ถ้าไม่มีประวัติให้ไปหน้าแรกแทน
function initBackLinks() {
    document.querySelectorAll('.js-back-link').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();

            if (window.history.length > 1) {
                window.history.back();
            } else {
                window.location.href = link.getAttribute('data-fallback') || '/';
            }
        });
    });
}

// เปิด/ปิดเมนูหลักบนจอ Tablet/Mobile
function initNavToggle() {
    var toggleBtn = document.getElementById('navToggle');
    var nav = document.getElementById('mainNav');

    if (!toggleBtn || !nav) {
        return;
    }

    toggleBtn.addEventListener('click', function () {
        var isOpen = nav.classList.toggle('open');
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', function (event) {
        if (!nav.classList.contains('open')) {
            return;
        }

        if (!nav.contains(event.target) && !toggleBtn.contains(event.target)) {
            nav.classList.remove('open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    });
}

// Hero Slider แบบ Vanilla JS ล้วน (ไม่ใช้ Library ภายนอก) - เปลี่ยนภาพอัตโนมัติทุก 5 วินาที
function initHeroSlider() {
    var slider = document.getElementById('heroSlider');

    if (!slider) {
        return;
    }

    var slides = slider.querySelectorAll('.hero-slide');
    var dots = slider.querySelectorAll('.hero-dot');

    if (slides.length <= 1) {
        return;
    }

    var current = 0;
    var intervalId = null;

    function show(index) {
        slides[current].classList.remove('active');
        dots[current] && dots[current].classList.remove('active');

        current = (index + slides.length) % slides.length;

        slides[current].classList.add('active');
        dots[current] && dots[current].classList.add('active');
    }

    function start() {
        intervalId = window.setInterval(function () {
            show(current + 1);
        }, 5000);
    }

    function stop() {
        if (intervalId !== null) {
            window.clearInterval(intervalId);
            intervalId = null;
        }
    }

    dots.forEach(function (dot, index) {
        dot.addEventListener('click', function () {
            stop();
            show(index);
            start();
        });
    });

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', start);

    start();
}
